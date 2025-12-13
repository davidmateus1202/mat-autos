<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)
                ->setHttpClient(new Client(['verify' => false]))
                ->user();
            Log::info("Social login success for provider: {$provider}", ['email' => $socialUser->getEmail()]);
        } catch (\Exception $e) {
            Log::error("Social login failed for provider: {$provider}", ['error' => $e->getMessage()]);
            return redirect('/login')->withErrors(['email' => 'Unable to login with ' . $provider]);
        }

        $user = DB::transaction(function () use ($socialUser, $provider) {
            // Check if social account exists
            $account = SocialAccount::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if ($account) {
                Log::info("Found existing social account for user: {$account->user_id}");
                return $account->user;
            }

            // Check if user with email exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                Log::info("Creating new user for email: {$socialUser->getEmail()}");
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(str()->random(16)), // Random password
                ]);
            } else {
                Log::info("Found existing user by email: {$user->id}");
            }

            // Create social account
            Log::info("Linking social account {$provider} to user {$user->id}");
            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]);

            return $user;
        });

        Auth::login($user);
        Log::info("User {$user->id} logged in via social auth. Session ID: " . session()->getId());

        return redirect('/'); // Redirect to SPA dashboard
    }
}
