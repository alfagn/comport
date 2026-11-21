<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Empetix') }} — Enterprise Studio & SaaS Control Center</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon.svg') }}"/>

    <!-- Google Fonts: Archivo, Public Sans, IBM Plex Mono, IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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
                            900: '#08142C',
                            850: '#0D1E3D',
                            800: '#13274D',
                            700: '#1E3664',
                            600: '#2F4B80',
                        },
                        accent: {
                            50: '#FFF3EF',
                            100: '#FFE4DB',
                            500: '#FF4D1C',
                            600: '#E03D10',
                            700: '#C23008',
                        },
                        neutral: {
                            0: '#FFFFFF',
                            50: '#F9F9F8',
                            100: '#F3F4F6',
                            150: '#EBEBE6',
                            200: '#E2E4E9',
                            300: '#C2C2B8',
                            500: '#64748B',
                            700: '#334155',
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
                    },
                    boxShadow: {
                        'subtle': '0 1px 3px 0 rgba(8, 20, 44, 0.04), 0 1px 2px -1px rgba(8, 20, 44, 0.04)',
                        'card': '0 10px 25px -5px rgba(8, 20, 44, 0.06), 0 8px 10px -6px rgba(8, 20, 44, 0.04)',
                        'glow': '0 8px 20px -4px rgba(255, 77, 28, 0.35)',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        /* Custom sleek scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.25); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 77, 28, 0.6); }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="h-full bg-[#F4F6F9] text-ink-900 antialiased font-sans flex flex-col selection:bg-accent-500 selection:text-white">

    {{ $slot }}

    <!-- Global Browser Toast Notifications -->
    <x-ui.toast />

    @livewireScripts
    @stack('scripts')
</body>
</html>
