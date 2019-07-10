<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Password;
use App\Sms\OtpLoginCode\UserSms;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', new Password()],
        ];
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        // If OTP is disabled then skip this method.
        if (!config('ck.otp_enabled')) {
            return redirect($this->redirectPath())
                ->with('status', trans($response));
        }

        // Get the user.
        $user = $this->guard()->user();

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
}
