<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resume;

class EligibilityMiddleware
{
    public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect()->route('dashboard');
    }

    if (
            $user->role !== 'user' ||
            !$user->is_invited ||
            !$user->can_take_test
        ) {
            return redirect()->route('resume.show')
                ->with('error', 'You are not allowed to take the assessment.');
        }

    $resume = Resume::where('user_id', $user->id)
        ->where('is_valid', true)
        ->first();

    if (!$resume || !session('has_valid_resume')) {
        return redirect()->route('resume.show')
            ->with('error', 'Upload a valid resume first.');
    }

    return $next($request);
}
}