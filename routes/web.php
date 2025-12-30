<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->role === 'admin') {
        return redirect()->route('dashboard');
    }

    return redirect()->route('resume.show');
});



Route::middleware(['guest'])->group(function () {
    Route::view('/login', 'login')->name('login');

    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])
        ->name('google.redirect');

    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('google.callback');
});

Route::middleware(['auth'])->group(function () {

    
    Route::middleware([\App\Http\Middleware\UserMiddleware::class])->group(function () {
        Route::get('/resume/upload', [ResumeController::class, 'show'])->name('resume.show');
        Route::post('/resume/upload', [ResumeController::class, 'store'])->name('resume.store');


        Route::middleware(['auth', \App\Http\Middleware\EligibilityMiddleware::class])->group(function () {
            Route::get('/assessment/rules', [AssessmentController::class, 'rules'])->name('assessment.rules');
            Route::post('/assessment/start', [AssessmentController::class, 'start'])->name('assessment.start');
            Route::get('/assessment/test/{attempt}', [AssessmentController::class, 'test'])->name('assessment.test');
            Route::post('/assessment/submit/{attempt}', [AssessmentController::class, 'submit'])->name('assessment.submit');
            Route::get('/assessment/result/{attempt}', [AssessmentController::class, 'result'])->name('assessment.result');
        });

        Route::post('/assessment/{attempt}/violation', [AssessmentController::class, 'violation'])->name('assessment.violation');
    });

    
    Route::get('/dashboard', [AssessmentController::class, 'index'])
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->name('dashboard');

    Route::get('/admin/reports', [AssessmentController::class, 'reports'])
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->name('admin.reports');

    Route::get('/admin/report/{attempt}', [AssessmentController::class, 'reportDetail'])
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->name('admin.report.detail');

    Route::post('/admin/questions/upload', [AssessmentController::class, 'uploadQuestions'])
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->name('admin.questions.upload');

    Route::get('/admin/report/{attempt}/download', 
    [AssessmentController::class, 'downloadReport']
)->middleware('admin')->name('admin.report.download');

     Route::get('/admin/reports/download', 
    [AssessmentController::class, 'downloadSummaryReport']
)->middleware('admin')->name('admin.reports.download');

Route::get(
    '/admin/reports/download/summary',
    [AssessmentController::class, 'downloadSummaryReport']
)->middleware('admin')
 ->name('admin.reports.download.summary');


 //Admin User Management Routes
     Route::get('/admin/users', [AdminUserController::class, 'index'])
    ->middleware('admin')
    ->name('admin.users');

Route::post('/admin/users/{user}/toggle-access', [AdminUserController::class, 'toggleAccess'])
    ->middleware('admin')
    ->name('admin.users.toggle');

Route::post('/admin/users/invite', [AdminUserController::class, 'invite'])
    ->middleware('admin')
    ->name('admin.users.invite');



    Route::post('/logout', function () {
        // Auto-submit in-progress assessment only for regular users
        if (Auth::user() && Auth::user()->role !== 'admin') {
            $attempt = \App\Models\AssessmentAttempt::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->first();

            if ($attempt) {
                // Auto-submit the assessment
                $service = new \App\Services\AssessmentService();
                $service->evaluate($attempt);
            }
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        session()->flush();

        // Clear session cookie
        Cookie::queue(Cookie::forget('laravel_session'));

        // Clear all cookies
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', time() - 3600, '/');
        }

        return redirect('/login')->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Clear-Site-Data' => '"cookies", "storage", "cache"'
        ]);
    })->name('logout');
});
