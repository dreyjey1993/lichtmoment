@extends('layouts.app')

@section('title', 'Admin Login – Lichtmoment')

@section('content')
@php $adminNav = false; $noFooter = true; @endphp

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-offwhite to-cream px-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="font-serif text-4xl text-gold-400 tracking-wide mb-2">Lichtmoment</h1>
            <p class="text-gray-400 text-sm">Admin-Panel</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Benutzername</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-gold-400 focus:ring-2 focus:ring-gold-100 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Passwort</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-gold-400 focus:ring-2 focus:ring-gold-100 outline-none transition-all">
                </div>
                <button type="submit" class="w-full py-3 bg-gold-400 hover:bg-gold-500 text-white font-medium rounded-xl transition-colors">
                    Anmelden
                </button>
            </form>
        </div>

        <p class="text-center mt-6 text-xs text-gray-300">
            <a href="{{ route('home') }}" class="hover:text-gold-400 transition-colors">Zurück zur Webseite</a>
        </p>
    </div>
</div>
@endsection
