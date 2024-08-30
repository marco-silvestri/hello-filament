<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('components.elements.favicon')

    @section('meta-tags')
    @show

    @section('title')
    <title>{{ config('app.name') }}</title>
    @show

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {!! $headSnippets !!}
    @vite('resources/css/app.css')
</head>

<body class="antialiased">
    <div class="md:w-[1280px] m-auto">
        {!! $bodySnippets !!}
        {{ $slot }}
    </div>


    @vite('resources/js/app.js')
    {!! $footerSnippets !!}
</body>

</html>
