<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\FacebookAccountController;

class SocialAuthController extends Controller
{
    public function redirectToProvider()
    {
        $permissions = [
            'pages_manage_engagement',
            'pages_manage_posts',
            'pages_read_engagement',
            'pages_read_user_content',
        ];

        return Socialite::driver('facebook')
            ->scopes($permissions)
            ->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            // Ekstrak token akses dari respon
            $user_access_token = $user->token;
            // Simpan token akses ke dalam session
            DashboardController::addFacebookAccount($user_access_token);
            // check cookie
            FacebookAccountController::updateCookies();
            return redirect()->route('home')->with('success', 'Login successful!');
        } catch (Exception $e) {
            return redirect()->route('user.login')->with('error', 'Error retrieving access token');
        }
    }
}
