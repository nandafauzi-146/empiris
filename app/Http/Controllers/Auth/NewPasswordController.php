<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->safe()->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PasswordReset) {
            return to_route('login')->with('status', 'Kata sandi berhasil diubah. Silakan masuk.');
        }

        return back()->withErrors([
            'email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.',
        ]);
    }
}
