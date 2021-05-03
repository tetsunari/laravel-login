<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login_show(): View
    {
        return view('login.login_form');
    }

    public function login(LoginFormRequest $request)
    {
        $creadentials = $request->only('email', 'password');

        if (Auth::attempt($creadentials)) {
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', 'ログイン成功しました！');
        }

        return back()->withErrors([
            'danger' => 'メールアドレスかパスワードが間違っています。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login_show')->with('logout', 'ログアウトしました！');
    }
}