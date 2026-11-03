<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Helpdesk IT') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen grid lg:grid-cols-2">

            {{-- Panel kiri: identitas RS (hanya tampil di layar lebar) --}}
            <div class="hidden lg:flex relative overflow-hidden bg-brand-800 text-white flex-col justify-between p-12">
                <div class="absolute -top-24 -left-24 h-80 w-80 rounded-full bg-brand-500 opacity-20"></div>
                <div class="absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-brand-500 opacity-10"></div>

                <div class="relative flex items-center gap-3">
                    <img src="{{ asset('images/logo-rs.png') }}" alt="Logo RS Aisyiyah Kudus" class="h-12 w-12 object-contain">
                    <div class="leading-tight">
                        <p class="font-bold text-lg">RS Aisyiyah Kudus</p>
                        <p class="text-sm text-brand-100">Islamic, Safety and Care</p>
                    </div>
                </div>

                <div class="relative">
                    <img src="{{ asset('images/logo-rs.png') }}" alt="" class="h-28 w-28 object-contain mb-8 drop-shadow-lg">
                    <h1 class="text-4xl font-extrabold leading-tight">
                        Sistem <span class="text-brand-500">Helpdesk IT</span>
                    </h1>
                    <p class="mt-4 text-brand-100 max-w-md leading-relaxed">
                        Laporkan kendala komputer, jaringan, printer, dan SIMRS dengan cepat,
                        lalu pantau penanganannya langsung dari satu tempat.
                    </p>
                </div>

                <p class="relative text-sm text-brand-100">
                    Jl. H.O.S. Cokroaminoto No. 248, Kudus
                </p>
            </div>

            {{-- Panel kanan: form --}}
            <div class="flex flex-col items-center justify-center bg-brand-50 px-6 py-10">
                {{-- Logo untuk layar kecil --}}
                <div class="lg:hidden flex flex-col items-center mb-6 text-center">
                    <img src="{{ asset('images/logo-rs.png') }}" alt="Logo RS Aisyiyah Kudus" class="h-20 w-20 object-contain">
                    <p class="mt-3 font-bold text-brand-800 text-lg leading-tight">RS Aisyiyah Kudus</p>
                    <p class="text-sm text-gray-500">Helpdesk IT</p>
                </div>

                <div class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-gray-100 px-8 py-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} RS Aisyiyah Kudus. Helpdesk IT Internal.</p>
            </div>
        </div>
    </body>
</html>
