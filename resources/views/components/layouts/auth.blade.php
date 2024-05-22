<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @filamentStyles
    @livewireStyles
    @livewire('notifications')
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md dark:border dark:bg-gray-800 dark:border-gray-700">

        <div class="p-2 space-y-4 md:space-y-6 sm:p-8 flex flex-col items-center"> <!-- Added flex and items-center -->
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-auto h-15 mr-2" src="{{asset ('images/Logo_CBI_2.png')}}" alt="logo">
            </a>

            <div class="w-full"> <!-- Added a div to contain the form -->
                {{$slot}} <!-- Place your form content inside this div -->
            </div>
        </div>

    </div>

    @filamentScripts
    @livewireScripts
</body>



</html>