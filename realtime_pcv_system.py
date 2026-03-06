# realtime_pcv_system.py

import cv2
import face_recognition
import numpy as np
import pickle
import time
import requests
import sys

# ===================== CONFIG =====================

# Path model encodings wajah
MODEL_PATH = r"C:\ATTEND-IO\pcv_trained_face_model.pkl"

# Base URL Laravel (sesuai cara kamu akses di browser)
API_BASE = "http://127.0.0.1:8000/api"

# Endpoint untuk poin fokus / hadir
FOCUS_EVENT_URL = f"{API_BASE}/attendio/focus-event"

# Endpoint untuk cek status pertemuan
# GET /api/attendio/pertemuan-status/{pertemuan_id}
PERTEMUAN_STATUS_URL_TEMPLATE = f"{API_BASE}/attendio/pertemuan-status/{{pertemuan_id}}"


ESP_IP = "172.20.10.3"  # TODO: 
ESP_DOOR_URL = f"http://{ESP_IP}/open-door" if ESP_IP else None

# Threshold confidence untuk menganggap wajah dikenal
CONFIDENCE_THRESHOLD = 0.6

# Kalau "tidak_fokus" berturut-turut selama detik ini => kirim 1 poin
NOT_FOCUS_SECONDS = 10.0

# Kalau wajah dikenali stabil selama detik ini => set hadir + buka pintu
ATTENDANCE_CONFIRM_SECONDS = 5.0

# Cek status pertemuan ke Laravel setiap berapa detik
CHECK_STATUS_INTERVAL = 5.0

# ==================================================


