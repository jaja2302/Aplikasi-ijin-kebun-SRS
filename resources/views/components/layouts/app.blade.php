<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @filamentStyles
    @livewireStyles
    @livewire('notifications')
</head>

<body class="antialiased bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">

            <img class="w-10 h-10 p-1 rounded-full ring-2 ring-gray-300 dark:ring-gray-500" src="{{asset ('/images/icons8-user-80.png')}}" alt="Bordered avatar">

            <button class="px-4 py-2 bg-gray-800 text-white rounded">Log Out</button>
        </div>
        {{$slot}}
    </div>

    @filamentScripts
    @livewireScripts
</body>

</html>