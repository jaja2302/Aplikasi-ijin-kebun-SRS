<!DOCTYPE html>
<html data-theme="emerald" lang="en">

<head>
    <title>@yield('title') - {{ config('app.name') }}</title>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('images/icons/logo.svg') }}">

    @livewire('notifications')
    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body>
    @yield('content')

    @livewireScripts
    @filamentScripts
    @vite('resources/js/app.js')
</body>

</html>
