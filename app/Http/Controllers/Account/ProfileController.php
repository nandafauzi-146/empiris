<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $request->validated();
        $emailChanged = $user->email !== $profile['email'];

        $user->fill($profile);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return to_route('verification.notice')
                ->with('status', 'Email profil diperbarui. Verifikasi alamat email baru Anda.');
        }

        return to_route('account.profile.edit')
            ->with('status', 'Profil berhasil diperbarui.');
    }
}
