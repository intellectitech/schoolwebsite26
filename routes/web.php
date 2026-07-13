<?php

use App\Http\Controllers\AcademicsController;
use App\Http\Controllers\Admin\AdmissionStepController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FacultyMemberController;
use App\Http\Controllers\Admin\FaqItemController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\PathwayController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public site routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/subscribe', [HomeController::class, 'subscribe'])->name('subscribe');
Route::get('/academics', [AcademicsController::class, 'index'])->name('academics');
Route::get('/admissions', [AdmissionsController::class, 'index'])->name('admissions');
Route::get('/campus', [CampusController::class, 'index'])->name('campus');
Route::get('/apply', [ApplicationController::class, 'create'])->name('apply.create');
Route::post('/apply', [ApplicationController::class, 'store'])->name('apply.store');
Route::post('/apply/draft', [ApplicationController::class, 'saveDraft'])->name('apply.draft.save');
Route::get('/apply/draft/{draftToken}', [ApplicationController::class, 'loadDraft'])->name('apply.draft.load');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Breeze auth routes
|--------------------------------------------------------------------------
| Public registration is disabled for this application. Admin/editor
| accounts are provisioned via seeder or `php artisan tinker` rather than
| self-signup. Login/logout and password reset remain available.
*/
require __DIR__.'/auth.php';

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware('auth')
    ->name('dashboard');

// Removed default Breeze dashboard route in favor of the admin dashboard redirect.
/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('news', NewsPostController::class);
    Route::resource('events', EventController::class);
    Route::resource('pathways', PathwayController::class);
    Route::resource('faculty', FacultyMemberController::class);
    Route::resource('facilities', FacilityController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('gallery', GalleryItemController::class);
    Route::resource('faqs', FaqItemController::class);
    Route::resource('admission-steps', AdmissionStepController::class);
    Route::get('applications', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('applications.index');
Route::get('applications/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'show'])->name('applications.show');
Route::patch('applications/{application}/status', [\App\Http\Controllers\Admin\ApplicationController::class, 'updateStatus'])->name('applications.status');
Route::get('messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('messages.index');
Route::get('messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('messages.show');
Route::delete('messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');
});
