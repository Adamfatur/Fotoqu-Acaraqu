@extends('layouts.landing')

@section('title', 'FotoQu | Bikin Momen Berkesan dengan Photobooth Berkualitas Tinggi')
@section('meta_description', 'FotoQu menghadirkan photobooth berkualitas tinggi untuk semua event. Dari softcopy unlimited hingga custom frame A5. Hubungi WhatsApp untuk booking sekarang!')

@section('content')
    @include('landing._navbar')
    @include('landing._hero')
    @include('landing._features')
    @include('landing._how-it-works')
    @include('landing._portfolio')
    @include('landing._testimonials')
    {{-- @include('landing._pricing') --}}
    @include('landing._cta')
    @include('landing._footer')
@endsection
