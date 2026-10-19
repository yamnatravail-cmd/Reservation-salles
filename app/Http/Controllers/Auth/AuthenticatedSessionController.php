<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // تأكد من استيراد نموذج User

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */public function store(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token
    ]);
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // تسجيل الخروج من الجلسة
        Auth::guard('web')->logout();

        // إلغاء التفعيل و تجديد التوكن
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
