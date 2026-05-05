<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\InternalSetting;
use App\Models\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $internalSetting    = InternalSetting::first(['logo', 'white_logo']);

        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth.verify', ['page' => 'Email Verifikasi'], compact('internalSetting'));
    }

    public function verify(Request $request)
    {
        $user = User::where('id', $request->route('id'))->firstOrFail();

        // Cocokkan hash dari URL dengan hash email user
        if (! hash_equals($request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification hash');
        }

        // Jika sudah diverifikasi
        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('status', 'Email sudah diverifikasi.');
        }

        // Tandai sudah terverifikasi
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect('/login')->with('status', 'Email berhasil diverifikasi!');
    }
}
