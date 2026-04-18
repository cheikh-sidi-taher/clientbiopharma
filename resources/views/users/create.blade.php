@extends('layouts.app')

@section('title', 'Nouvel utilisateur')
@section('page_title', 'Nouvel utilisateur')

@section('content')

<div style="max-width:560px;">
    <a href="{{ route('users.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--primary);text-decoration:none;font-weight:600;margin-bottom:16px;">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>

    <div class="card">
        <div class="card-header" style="padding-bottom:0;">
            <h2 class="card-title">Créer un utilisateur</h2>
            <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">Un rôle détermine les permissions dans l’application.</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                    @error('name')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                    @error('email')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Rôle</label>
                    <select name="role" required
                            style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>— Choisir —</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Mot de passe</label>
                    <input type="password" name="password" required autocomplete="new-password"
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                    @error('password')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">
                </div>

                <button type="submit" class="btn btn-primary" style="padding:10px 20px;">
                    <i class="bi bi-check-lg"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
