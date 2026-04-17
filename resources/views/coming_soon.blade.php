@extends('layouts.app')

@section('title', $module)
@section('page_title', $module)

@section('content')
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:60vh; text-align:center;">
    <div style="width:100px;height:100px;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:48px;color:var(--primary);margin-bottom:24px;">
        <i class="bi bi-hourglass-split"></i>
    </div>
    <h2 style="font-size:24px;font-weight:800;color:var(--text-primary);margin-bottom:12px;">Module {{ $module }}</h2>
    <p style="font-size:15px;color:var(--text-secondary);max-width:400px;line-height:1.6;margin-bottom:28px;">
        Ce module sera disponible dans le prochain sprint. Il est en cours de développement selon le plan d'implémentation Biopharma.
    </p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> Retour au Dashboard
    </a>
</div>
@endsection
