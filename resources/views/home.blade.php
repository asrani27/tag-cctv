@extends('layouts.public')

@section('title', 'Beranda | Sistem Informasi Survei CCTV & WiFi Kota Banjarmasin')
@section('meta_description', 'Portal informasi dan pemetaan spasial hasil survei lokasi CCTV dan WiFi di Kota Banjarmasin untuk mendukung perencanaan infrastruktur digital smart city.')

@section('content')
    {{-- 1. Hero Section --}}
    @include('public.partials.hero')

    {{-- 2. Statistik Publik --}}
    @include('public.partials.stats')

    {{-- 3. Section Peta Persebaran --}}
    @include('public.partials.map-section')

    {{-- 4. Section Data Survei Terbaru --}}
    @include('public.partials.recent-surveys')

    {{-- 5. Section Fitur / Keunggulan Sistem --}}
    @include('public.partials.features')

    {{-- 6. Section Informasi Survei --}}
    @include('public.partials.info-scope')

    {{-- 7. Section Call to Action --}}
    @include('public.partials.cta')
@endsection
