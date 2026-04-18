@extends('layouts.auth-biopharma')

@section('title', 'Mot de passe oublié — Biopharma CRM')

@section('auth_heading', 'Réinitialiser le mot de passe')
@section('auth_lead', 'Indiquez votre adresse e-mail : nous vous enverrons un lien pour choisir un nouveau mot de passe.')

@section('content')
    <a class="auth-back" href="{{ route('login') }}">
        <i class="bi bi-arrow-left"></i> Retour à la connexion
    </a>

    <h2 class="form-title">Mot de passe oublié</h2>
    <p class="form-subtitle">Saisissez l’e-mail associé à votre compte. Vous recevrez un lien de réinitialisation si un compte existe.</p>

    @if (session('status'))
        <div class="success-message">
            <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:2px;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Adresse e-mail</label>
            <div class="input-wrapper">
                <i class="bi bi-envelope-fill input-icon"></i>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            @error('email')
                <div class="error-message" style="margin-top:10px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-send-fill"></i>
            Envoyer le lien
        </button>
    </form>
@endsection
