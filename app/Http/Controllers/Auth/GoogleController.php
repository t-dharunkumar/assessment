<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::where('email', $googleUser->email)->first();

if (!$user) {
    return redirect('/login')
        ->with('error', 'You are not invited to take this assessment.');
}


    if ($user->name === $user->email) {
        $user->update(['name' => $googleUser->name]);
    }

    if ($user->role !== 'admin' && (!$user->is_invited || !$user->can_take_test)) {
        Auth::logout();
        return redirect('/login')
            ->with('error', 'You don\'t have access to the assessment.');
    }

    Auth::login($user);

    return redirect()->route('resume.show');

}
}