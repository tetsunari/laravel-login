<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Models\User;
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

        $user = User::where('email', '=', $creadentials['email'])->first();

        if(!is_null($user)) {
            if ($user->locked_flg === 1) {
                return back()->withErrors([
                    'danger' => 'アカウントがロックされています。',
                ]);
            }

            if (Auth::attempt($creadentials)) {
                $request->session()->regenerate();
                if ($user->error_count > 0) {
                    $user->error_count = 0;
                    $user->save();
                }

                return redirect()->route('home')->with('success', 'ログイン成功しました！');
            }
            
            $user->error_count = $user->error_count + 1;
            if ($user->error_count > 5) {
                $user->locked_flg = 1;
                $user->save();

                return back()->withErrors([
                    'danger' => 'アカウントがロックされました。',
                ]);    
            }
            $user->save();
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