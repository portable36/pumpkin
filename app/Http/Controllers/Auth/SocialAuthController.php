<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::firstOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'User',
            'password' => bcrypt(Str::random(32)),
        ]);

        // Create a Sanctum token for SPA usage
        $token = $user->createToken('social')->plainTextToken;

        // For web flows, redirect to frontend with token
        $redirect = config('app.url') . '/?token=' . $token;
        return redirect($redirect);
    }
}
