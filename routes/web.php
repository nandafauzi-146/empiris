<?php

use App\Http\Controllers\Account\ConsultationController as AccountConsultationController;
use App\Http\Controllers\Account\ConsultationReplyController;
use App\Http\Controllers\Account\OfferResponseController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Public\CalendarController as PublicCalendarController;
use App\Http\Controllers\Public\ChatbotPublicController;
use App\Http\Controllers\Public\ConsultationController;
use App\Http\Controllers\Public\DestinationController;
use App\Models\Project;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/tentang', 'pages.about')->name('about');

Route::view('/layanan', 'pages.services.index')->name('services.index');
Route::view('/layanan/{slug}', 'pages.services.show')
    ->whereIn('slug', ['tour-wisata', 'event-gathering', 'outbound', 'rental-armada', 'team-building'])
    ->name('services.show');

Route::get('/destinasi', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinasi/{slug}', [DestinationController::class, 'show'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('destinations.show');

Route::view('/paket/{slug}', 'pages.packages.show')
    ->whereIn('slug', ['jelajah-solo', 'pesona-dieng', 'eksplorasi-bali'])
    ->name('packages.show');
Route::view('/armada', 'pages.vehicles.index')->name('vehicles.index');

Route::view('/portofolio', 'pages.portfolios.index')->name('portfolios.index');
Route::view('/portofolio/{slug}', 'pages.portfolios.show')
    ->whereIn('slug', [
        'rapat-kerja-nasional-instansi',
        'gathering-tahunan-400-karyawan',
        'studi-lapangan-mahasiswa-arsitektur',
        'outbound-pegawai-dinas',
        'team-building-startup-teknologi',
        'wisuda-wisata-angkatan',
    ])
    ->name('portfolios.show');

Route::view('/klien', 'pages.clients.index')->name('clients.index');
Route::get('/konsultasi/baru', [ConsultationController::class, 'create'])
    ->name('consultation.create');
Route::get('/konsultasi', [ConsultationController::class, 'create'])
    ->name('consultation.legacy');
Route::post('/konsultasi', [ConsultationController::class, 'store'])
    ->middleware('throttle:konsultasi')
    ->name('consultation.store');
Route::view('/faq-kontak', 'pages.contact')->name('contact');
Route::get('/kalender', PublicCalendarController::class)->name('calendar.public');
Route::post('/api/chatbot', ChatbotPublicController::class)
    ->middleware('throttle:chatbot')
    ->name('chatbot.chat');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:register')
        ->name('register.store');

    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/lupa-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/lupa-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::post('/keluar', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/api/penawaran/{offer}/respons', [OfferResponseController::class, 'store'])
    ->middleware(['auth', 'verified', 'role:klien'])
    ->whereNumber('offer')
    ->name('api.offers.respond');

Route::get('/email/verifikasi', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verifikasi/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    $request->user()->claimGuestConsultations();

    return to_route('account.consultations.index')
        ->with('status', 'Email berhasil diverifikasi. Konsultasi Anda sudah ditautkan ke akun.');
})->middleware(['auth', 'signed', 'throttle:email-verification'])->name('verification.verify');

Route::post('/email/verifikasi/kirim-ulang', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return to_route('account.consultations.index');
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'Tautan verifikasi baru telah dikirim ke email Anda.');
})->middleware(['auth', 'throttle:email-verification'])->name('verification.send');

Route::prefix('akun')
    ->name('account.')
    ->middleware(['auth', 'verified', 'role:klien'])
    ->group(function () {
        Route::get('/proyek', function (Request $request) {
            $projects = Project::query()
                ->with('terms')
                ->where('id_klien', $request->user()->getKey())
                ->latest('tgl_mulai')
                ->get();

            return view('account.projects.index', ['projects' => $projects]);
        })->name('projects.index');
        Route::get('/profil', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/profil', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::get('/konsultasi/baru', [AccountConsultationController::class, 'create'])
            ->name('consultations.create');
        Route::post('/konsultasi/baru', [AccountConsultationController::class, 'store'])
            ->middleware('throttle:konsultasi')
            ->name('consultations.store');
        Route::get('/konsultasi', [AccountConsultationController::class, 'index'])
            ->name('consultations.index');
        Route::get('/konsultasi/{consultation}', [AccountConsultationController::class, 'show'])
            ->whereNumber('consultation')
            ->name('consultations.show');
        Route::post('/konsultasi/{consultation}/batal', [AccountConsultationController::class, 'cancel'])
            ->whereNumber('consultation')
            ->name('consultations.cancel');
        Route::post('/konsultasi/{consultation}/balasan', [ConsultationReplyController::class, 'store'])
            ->whereNumber('consultation')
            ->middleware('throttle:consultation-replies')
            ->name('consultations.replies.store');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)
            ->name('dashboard');
        Route::get('/konsultasi', [AdminConsultationController::class, 'index'])
            ->name('consultations.index');
        Route::get('/konsultasi/{consultation}', [AdminConsultationController::class, 'show'])
            ->whereNumber('consultation')
            ->name('consultations.show');
        Route::post('/konsultasi/{consultation}/balasan', [AdminConsultationController::class, 'storeReply'])
            ->whereNumber('consultation')
            ->middleware('throttle:consultation-replies')
            ->name('consultations.replies.store');
        Route::post('/konsultasi/{consultation}/penawaran', [AdminConsultationController::class, 'storeOffer'])
            ->whereNumber('consultation')
            ->name('consultations.offers.store');
        Route::post('/konsultasi/{consultation}/penawaran/{offer}/kirim', [AdminConsultationController::class, 'sendOffer'])
            ->whereNumber('consultation')
            ->whereNumber('offer')
            ->name('consultations.offers.send');
        Route::get('/proyek', [AdminProjectController::class, 'index'])->name('projects.index');
        Route::post('/proyek', [AdminProjectController::class, 'store'])->name('projects.store');
        Route::patch('/proyek/{project}/status', [AdminProjectController::class, 'updateStatus'])
            ->whereNumber('project')
            ->name('projects.status');
        Route::post('/termin/{term}/pembayaran', [AdminProjectController::class, 'recordPayment'])
            ->whereNumber('term')
            ->name('projects.payments.store');
        Route::get('/kalender', [AdminCalendarController::class, 'index'])->name('calendar.index');
        Route::post('/kalender/penanda', [AdminCalendarController::class, 'toggleMarker'])
            ->name('calendar.markers.toggle');
        Route::get('/konten', [AdminContentController::class, 'index'])->name('content.index');
        Route::post('/konten', [AdminContentController::class, 'store'])->name('content.store');
        Route::patch('/konten/{content}', [AdminContentController::class, 'update'])
            ->whereNumber('content')
            ->name('content.update');
        Route::delete('/konten/{content}', [AdminContentController::class, 'destroy'])
            ->whereNumber('content')
            ->name('content.destroy');
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/{report}/{format}', [AdminReportController::class, 'export'])
            ->whereIn('report', ['konsultasi', 'proyek', 'pembayaran'])
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('reports.export');
        Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/pengguna/admin', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/pengguna/{user}/peran', [AdminUserController::class, 'updateRole'])
            ->whereNumber('user')
            ->name('users.role');
        Route::patch('/pengguna/{user}/aktif', [AdminUserController::class, 'toggleActive'])
            ->whereNumber('user')
            ->name('users.active');
    });
