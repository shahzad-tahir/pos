<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends ResponseController
{
    //login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            $error = "Invalid credentials! Please try again";
            return $this->sendError(0, $error, null, 401);
        }

        $user = Auth::user();
        $message = "Login successful";

        return $this->sendResponse(1, $message, $user);
    }
}