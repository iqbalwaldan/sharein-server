<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

// use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function indexLogin()
    {
        return view('client.user.auth.login.index');
    }

    public function login(Request $request)
    {
        Cookie::queue(Cookie::forget('facebookData'));

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (auth()->user()->email_verified_at == null) {
                Auth::logout();
                return back()->withErrors(['title' => 'Verify Email', 'credentials' => 'Please verify your email first!']);
            }
            // Regenerate the session
            $request->session()->regenerate();

            // Generate a session token
            $sessionToken = session()->getId();

            // Save the session token in a cookie
            setcookie('tokenlogin', $sessionToken, time() + (86400 * 30), "/"); // Cookie will expire in 30 days

            if (!isset($_COOKIE['facebookData'])) {
                // Set cookie
                setcookie('facebookData', DashboardController::fetchFacebookData(), time() + (86400 * 30), "/"); // Cookie will expire in 30 days
            }

            return redirect()->intended('/dashboard');
        }
        // return back()->with('error', 'Login failed!');
        return back()->withErrors(['title' => 'Sign In Failed', 'credentials' => 'Please check your email and password again!']);
    }

    public function indexRegister()
    {
        return view('client.user.auth.register.index');
    }

    public function register(RegisterRequest $request)
    {
        $request->validated($request->all());

        $registrationData = $request->all();
        $registrationData['password'] = bcrypt($registrationData['password']);

        $user = User::create($registrationData);
        $authentication['token'] =  $user->createToken('auth_tokens')->plainTextToken;

        Auth::logout();

        event(new Registered($user)); //Sending Verification Email

        return view('client.user.auth.login.index');
    }

    public function indexForgotPassword()
    {
        return view('client.user.auth.forgot-password.index');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function indexResetPassword($token)
    {
        return view('client.user.auth.reset-password.index', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($request->password != $request->password_confirmation) {
            return back()->withErrors(['password' => 'Password confirmation does not match']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('user.login')->with('status', __($status))
            : back()->withErrors(['password' => [__($status)]]);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        // Make invalidate the session
        $request->session()->invalidate();

        // Regenerate the session
        $request->session()->regenerateToken();

        // Delete the session token in a cookie 
        setcookie('tokenlogin', '', time() - 3600, "/");

        return redirect('/login');
    }

    public function emailVerificationProcess()
    {
        return view('client.email-verification.process.index');
    }

    public function resendEmailVerification()
    {
        // Masi mikir sabar
    }

    public function emailVerificationSuccess()
    {
        return view('client.email-verification.success.index', [
            'title' => "Email Verified",
            'description' => "Your email address was successfully verified.",
            'year' => now()->year
        ]);
    }
    public function emailVerificationAlreadySuccess()
    {
        return view('client.email-verification.success.index', [
            'title' => "Email Already Verified",
            'description' => "Your email address was already verified.",
            'year' => now()->year
        ]);
    }
}
