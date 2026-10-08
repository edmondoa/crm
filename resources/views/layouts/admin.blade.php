<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        @yield('title', 'CRM')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
    @stack('styles')

</head>


<body>

    <div class="admin-layout">

        {{-- Sidebar --}}
        @include('layouts.sidebar')


        {{-- Main --}}
        <div class="admin-main">

            {{-- Top Navigation --}}
            @include('layouts.navigation')


            {{-- Page --}}
            <main class="erp-page">

                @yield('content')

            </main>

        </div>

    </div>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>

@stack('scripts')
@stack('scripts')
</body>

</html>