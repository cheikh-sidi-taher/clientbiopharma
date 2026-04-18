@extends('layouts.auth-biopharma')

@section('title', 'Nouveau mot de passe — Biopharma CRM')

@section('auth_heading', 'Nouveau mot de passe')
@section('auth_lead', 'Choisissez un mot de passe robuste que vous n’utilisez pas ailleurs.')

@section('content')
    <a class="auth-back" href="{{ route('login') }}">
        <i class="bi bi-arrow-left"></i> Retour à la connexion
    </a>

    <h2 class="form-title">Définir un nouveau mot de passe</h2>
    <p class="form-subtitle">Saisissez votre e-mail et votre nouveau mot de passe.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label class="form-label" for="email">Adresse e-mail</label>
            <div class="input-wrapper">
                <i class="bi bi-envelope-fill input-icon"></i>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <div class="input-wrapper">
                <i class="bi bi-lock-fill input-icon"></i>
                <input type="password" id="password" name="password" class="form-control"
                       required autocomplete="new-password">
            </div>
            @error('password')
                <div class="error-message" style="margin-top:10px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <div class="input-wrapper">
                <i class="bi bi-shield-lock-fill input-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                       required autocomplete="new-password">
            </div>
        </div>

        @error('email')
            <div class="error-message" style="margin-bottom:16px;">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $message }}</span>
            </div>
        @enderror

        <button type="submit" class="btn-login">
            <i class="bi bi-check-lg"></i>
            Enregistrer le mot de passe
        </button>
    </form>
@endsection
