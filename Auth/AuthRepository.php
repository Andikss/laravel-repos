<?php

namespace App\Http\Repositories\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

interface AuthRepositoryInterface
{
    public function login(Request $request);
    public function logout(Request $request);
}

class AuthRepository implements AuthRepositoryInterface
{
    public function login(Request $request)
    {
        $get  = $request->isMethod('GET');
        $post = $request->isMethod('POST');

        try {
            if ($post) {
                $credentials = $request->only('username', 'password');

                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();

                    $user  = Auth::user();
                    $token = self::getToken($user);

                    return response()->json([
                        'message'  => 'Login Succeed',
                        'data'     => $user,
                        'token'    => $token,
                        'redirect' => '/'
                    ], 200);
                }
            } else if ($get) {
                return view('pages.login');
            }
        } catch (Exception $e) {
            log::critical('Error while logging in : ' . $e->getMessage());
            throw new Exception('Error while logging in', 500);
        }

        throw ValidationException::withMessages([
            'username' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (Exception $e) {
            Log::critical('Error while logging out : ' . $e->getMessage());
            throw new Exception('Error while logging out', 500);
        }
    }

    protected static function getToken(User $user): string
    {
        $user  = User::find($user->id);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $token;
    }

    protected static function isAuthorize(): bool
    {
        return Auth::check();
    }
}
