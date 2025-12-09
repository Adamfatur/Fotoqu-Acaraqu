@extends('admin.layout')

@section('header', 'Dashboard')
@section('description', 'Ringkasan statistik dan aktivitas terkini sistem FOTOQU Photobooth')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100" x-data="dashboardData()">
        {{-- Dashboard Header --}}
        @include('admin.components.dashboard-header')

        {{-- Dashboard Stats Cards --}}
        @include('admin.components.dashboard-stats')

        {{-- Analytics Charts --}}
        @include('admin.components.analytics-charts')

        {{-- Recent Sessions & Live Activities --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            @include('admin.components.recent-sessions')
            @include('admin.components.live-activities')
        </div>

        {{-- Photobox Management --}}
        @include('admin.components.photobox-management')



        {{-- Session Detail Modal --}}
        @include('admin.components.session-detail-modal')

        {{-- Global Confirmation & Alert Modals --}}
        @include('admin.components.confirmation-modals')
    </div>

    @push('styles')
        @include('admin.components.dashboard-styles')
    @endpush

    @push('scripts')
        @include('admin.components.dashboard-scripts')
    @endpush

@endsection