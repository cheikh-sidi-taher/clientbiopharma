@extends('layouts.app')

@section('title', 'Accès refusé')
@section('page_title', 'Accès refusé')

@section('content')
<div class="card" style="max-width:560px;margin:0 auto;">
    <div class="card-body" style="text-align:center;padding:40px 32px;">
        <div style="font-size:48px;margin-bottom:16px;color:#dc2626;"><i class="bi bi-shield-lock-fill"></i></div>
        <h1 style="font-size:20px;font-weight:700;margin-bottom:8px;color:var(--text-primary);">Vous n’avez pas l’autorisation</h1>
        <p style="color:var(--text-secondary);font-size:14px;line-height:1.6;margin-bottom:24px;">
            Votre rôle ne permet pas d’accéder à cette page. Contactez un administrateur si vous pensez qu’il s’agit d’une erreur.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-grid-1x2-fill"></i> Retour au tableau de bord
        </a>
    </div>
</div>
@endsection
