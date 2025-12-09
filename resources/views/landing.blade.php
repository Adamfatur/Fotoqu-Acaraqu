@extends('layouts.landing')

@section('title', 'FotoQu | Photobooth Termurah di Tangerang - Hasil Studio, Cetak Instan')
@section('meta_description', 'Cari photobooth termurah di Tangerang? FotoQu solusinya! Kualitas studio, cetak instan, softcopy QR. Cocok untuk wedding, lamaran, & event kantor. Booking sekarang!')

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