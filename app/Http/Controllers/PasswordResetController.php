<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem.']);
        }

        // Generate token
        $token = Str::random(64);
        
        // Simpan token ke database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Kirim email
        try {
            Mail::send('emails.reset-password', [
                'token' => $token, 
                'user' => $user,
                'resetUrl' => config('app.url') . '/reset-password/' . $token
            ], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset Password - Sistem Presensi Siswa');
            });

            return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    public function showResetForm(Request $request, $token)
    {
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$reset) {
            return redirect()->route('login')->withErrors(['email' => 'Link reset password tidak valid atau sudah kadaluarsa.']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $reset->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Link reset password tidak valid atau sudah kadaluarsa.']);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
    }
} 