@extends('layouts.app')

@section('title', 'Impressum – Lichtmoment')

@section('content')
<div class="py-24 px-6 bg-offwhite min-h-screen">
    <div class="max-w-3xl mx-auto">
        <h1 class="font-serif text-4xl text-gray-700 text-center mb-12">Impressum</h1>
        <div class="bg-white rounded-2xl border border-gray-100 p-8 md:p-12 prose prose-gray max-w-none">
            <h2>Angaben gemäß § 5 TMG</h2>
            <p>
                Markus Licht<br>
                Hochzeitsfotografie<br>
                Musterstraße 123<br>
                12345 Musterstadt
            </p>

            <h2>Kontakt</h2>
            <p>
                Telefon: +49 171 234 56 78<br>
                E-Mail: info@lichtmoment.de
            </p>

            <h2>Umsatzsteuer-ID</h2>
            <p>Umsatzsteuer-Identifikationsnummer gemäß §27a UStG: DE123456789</p>

            <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
            <p>Markus Licht, Musterstraße 123, 12345 Musterstadt</p>

            <h2>Streitbeilegung</h2>
            <p>
                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
                <a href="https://ec.europa.eu/consumers/odr" target="_blank">https://ec.europa.eu/consumers/odr</a>
            </p>
            <p>Wir sind nicht bereit oder verpflichtet, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
        </div>
    </div>
</div>
@endsection
