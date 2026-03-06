<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    // Tampilkan form lupa password
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Kirim email ke admin (attendiouye@gmail.com)
    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $userEmail = $data['email'];

        try {
            // Kirim email sederhana ke admin
            Mail::raw(
                "Ada permintaan lupa password untuk akun dengan email: {$userEmail}\n\n" .
                "Waktu permintaan : " . now()->format('d-m-Y H:i') . "\n\n" .
                "Silakan cek dan lakukan reset password secara manual di sistem.",
                function ($message) {
                    $message->to('attendiouye@gmail.com')
                            ->subject('Permintaan Lupa Password - ATTEND-IO');
                }
            );

            return back()->with(
                'status',
                'Permintaan lupa password sudah dikirim ke admin. Silakan tunggu konfirmasi.'
            );
        } catch (\Throwable $e) {
            // kalau email gagal, jangan 500 error, kasih pesan rapi
            \Log::error('Gagal kirim email lupa password: '.$e->getMessage());

            return back()->withErrors([
                'email' => 'Gagal mengirim email ke admin. Coba lagi beberapa saat, atau hubungi admin secara langsung.',
            ]);
        }
    }
}
