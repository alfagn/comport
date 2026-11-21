<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Empetix') }} — Enterprise SaaS & Technology Solutions</title>

    <!-- Google Fonts: Archivo, Public Sans, IBM Plex Mono, IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN for instant styling across local Herd & production -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['{{ app()->getLocale() === "ar" ? "IBM Plex Sans Arabic" : "Public Sans" }}', 'sans-serif'],
                        heading: ['{{ app()->getLocale() === "ar" ? "IBM Plex Sans Arabic" : "Archivo" }}', 'sans-serif'],
                        mono: ['IBM Plex Mono', 'monospace'],
                    },
                    colors: {
                        ink: {
                            950: '#050C1A',
                            900: '#08142C', // Deep Brand Navy
                            850: '#0D1E3D',
                            800: '#13274D',
                            700: '#1E3664',
                            600: '#2F4B80',
                        },
                        accent: {
                            500: '#FF4D1C', // Signature Empetix Orange
                            600: '#E03D10',
                            700: '#C23008',
                        },
                        neutral: {
                            0: '#FFFFFF',
                            50: '#FAFAFA',
                            100: '#F5F5F2', // Warm Off-White
                            150: '#EBEBE6',
                            200: '#DCDCD4', // Hairline 1px Divider
                            300: '#C2C2B8',
                            500: '#595953',
                            700: '#2B2B28',
                            900: '#08142C',
                        },
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#FF4D1C',
                            600: '#E03D10',
                            700: '#08142C',
                            900: '#050C1A',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased font-sans flex flex-col selection:bg-indigo-500 selection:text-white">

    {{ $slot }}

    <!-- Global Browser Toast Notifications -->
    <x-ui.toast />

    @livewireScripts
    @stack('scripts')
</body>
</html>
