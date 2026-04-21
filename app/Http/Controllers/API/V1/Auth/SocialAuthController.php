<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function googleRedirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()->getTargetUrl(),
        ]);
    }

    public function googleCallback(Request $request): JsonResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Google login'], 401);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $firstName = explode(' ', $googleUser->getName())[0] ?? 'Google';
            $lastName = explode(' ', $googleUser->getName())[1] ?? 'User';
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'name' => trim($firstName.' '.$lastName),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
                'referral_code' => Str::upper(Str::random(8)),
            ]);
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function facebookRedirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('facebook')
                ->stateless()
                ->redirect()->getTargetUrl(),
        ]);
    }

    public function facebookCallback(Request $request): JsonResponse
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Facebook login'], 401);
        }

        $user = User::where('facebook_id', $facebookUser->getId())
            ->orWhere('email', $facebookUser->getEmail())
            ->first();

        if (! $user) {
            $firstName = explode(' ', $facebookUser->getName())[0] ?? 'Facebook';
            $lastName = explode(' ', $facebookUser->getName())[1] ?? 'User';
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'name' => trim($firstName.' '.$lastName),
                'email' => $facebookUser->getEmail(),
                'facebook_id' => $facebookUser->getId(),
                'avatar' => $facebookUser->getAvatar(),
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
                'referral_code' => Str::upper(Str::random(8)),
            ]);
        } else {
            $user->update([
                'facebook_id' => $facebookUser->getId(),
                'avatar' => $facebookUser->getAvatar(),
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function appleRedirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('apple')
                ->stateless()
                ->redirect()->getTargetUrl(),
        ]);
    }

    public function appleCallback(Request $request): JsonResponse
    {
        try {
            $appleUser = Socialite::driver('apple')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Apple login'], 401);
        }

        $user = User::where('apple_id', $appleUser->getId())
            ->orWhere('email', $appleUser->getEmail())
            ->first();

        $name = explode(' ', $appleUser->getName() ?? 'Apple User');

        if (! $user) {
            $user = User::create([
                'first_name' => $name[0] ?? 'Apple',
                'last_name' => $name[1] ?? 'User',
                'name' => trim(($name[0] ?? 'Apple').' '.($name[1] ?? 'User')),
                'email' => $appleUser->getEmail(),
                'apple_id' => $appleUser->getId(),
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
                'referral_code' => Str::upper(Str::random(8)),
            ]);
        } else {
            $user->update(['apple_id' => $appleUser->getId()]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
