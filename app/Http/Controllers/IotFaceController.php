<?php

namespace App\Http\Controllers;

use App\Models\FaceEvent;
use Illuminate\Http\Request;

class IotFaceController extends Controller
{
    public function store(Request $request)
    {
        // validasi ringan (bisa ditambah sesuai kebutuhan)
        $data = $request->validate([
            'device_id'       => 'nullable|string|max:100',
            'nim'             => 'nullable|string|max:50',
            'predicted_label' => 'nullable|string|max:100',
            'confidence'      => 'nullable|numeric',
            'image_path'      => 'nullable|string|max:255',
            'raw_payload'     => 'nullable', 
        ]);

        // Kalau raw_payload dikirim sebagai array/string, kita simpan apa adanya
        if ($request->has('raw_payload')) {
            $data['raw_payload'] = $request->input('raw_payload');
        }

        $faceEvent = FaceEvent::create($data);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Data AI diterima dan disimpan',
            'id'      => $faceEvent->id,
        ]);
    }
}
