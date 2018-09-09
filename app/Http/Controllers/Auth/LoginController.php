<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Sms\OtpLoginCode\UserSms;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('otp')->only('showOtpForm', 'otp');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @return mixed
     */
    protected function authenticated(Request $request, User $user)
    {
        // Log user out.
        $this->guard()->logout();

        // Place the user ID in the session.
        session()->put('otp.user_id', $user->id);

        // Generate and send the OTP code.
        $otpCode = mt_rand(10000, 99999);
        session()->put('otp.code', $otpCode);
        $user->sendSms(new UserSms($user->phone, ['OTP_CODE' => $otpCode]));

        // Forward the user to the code page.
        return redirect('/login/code');
    }

    /**
     * Show the one time password form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showOtpForm()
    {
        return view('auth.code');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function otp(Request $request)
    {
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendOtpLockoutResponse($request);
        }

        // Validate the OTP code and login if correct.
        if ($request->token == $request->session()->get('otp.code')) {
            $userId = $request->session()->get('otp.user_id');
            $this->guard()->login(User::findOrFail($userId));

            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            session()->forget(['otp.user_id', 'otp.code']);

            return redirect()->intended($this->redirectPath());
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedOtpResponse($request);
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendOtpLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            'token' => [Lang::get('auth.throttle', ['seconds' => $seconds])],
        ])->status(429);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedOtpResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'token' => ['The token provided is incorrect.'],
        ]);
    }
}
