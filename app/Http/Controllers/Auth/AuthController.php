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
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function login_show(): View
    {
        return view('login.login_form');
    }

    public function login(LoginFormRequest $request)
    {
        $creadentials = $request->only('email', 'password');

        $user = $this->user->getUserByEmail($creadentials['email']);

        if(!is_null($user)) {
            if ($this->user->isAccountLocked($user)) {
                return back()->withErrors([
                    'danger' => 'アカウントがロックされています。',
                ]);
            }

            if (Auth::attempt($creadentials)) {
                $request->session()->regenerate();
                $this->user->resetErrorCount($user);

                return redirect()->route('home')->with('success', 'ログイン成功しました！');
            }
            
            $user->error_count = $this->user->addErrorCount($user->error_count);
            if ($this->user->lockAccount($user)) {
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