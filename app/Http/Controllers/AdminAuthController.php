<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $password = $credentials['password'];
        $expectedEmail = strtolower(trim((string) config('admin.email')));
        $expectedPassword = (string) config('admin.password');

        $emailOk = $expectedEmail !== '' && hash_equals($expectedEmail, $email);
        $passwordOk = $expectedPassword !== '' && $this->adminPasswordMatches($password, $expectedPassword);

        if ($emailOk && $passwordOk) {
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'بيانات الدخول غير صحيحة. تأكد أنك على /admin/login وليس صفحة دخول الأعضاء.',
            ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function adminPasswordMatches(string $password, string $expected): bool
    {
        if (preg_match('/^\$2[ayb]\$/', $expected) === 1) {
            return Hash::check($password, $expected);
        }

        return hash_equals($expected, $password);
    }
}
