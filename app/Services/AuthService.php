<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Tenta realizar o login com as credenciais fornecidas
     *
     * @param array $credentials Credenciais do usuário (email e senha)
     * @return string|false Token JWT se bem sucedido, false caso contrário
     */
    public function attemptLogin(array $credentials)
    {
        return Auth::attempt($credentials);
    }

    /**
     * Obtém o usuário autenticado
     *
     * @return \App\Models\User|null Usuário autenticado ou null
     */
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

    /**
     * Realiza o logout do usuário autenticado
     *
     * @return void
     */
    public function logout()
    {
        Auth::logout();
    }

    /**
     * Atualiza o token JWT
     *
     * @return string Novo token JWT
     */
    public function refreshToken()
    {
        return Auth::refresh();
    }

    /**
     * Obtém o tempo de vida do token em segundos
     *
     * @return int Tempo de vida em segundos
     */
    public function getTokenTTL()
    {
        return Auth::factory()->getTTL() * 60;
    }
}

