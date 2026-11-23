<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ], [
            'q.max' => 'Pencarian maksimal 120 karakter.',
        ]);

        return view('admin.users.index', [
            'users' => User::query()
                ->when($validated['q'] ?? null, function ($query, string $term): void {
                    $query->where(fn ($query) => $query
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%"));
                })
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $validated,
        ]);
    }

    public function store(Request $request, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Kata sandi admin minimal 12 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        DB::transaction(function () use ($validated, $admin, $auditLogger): void {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => UserRole::Admin,
                'aktif' => true,
            ]);
            $auditLogger->record(
                $admin,
                'admin_dibuat',
                'users',
                (int) $user->id,
                null,
                ['role' => UserRole::Admin->value, 'aktif' => true],
            );
        });

        return to_route('admin.users.index')->with('status', 'Akun admin berhasil dibuat.');
    }

    public function updateRole(
        Request $request,
        User $user,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $validated = $request->validate([
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ], [
            'role.in' => 'Peran pengguna tidak valid.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        abort_if($admin->is($user), 403, 'Anda tidak dapat mengubah peran akun sendiri.');

        DB::transaction(function () use ($admin, $user, $validated, $auditLogger): void {
            $activeAdmins = User::query()
                ->where('role', UserRole::Admin)
                ->where('aktif', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $previousRole = $lockedUser->role;

            if ($previousRole === UserRole::Admin && $validated['role'] !== UserRole::Admin->value) {
                abort_if($activeAdmins->count() <= 1, 422, 'Sistem harus memiliki minimal satu admin aktif.');
            }

            $lockedUser->update(['role' => $validated['role']]);
            $auditLogger->record(
                $admin,
                'peran_pengguna_diubah',
                'users',
                (int) $lockedUser->id,
                ['role' => $previousRole->value],
                ['role' => $validated['role']],
            );
        });

        return to_route('admin.users.index')->with('status', 'Peran pengguna berhasil diperbarui.');
    }

    public function toggleActive(
        Request $request,
        User $user,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        abort_if($admin->is($user), 403, 'Anda tidak dapat menonaktifkan akun sendiri.');
        DB::transaction(function () use ($admin, $user, $auditLogger): void {
            $activeAdmins = User::query()
                ->where('role', UserRole::Admin)
                ->where('aktif', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
            abort_if(
                $lockedUser->role === UserRole::Admin
                    && $lockedUser->aktif
                    && $activeAdmins->count() <= 1,
                422,
                'Sistem harus memiliki minimal satu admin aktif.',
            );

            $previous = $lockedUser->aktif;
            $lockedUser->update(['aktif' => ! $previous]);
            $auditLogger->record(
                $admin,
                $previous ? 'akun_dinonaktifkan' : 'akun_diaktifkan',
                'users',
                (int) $lockedUser->id,
                ['aktif' => $previous],
                ['aktif' => $lockedUser->aktif],
            );
        });

        return to_route('admin.users.index')->with('status', 'Status akun berhasil diperbarui.');
    }
}
