<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('components.elements.favicon')

    <meta name="description" content="{{__('meta-tags.description')}}">
    <meta property="og:title" content="{{__('meta-tags.title')}}">
    <meta property="og:description" content="{{__('meta-tags.description')}}">
    <meta property="og:site_name" content="{{config('app.name')}}">
    <meta property="og:image" content="{{__('meta-tags.image')}}">

    @section('title')
    <title>{{ config('app.name') }}</title>
    @show
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @livewireStyles
    @vite('resources/css/app.css')
</head>

<body class="antialiased">
    {{ $slot }}
    @filamentScripts
    @livewireScripts
    @vite('resources/js/app.js')
</body>

</html>
