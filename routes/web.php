<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes (Laravel provides these with Laravel UI)
Auth::routes();

// Events routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

// Routes requiring authentication
Route::middleware(['auth'])->group(function () {
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/events/{eventId}/tickets/{ticketTypeId}/book', [BookingController::class, 'create'])
        ->name('bookings.create');
    Route::post('/events/{eventId}/tickets/{ticketTypeId}/book', [BookingController::class, 'store'])
        ->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{id}/verify', [BookingController::class, 'verifyBooking'])->name('bookings.verify');

    // Additional Payment Routes for Booking Updates
    Route::get('/payment/additional/{bookingId}', [PaymentController::class, 'additionalPayment'])->name('payment.additional');
    Route::post('/payment/process-additional/{bookingId}', [PaymentController::class, 'processAdditionalPayment'])->name('payment.process.additional');
    Route::post('/payment/{bookingId}/stripe-additional', [PaymentController::class, 'processStripeAdditional'])->name('payment.process.stripe.additional');
    Route::get('/payment/{bookingId}/additional-success', [PaymentController::class, 'additionalSuccess'])->name('payment.additional.success');
    Route::get('/payment/{bookingId}/additional-cancel', [PaymentController::class, 'additionalCancel'])->name('payment.additional.cancel');

    // Standard Payments
    Route::get('/payments/{bookingId}', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payments/{bookingId}/stripe', [PaymentController::class, 'processStripePayment'])
        ->name('payment.process.stripe');
    Route::post('/payments/{bookingId}/paypal', [PaymentController::class, 'processPayPalPayment'])
        ->name('payment.process.paypal');
    Route::get('/payments/{bookingId}/success', [PaymentController::class, 'stripeSuccess'])
        ->name('payment.success');
    Route::get('/payments/{bookingId}/cancel', [PaymentController::class, 'cancel'])
        ->name('payment.cancel');

    // Booking edit and cancel routes
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::get('/bookings/{id}/cancel-confirmation', [BookingController::class, 'cancelConfirmation'])->name('bookings.cancel-confirmation');
    Route::delete('/bookings/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('bookings.cancel-booking');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}/edit-role', [AdminController::class, 'editUserRole'])->name('admin.users.edit-role');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.update-role');
    Route::get('/events', [AdminController::class, 'events'])->name('admin.events');
    Route::put('/events/{id}/status', [AdminController::class, 'updateEventStatus'])->name('admin.events.update-status');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::get('/reports', [AdminController::class, 'generateReport'])->name('admin.reports');
});

// Organizer routes
Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->group(function () {
    Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('organizer.dashboard');
    Route::get('/events', [OrganizerController::class, 'events'])->name('organizer.events');
    Route::get('/events/{eventId}/bookings', [OrganizerController::class, 'eventBookings'])
        ->name('organizer.event.bookings');
    Route::get('/events/{eventId}/report', [OrganizerController::class, 'generateEventReport'])
        ->name('organizer.event.report');
    
    // Event CRUD
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::put('/events/{id}/status', [EventController::class, 'updateStatus'])->name('events.update-status');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // Ticket Types
    Route::get('/events/{eventId}/ticket-types/create', [EventController::class, 'createTicketType'])
        ->name('ticket-types.create');
    Route::post('/events/{eventId}/ticket-types', [EventController::class, 'storeTicketType'])
        ->name('ticket-types.store');
    Route::get('/events/{eventId}/ticket-types/{ticketTypeId}/edit', [EventController::class, 'editTicketType'])
        ->name('ticket-types.edit');
    Route::put('/events/{eventId}/ticket-types/{ticketTypeId}', [EventController::class, 'updateTicketType'])
        ->name('ticket-types.update');
    Route::delete('/events/{eventId}/ticket-types/{ticketTypeId}', [EventController::class, 'destroyTicketType'])
        ->name('ticket-types.destroy');
});