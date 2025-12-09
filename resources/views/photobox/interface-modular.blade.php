<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fotoku Photobox - {{ $photobox->code }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    {{-- Import Styles --}}
    @include('photobox.components.styles')
</head>
<body class="h-screen">
    <div id="app" class="h-full flex flex-col">
        {{-- Header with Camera Settings --}}
        @include('photobox.components.header')

        {{-- Main Content Area --}}
        <main class="flex-1 p-6 overflow-hidden">
            {{-- Waiting State --}}
            @include('photobox.components.waiting-state')

            {{-- Camera Capture State --}}
            @include('photobox.components.capture-state')

            {{-- Photo Selection State --}}
            @include('photobox.components.selection-state')

            {{-- Processing State --}}
            @include('photobox.components.processing-state')

            {{-- Completed State --}}
            @include('photobox.components.completed-state')
        </main>
    </div>

    {{-- External Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    {{-- Import JavaScript Components --}}
    @include('photobox.components.core-js')
    @include('photobox.components.state-management-js')
    @include('photobox.components.camera-js')
    @include('photobox.components.photo-selection-js')
    @include('photobox.components.session-management-js')
    @include('photobox.components.processing-js')
    @include('photobox.components.global-functions')
    {{-- Load completed-state-js LAST to override showCompletedState --}}
    @include('photobox.components.completed-state-js')
</body>
</html>
