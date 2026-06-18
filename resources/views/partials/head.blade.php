<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="{{ asset('assets/images/favicons/favicon-16x16.png') }}" sizes="any">
<link rel="icon" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
@livewireStyles