class PCVRealTimeSystem:
    def __init__(self,
                 model_path=MODEL_PATH,
                 confidence_threshold=CONFIDENCE_THRESHOLD,
                 pertemuan_id=None):
        self.model_path = model_path
        self.confidence_threshold = confidence_threshold
        self.pertemuan_id = pertemuan_id

        # load model encodings
        self.known_face_encodings = []
        self.known_face_names = []
        self.load_model(model_path)

        # eye cascade untuk deteksi fokus
        self.eye_cascade = cv2.CascadeClassifier(
            cv2.data.haarcascades + 'haarcascade_eye.xml'
        )

        # tracking waktu tidak fokus per orang (untuk poin_fokus)
        # name -> timestamp mulai "tidak_fokus"
        self.not_focus_start = {}

        # tracking waktu hadir stabil (5 detik) per orang
        # name -> timestamp pertama kali mulai terdeteksi stabil
        self.attendance_timers = {}
        # siapa saja yang sudah pernah ditandai hadir (biar tidak dobel)
        self.attendance_confirmed = set()

        # statistik
        self.processing_times = []
        self.last_status_check = 0.0

        # URL API
        self.focus_event_url = FOCUS_EVENT_URL
        self.pertemuan_status_url = (
            PERTEMUAN_STATUS_URL_TEMPLATE.format(pertemuan_id=self.pertemuan_id)
            if self.pertemuan_id else None
        )

        # ESP door
        self.esp_door_url = ESP_DOOR_URL

        print("=== PCV Real-Time System ===")
        print(f"Model path      : {self.model_path}")
        print(f"Encodings loaded: {len(self.known_face_encodings)} wajah")
        print(f"Pertemuan ID    : {self.pertemuan_id}")
        print(f"API Fokus       : {self.focus_event_url}")
        if self.pertemuan_status_url:
            print(f"API Status Pert.: {self.pertemuan_status_url}")
        if self.esp_door_url:
            print(f"ESP Door URL    : {self.esp_door_url}")

    # --------------------------------------------------
    #  MODEL
    # --------------------------------------------------
    def load_model(self, model_path):
        try:
            with open(model_path, 'rb') as f:
                data = pickle.load(f)

            self.known_face_encodings = data.get("encodings", [])
            self.known_face_names = data.get("names", [])
            print(" Model encodings loaded successfully!")
        except Exception as e:
            print(f" Error loading model encodings: {e}")
            self.known_face_encodings = []
            self.known_face_names = []

    # --------------------------------------------------
    #  PREPROCESSING 
    # --------------------------------------------------
    def apply_pcv_preprocessing_realtime(self, frame_bgr):
       
        start = time.time()

        # 1. BGR -> YCrCb
        ycrcb = cv2.cvtColor(frame_bgr, cv2.COLOR_BGR2YCrCb)
        y, cr, cb = cv2.split(ycrcb)

        # 2. CLAHE di luminance
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
        y_clahe = clahe.apply(y)
        ycrcb_clahe = cv2.merge([y_clahe, cr, cb])

        # 3. Bilateral filter (untuk haluskan noise tapi edge masih terjaga)
        filtered = cv2.bilateralFilter(ycrcb_clahe, 9, 75, 75)

        # 4. Blur score (pakai channel Y asli)
        blur_score = cv2.Laplacian(y, cv2.CV_64F).var()

        # 5. Back to BGR
        processed_bgr = cv2.cvtColor(filtered, cv2.COLOR_YCrCb2BGR)

        proc_time = time.time() - start
        self.processing_times.append(proc_time)

        return processed_bgr, blur_score, proc_time

    # --------------------------------------------------
    #  FACE RECOGNITION
    # --------------------------------------------------
    def recognize_faces(self, processed_bgr):
        """
        processed_bgr: frame BGR setelah PCV preprocessing
        return: face_locations, face_names, face_confidences
        """
        rgb = cv2.cvtColor(processed_bgr, cv2.COLOR_BGR2RGB)

        face_locations = face_recognition.face_locations(rgb)
        face_encodings = face_recognition.face_encodings(rgb, face_locations)

        face_names = []
        face_confidences = []

        for encoding in face_encodings:
            if not self.known_face_encodings:
                face_names.append("Unknown")
                face_confidences.append(0.0)
                continue

            # bandingkan dengan encodings known
            distances = face_recognition.face_distance(self.known_face_encodings, encoding)
            best_idx = np.argmin(distances)
            confidence = 1.0 - distances[best_idx]  # makin dekat jaraknya, makin tinggi confidencenya

            if confidence >= self.confidence_threshold:
                name = self.known_face_names[best_idx]
            else:
                name = "Unknown"

            face_names.append(name)
            face_confidences.append(float(confidence))

        return face_locations, face_names, face_confidences

    # --------------------------------------------------
    #  FOCUS / TIDAK FOKUS (deteksi mata)
    # --------------------------------------------------
    def classify_focus(self, face_roi_bgr):
        """
        face_roi_bgr: crop wajah BGR
        return: focus_status ("fokus" / "tidak_fokus"), score, num_eyes
        """
        gray = cv2.cvtColor(face_roi_bgr, cv2.COLOR_BGR2GRAY)
        eyes = self.eye_cascade.detectMultiScale(gray, 1.1, 4)

        num_eyes = len(eyes)
        if num_eyes >= 2:
            status = "fokus"
            score = 85
        elif num_eyes == 1:
            status = "tidak_fokus"  
            score = 60
        else:
            status = "tidak_fokus"
            score = 30

        return status, score, num_eyes

    # --------------------------------------------------
    #  API: KIRIM POIN FOKUS / HADIR
    # --------------------------------------------------
    def send_focus_event(self, name, focus_status="tidak_fokus", points=1):
        if not self.pertemuan_id:
            return

        payload = {
            "pertemuan_id": self.pertemuan_id,
            "nama": name,
            "poin": points,
            "focus_status": focus_status,
        }

        try:
            resp = requests.post(self.focus_event_url, json=payload, timeout=2)
            print(f"[API] POST {self.focus_event_url} -> {resp.status_code}")
            try:
                print("[API] Response:", resp.json())
            except Exception:
                print("[API] Response (raw):", resp.text)
        except Exception as e:
            print(f"[API] Error send_focus_event: {e}")

    # --------------------------------------------------
    #  API: POIN FOKUS (10 detik tidak fokus)
    # --------------------------------------------------
    def update_focus_points(self, name, focus_status):
        now = time.time()

        if name == "Unknown":
            if name in self.not_focus_start:
                del self.not_focus_start[name]
            return

        if focus_status == "tidak_fokus":
            start = self.not_focus_start.get(name)
            if start is None:
                # baru mulai tidak fokus
                self.not_focus_start[name] = now
            else:
                elapsed = now - start
                if elapsed >= NOT_FOCUS_SECONDS:
                    print(f"[FOCUS] {name} tidak fokus {elapsed:.1f}s -> kirim poin")
                    self.send_focus_event(name, focus_status="tidak_fokus", points=1)
                    # reset supaya kalau terus tidak fokus, tiap 10s dapat 1 poin lagi
                    self.not_focus_start[name] = now
        else:
            # kembali fokus -> reset timer
            if name in self.not_focus_start:
                print(f"[FOCUS] {name} kembali fokus, reset timer.")
                del self.not_focus_start[name]

    # --------------------------------------------------
    #  API: ATTENDANCE (5 detik wajah stabil + buka pintu)
    # --------------------------------------------------
    def notify_esp_open_door(self, name):
        if not self.esp_door_url:
            return
        try:
            resp = requests.post(self.esp_door_url, timeout=2)
            print(f"[ESP] POST {self.esp_door_url} -> {resp.status_code}")
        except Exception as e:
            print(f"[ESP] Error notify_esp_open_door for {name}: {e}")

    def mark_attendance(self, name):
        """
        Tandai hadir di Laravel + buka pintu via ESP (sekali saja per mahasiswa).
        """
        if name == "Unknown":
            return
        if name in self.attendance_confirmed:
            return

        print(f"[ATTEND] Konfirmasi hadir via wajah untuk: {name}")

        # 1) Tanda hadir di DB (poin=0, tapi status=1 di Laravel)
        self.send_focus_event(name, focus_status="hadir", points=0)

        # 2) Buka pintu via ESP
        self.notify_esp_open_door(name)

        # 3) tandai bahwa sudah pernah hadir
        self.attendance_confirmed.add(name)
        if name in self.attendance_timers:
            del self.attendance_timers[name]

    def update_attendance(self, names_in_frame):
        """
        Logika:
        - Jika sebuah nama (bukan 'Unknown') muncul terus di frame selama
          ATTENDANCE_CONFIRM_SECONDS (default 5 detik),
          maka dianggap hadir dan dipanggil mark_attendance(name).
        - Kalau hilang dari frame sebelum 5 detik, timernya di-reset.
        """
        now = time.time()

        # update / start timer untuk nama yang lagi kelihatan
        for name in names_in_frame:
            if name == "Unknown":
                continue
            if name in self.attendance_confirmed:
                continue

            if name not in self.attendance_timers:
                self.attendance_timers[name] = now
                print(f"[ATTEND] Mulai hitung hadir untuk {name}")
            else:
                elapsed = now - self.attendance_timers[name]
                if elapsed >= ATTENDANCE_CONFIRM_SECONDS:
                    self.mark_attendance(name)

        # reset timer untuk nama yang tidak kelihatan lagi
        for tracked in list(self.attendance_timers.keys()):
            if tracked not in names_in_frame:
                print(f"[ATTEND] {tracked} hilang dari frame, reset timer hadir.")
                del self.attendance_timers[tracked]

    # --------------------------------------------------
    #  API: CEK STATUS PERTEMUAN
    # --------------------------------------------------
    def should_stop(self):
        """
        Return True kalau pertemuan BUKAN 'ongoing'.
        Kalau API status belum dibuat / error, return False.
        """
        if not self.pertemuan_status_url:
            return False

        try:
            resp = requests.get(self.pertemuan_status_url, timeout=2)
            if resp.status_code != 200:
                return False

            data = resp.json()
            status = data.get("status", "ongoing")
            if status != "ongoing":
                print(f"[STATUS] Pertemuan status = {status}, stop realtime system.")
                return True
        except Exception as e:
            print(f"[STATUS] Gagal cek status pertemuan: {e}")
        return False

    # --------------------------------------------------
    #  DRAWING
    # --------------------------------------------------
    def draw_pcv_info(self, frame, blur_score, proc_time):
        h, w = frame.shape[:2]
        x, y = 10, 25
        lh = 22

        cv2.putText(frame, "PCV PREPROCESSING:",
                    (x, y), cv2.FONT_HERSHEY_SIMPLEX, 0.6,
                    (255, 255, 255), 2)

        color_blur = (0, 255, 0) if blur_score > 50 else (0, 0, 255)
        cv2.putText(frame, f"Blur Score: {blur_score:.1f}",
                    (x, y + lh), cv2.FONT_HERSHEY_SIMPLEX, 0.5,
                    color_blur, 1)

        cv2.putText(frame, f"Proc Time: {proc_time*1000:.1f} ms",
                    (x, y + 2*lh), cv2.FONT_HERSHEY_SIMPLEX, 0.5,
                    (255, 255, 0), 1)

        fps = 1.0 / proc_time if proc_time > 0 else 0.0
        cv2.putText(frame, f"FPS (approx): {fps:.1f}",
                    (x, y + 3*lh), cv2.FONT_HERSHEY_SIMPLEX, 0.5,
                    (255, 255, 0), 1)

    def draw_face_info(self, frame, face_locations, face_names, face_confidences,
                       focus_statuses, focus_scores):
        """
        Gambar bbox + nama + confidence + status fokus di frame.
        """
        for (top, right, bottom, left), name, conf, f_status, f_score in zip(
            face_locations, face_names, face_confidences, focus_statuses, focus_scores
        ):
            # pastikan dalam bounds
            h, w = frame.shape[:2]
            top = max(0, top)
            left = max(0, left)
            bottom = min(h, bottom)
            right = min(w, right)
    
            # warna bbox
            if f_status == "fokus":
                color = (0, 255, 0)
            else:
                color = (0, 0, 255)

            cv2.rectangle(frame, (left, top), (right, bottom), color, 2)

            # label nama + confidence
            label = name
            if name != "Unknown":
                label += f" ({conf:.2f})"

            cv2.rectangle(frame, (left, bottom - 40), (right, bottom),
                          color, cv2.FILLED)
            cv2.putText(frame, label, (left + 5, bottom - 20),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.45,
                        (255, 255, 255), 1)

            cv2.putText(frame, f"{f_status.upper()} ({f_score})",
                        (left + 5, bottom - 5),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.45,
                        (255, 255, 255), 1)

    # --------------------------------------------------
    #  MAIN LOOP
    # --------------------------------------------------
    def run_realtime(self):
        cap = cv2.VideoCapture(0)  

        if not cap.isOpened():
            print("❌ Tidak bisa akses kamera.")
            return

        print("🚀 Start PCV Real-Time. Tekan 'q' untuk keluar.")

        while True:
            # cek status pertemuan berkala
            now = time.time()
            if self.pertemuan_id and self.pertemuan_status_url:
                if now - self.last_status_check > CHECK_STATUS_INTERVAL:
                    self.last_status_check = now
                    if self.should_stop():
                        break

            ret, frame = cap.read()
            if not ret:
                print("❌ Gagal baca frame.")
                break

            # mirror biar kaya kaca
            frame = cv2.flip(frame, 1)

            # PCV preprocessing
            processed_bgr, blur_score, proc_time = self.apply_pcv_preprocessing_realtime(frame)

            # face recognition
            face_locations, face_names, face_confs = self.recognize_faces(processed_bgr)

            # === LOGIKA HADIR DENGAN DELAY 5 DETIK ===
            names_in_frame = set(face_names)
            self.update_attendance(names_in_frame)

            focus_statuses = []
            focus_scores = []

            # untuk tiap wajah, hitung fokus/tidak_fokus + update poin
            for (top, right, bottom, left), name in zip(face_locations, face_names):
                # crop wajah
                face_roi = frame[top:bottom, left:right]
                if face_roi.size == 0:
                    focus_statuses.append("tidak_fokus")
                    focus_scores.append(0)
                    continue

                f_status, f_score, num_eyes = self.classify_focus(face_roi)
                focus_statuses.append(f_status)
                focus_scores.append(f_score)

                # update poin fokus/tidak_fokus per orang
                self.update_focus_points(name, f_status)

            # gambar info di frame asli
            self.draw_face_info(frame, face_locations, face_names,
                                face_confs, focus_statuses, focus_scores)
            self.draw_pcv_info(frame, blur_score, proc_time)

            # info jumlah wajah
            cv2.putText(frame, f"Wajah: {len(face_locations)}",
                        (10, frame.shape[0] - 10),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.6,
                        (255, 255, 255), 2)

            cv2.imshow("PCV Face Recognition + Focus", frame)

            key = cv2.waitKey(1) & 0xFF
            if key == ord('q'):
                print("👉 Tombol 'q' ditekan, keluar.")
                break

        cap.release()
        cv2.destroyAllWindows()

        if self.processing_times:
            avg = np.mean(self.processing_times)
            print("\n=== STATISTIK ===")
            print(f"Rata-rata waktu PCV : {avg*1000:.1f} ms")
            print(f"Perkiraan FPS       : {1.0/avg:.1f}")
            print(f"Total frame         : {len(self.processing_times)}")


def main():
    # Baca pertemuan_id dari argumen CLI
    pertemuan_id = None
    if len(sys.argv) >= 2:
        try:
            pertemuan_id = int(sys.argv[1])
            print(f"[ARGS] pertemuan_id dari argumen = {pertemuan_id}")
        except ValueError:
            print(f"[ARGS] Argumen pertama bukan integer: {sys.argv[1]} (diabaikan)")

    system = PCVRealTimeSystem(
        model_path=MODEL_PATH,
        confidence_threshold=CONFIDENCE_THRESHOLD,
        pertemuan_id=pertemuan_id
    )
    system.run_realtime()


if __name__ == "__main__":
    main()
