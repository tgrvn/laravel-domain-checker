<?php

namespace App\Http\Controllers;


use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $this->authService->register($request->validated());

        return redirect()->route('domains.index')->with('success', 'Регистрация прошла успешно!');
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request)
            ? redirect()->route('domains.index')
            : back()->withErrors(['email' => 'Неверные учетные данные.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}