<?php
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
Route::get('/', [HomeController::class, 'index'])->name('home');
// Placeholder routes referenced in Navbar and Footer
Route::get('/tentang', fn () => redirect()->route('home', ['#tentang']))->name('about');
Route::get('/layanan', fn () => redirect()->route('home', ['#layanan']))->name('services');
Route::get('/destinasi', fn () => redirect()->route('home', ['#destinasi']))->name('destinations');
Route::get('/konsultasi', fn () => redirect()->route('home', ['#konsultasi']))->name('consultation');
Route::get('/faq-kontak', fn () => redirect()->route('home', ['#kontak']))->name('contact');
// Authentication routes
Route::get('/masuk', fn () => view('auth.login'))->name('login');
Route::get('/daftar', fn () => view('auth.register'))->name('register');