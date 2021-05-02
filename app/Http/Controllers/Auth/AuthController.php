<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
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

            return redirect('home')->with('login_success', 'ログイン成功しました！');
        }

        return back()->withErrors([
            'login_error' => 'メールアドレスかパスワードが間違っています。',
        ]);
    }
}