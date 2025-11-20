<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     * Sesuaikan ini jika Anda perlu mengarahkan ke URL tertentu.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json([
            'message' => trans($response)
        ]);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json([
            'email' => [trans($response)]
        ], 422);
    }
}