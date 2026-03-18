<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CmsVehicleController;
use App\Http\Controllers\CmsTourController;
use App\Http\Controllers\CmsZoneController;
use App\Http\Controllers\CmsCarouselController;
use App\Http\Controllers\CmsGalleryController;
use App\Http\Controllers\CmsSectionImagesController;
use App\Http\Controllers\CmsSettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Booking + Contact API (AJAX from wizard)
Route::post('/api/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::post('/api/contact', [ContactController::class, 'send'])->name('contact.store');

// ── Booking lookup & voucher resend (auth required: expone PII / permite spam de email)
Route::middleware('auth')->group(function () {
    Route::get('/api/bookings/{orderNumber}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/api/bookings/{orderNumber}/resend', [BookingController::class, 'resendVoucher'])->name('bookings.resend');
});

// ── Auth (login/logout)
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/admin');
    }
    return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
})->name('login.post')->middleware(['guest', 'throttle:5,1']);

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

// ── Admin Panel
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/',                              [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings',                      [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}',            [AdminController::class, 'bookingShow'])->name('booking.show');
    Route::get('/bookings/{booking}/pdf',        [BookingController::class, 'downloadPdf'])->name('booking.pdf');
    Route::put('/bookings/{booking}',            [AdminController::class, 'bookingUpdate'])->name('booking.update');
    Route::delete('/bookings/{booking}',         [AdminController::class, 'bookingDestroy'])->name('booking.destroy');
    Route::get('/contacts',                      [AdminController::class, 'contacts'])->name('contacts');
    Route::delete('/contacts/{contact}',         [AdminController::class, 'contactDestroy'])->name('contact.destroy');
    Route::get('/profile',                       [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile',                       [AdminController::class, 'profileUpdate'])->name('profile.update');
    Route::put('/profile/password',              [AdminController::class, 'passwordUpdate'])->name('profile.password');

    // ── CMS
    Route::prefix('cms')->name('cms.')->group(function () {
        // Vehicles
        Route::get('/vehicles',               [CmsVehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/create',        [CmsVehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles',              [CmsVehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit',[CmsVehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{vehicle}',     [CmsVehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}',  [CmsVehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::post('/vehicles/{vehicle}/toggle', [CmsVehicleController::class, 'toggle'])->name('vehicles.toggle');

        // Tours
        Route::get('/tours',               [CmsTourController::class, 'index'])->name('tours.index');
        Route::get('/tours/create',        [CmsTourController::class, 'create'])->name('tours.create');
        Route::post('/tours',              [CmsTourController::class, 'store'])->name('tours.store');
        Route::get('/tours/{tour}/edit',   [CmsTourController::class, 'edit'])->name('tours.edit');
        Route::put('/tours/{tour}',        [CmsTourController::class, 'update'])->name('tours.update');
        Route::delete('/tours/{tour}',     [CmsTourController::class, 'destroy'])->name('tours.destroy');
        Route::post('/tours/{tour}/toggle',[CmsTourController::class, 'toggle'])->name('tours.toggle');

        // Zones & Hotels
        Route::get('/zones',                       [CmsZoneController::class, 'index'])->name('zones.index');
        Route::post('/zones',                      [CmsZoneController::class, 'store'])->name('zones.store');
        Route::put('/zones/{zone}',                [CmsZoneController::class, 'update'])->name('zones.update');
        Route::post('/zones/{zone}/images',        [CmsZoneController::class, 'updateImages'])->name('zones.images');
        Route::delete('/zones/{zone}',             [CmsZoneController::class, 'destroy'])->name('zones.destroy');
        Route::post('/zones/{zone}/hotels',        [CmsZoneController::class, 'storeHotel'])->name('zones.hotels.store');
        Route::delete('/hotels/{hotel}',           [CmsZoneController::class, 'destroyHotel'])->name('hotels.destroy');
        Route::post('/hotels/{hotel}/toggle',      [CmsZoneController::class, 'toggleHotel'])->name('hotels.toggle');

        // Carousel
        Route::get('/carousel',                    [CmsCarouselController::class, 'index'])->name('carousel.index');
        Route::get('/carousel/create',             [CmsCarouselController::class, 'create'])->name('carousel.create');
        Route::post('/carousel',                   [CmsCarouselController::class, 'store'])->name('carousel.store');
        Route::get('/carousel/{slide}/edit',       [CmsCarouselController::class, 'edit'])->name('carousel.edit');
        Route::post('/carousel/{slide}',           [CmsCarouselController::class, 'update'])->name('carousel.update');
        Route::delete('/carousel/{slide}',         [CmsCarouselController::class, 'destroy'])->name('carousel.destroy');
        Route::post('/carousel/{slide}/toggle',    [CmsCarouselController::class, 'toggle'])->name('carousel.toggle');

        // Gallery
        Route::get('/gallery',                     [CmsGalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery',                    [CmsGalleryController::class, 'store'])->name('gallery.store');
        Route::post('/gallery/{image}',            [CmsGalleryController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{image}',          [CmsGalleryController::class, 'destroy'])->name('gallery.destroy');
        Route::post('/gallery/{image}/toggle',     [CmsGalleryController::class, 'toggle'])->name('gallery.toggle');

        // Section Images (Acerca de Nosotros & Servicio Aeropuerto)
        Route::get('/section-images',                           [CmsSectionImagesController::class, 'index'])->name('section-images.index');
        Route::post('/section-images/{slot}',                   [CmsSectionImagesController::class, 'update'])->name('section-images.update');
        Route::post('/section-images/{slot}/restore',           [CmsSectionImagesController::class, 'restore'])->name('section-images.restore');

        // Settings
        Route::get('/settings',                    [CmsSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings',                   [CmsSettingsController::class, 'update'])->name('settings.update');
        Route::delete('/settings/logo',            [CmsSettingsController::class, 'removeLogo'])->name('settings.logo.remove');
    });
});
