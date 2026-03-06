<?php

namespace App\Exports;

use App\Models\Pertemuan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;



class PertemuanDetailExport implements FromCollection, WithHeadings, WithEvents, WithDrawings, ShouldAutoSize, WithCustomStartCell
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    protected function buildQuery()
    {
        $query = Pertemuan::with([
            'kelas.dosen',
            'kelas.mataKuliah',
            'kehadiran.mahasiswa',
        ]);

        if (!empty($this->filters['dosen_id'])) {
            $query->where('dosen_id', $this->filters['dosen_id']);
        }

        if (!empty($this->filters['mata_kuliah_id'])) {
            $query->where('mata_kuliah_id', $this->filters['mata_kuliah_id']);
        }

        if (!empty($this->filters['golongan'])) {
            $gol = $this->filters['golongan'];
            $query->whereHas('kelas', function ($q) use ($gol) {
                $q->where('golongan', $gol);
            });
        }

        if (!empty($this->filters['minggu_ke'])) {
            $query->where('minggu_ke', $this->filters['minggu_ke']);
        }

        if (!empty($this->filters['acara_ke'])) {
            $query->where('acara_ke', $this->filters['acara_ke']);
        }

        return $query->orderBy('tanggal')
                     ->orderBy('jam_mulai');
    }

    public function collection(): Collection
    {
        $pertemuanList = $this->buildQuery()->get();
        $rows = [];

        foreach ($pertemuanList as $p) {
            $firstRow = true;

            foreach ($p->kehadiran as $kh) {
                $rows[] = [
                    'tanggal'     => $firstRow ? $p->tanggal : '',
                    'jam_mulai'   => $firstRow ? $p->jam_mulai : '',
                    'jam_selesai' => $firstRow ? $p->jam_selesai : '',
                    'dosen'       => $firstRow ? ($p->kelas->dosen->nama_dosen ?? '-') : '',
                    'mata_kuliah' => $firstRow ? ($p->kelas->mataKuliah->nama_matkul ?? '-') : '',
                    'golongan'    => $firstRow ? ($p->kelas->golongan ?? '-') : '',
                    'minggu_ke'   => $firstRow ? $p->minggu_ke : '',
                    'acara_ke'    => $firstRow ? $p->acara_ke : '',
                    'nim'         => $kh->mahasiswa->nim ?? '-',
                    'nama_mhs'    => $kh->mahasiswa->nama ?? '-',
                    'status'      => $kh->status == 1 ? 'Hadir' : 'Tidak hadir',
                    'poin_fokus'  => $kh->poin_fokus,
                ];

                $firstRow = false;
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Dosen',
            'Mata Kuliah',
            'Golongan',
            'Minggu ke-',
            'Acara ke-',
            'NIM',
            'Nama Mahasiswa',
            'Status',
            'Poin Fokus',
        ];
    }

    // === bikin heading mulai dari baris 7 ===
    public function startCell(): string
    {
        return 'A7'; // baris 7: heading, data mulai baris 8
    }

    // === KOP + STYLING (tanpa insert row lagi) ===
    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // ============ PAGE SETUP ============
            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            $margins = $sheet->getPageMargins();
            $margins->setTop(0.5);
            $margins->setBottom(0.5);
            $margins->setLeft(0.5);
            $margins->setRight(0.5);

            // ============ KOP SURAT ============
            $sheet->mergeCells('B2:K2');
            $sheet->mergeCells('B3:K3');
            $sheet->mergeCells('B4:K4');
            $sheet->mergeCells('B5:K5');

            $sheet->setCellValue('B2', 'POLITEKNIK NEGERI JEMBER');
            $sheet->setCellValue('B3', 'JURUSAN TEKNOLOGI INFORMASI - PROGRAM STUDI TEKNIK INFORMATIKA');
            $sheet->setCellValue('B4', 'LAPORAN PRESENSI & MONITORING FOKUS MAHASISWA');
            $sheet->setCellValue('B5', 'Tahun Akademik 2025/2026');

            $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(13);
            $sheet->getStyle('B5')->getFont()->setBold(true)->setSize(12);

            $sheet->getStyle('B2:B5')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getRowDimension(3)->setRowHeight(20);
            $sheet->getRowDimension(4)->setRowHeight(20);
            $sheet->getRowDimension(5)->setRowHeight(18);

            // GARIS BAWAH KOP (double line)
            $sheet->getStyle('A5:L5')->getBorders()->getBottom()
                  ->setBorderStyle(Border::BORDER_MEDIUM);
            $sheet->getStyle('A6:L6')->getBorders()->getBottom()
                  ->setBorderStyle(Border::BORDER_THIN);

            // ============ HEADER & DATA ============
            $headerRow    = 7;                           // baris judul kolom
            $dataStartRow = $headerRow + 1;              // baris pertama data
            $lastRow      = $sheet->getHighestRow();     // baris terakhir

            // Header (A–L) bold + center + border
            $sheet->getStyle("A{$headerRow}:L{$headerRow}")
                ->getFont()->setBold(true);
            $sheet->getStyle("A{$headerRow}:L{$headerRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$headerRow}:L{$headerRow}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Freeze pane di baris setelah header
            $sheet->freezePane("A" . ($headerRow + 1));

            // Kalau belum ada data, stop di sini
            if ($lastRow < $dataStartRow) {
                return;
            }

            // === GRID PENUH UNTUK NIM–POIN (I–L) ===
            $sheet->getStyle("I{$dataStartRow}:L{$lastRow}")
                ->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            // === BLOK TANGGAL–ACARA KE (A–H) ===
            // Outline dulu (batas luar)
            $sheet->getStyle("A{$dataStartRow}:H{$lastRow}")
                ->getBorders()->getOutline()
                ->setBorderStyle(Border::BORDER_THIN);

            // Garis vertikal setiap kolom A–H (biar lurus dengan header)
            foreach (range('A', 'H') as $col) {
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastRow}")
                    ->getBorders()->getLeft()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            // Pastikan batas kanan di H juga ada
            $sheet->getStyle("H{$dataStartRow}:H{$lastRow}")
                ->getBorders()->getRight()
                ->setBorderStyle(Border::BORDER_THIN);

            // === PEMBATAS ANTAR PERTEMUAN (GARIS TEBAL) ===
            for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                $currentDate = $sheet->getCell("A{$row}")->getValue();
                $nextDate    = $row < $lastRow
                    ? $sheet->getCell("A" . ($row + 1))->getValue()
                    : null;

                $isLastRow = ($row === $lastRow);

                // akhir pertemuan jika:
                // - ini baris terakhir, atau
                // - baris ini kosong tanggal & baris berikutnya ada tanggal (mulai pertemuan baru), atau
                // - baris ini ada tanggal & baris berikutnya juga ada tanggal tapi beda (pertemuan 1 baris)
                if (
                    $isLastRow ||
                    ((empty($currentDate)) && !empty($nextDate)) ||
                    (!empty($currentDate) && !empty($nextDate) && $currentDate != $nextDate)
                ) {
                    $sheet->getStyle("A{$row}:L{$row}")
                        ->getBorders()->getBottom()
                        ->setBorderStyle(Border::BORDER_MEDIUM);
                }
            }
        },
    ];
}

    // === LOGO KIRI & KANAN (tidak ikut geser lagi) ===
    public function drawings()
    {
        $drawings = [];

        // Logo kiri
        if (file_exists(public_path('images/polije.png'))) {
            $logoLeft = new Drawing();
            $logoLeft->setName('Logo Kiri');
            $logoLeft->setDescription('Logo kiri');
            $logoLeft->setPath(public_path('images/polije.png'));
            $logoLeft->setHeight(110);
            $logoLeft->setCoordinates('A2');   // tetap di A2
            $drawings[] = $logoLeft;
        }

        // Logo kanan
        if (file_exists(public_path('images/attend.png'))) {
            $logoRight = new Drawing();
            $logoRight->setName('Logo Kanan');
            $logoRight->setDescription('Logo kanan');
            $logoRight->setPath(public_path('images/attend.png'));
            $logoRight->setHeight(110);
            $logoRight->setCoordinates('L2');  // di kanan kop
            $drawings[] = $logoRight;
        }

        return $drawings;
    }
}
