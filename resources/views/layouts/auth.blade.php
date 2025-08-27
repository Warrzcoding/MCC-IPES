<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Office Performance Evaluation System')</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="{{ asset('images/mainmcc.jpg') }}" as="image" crossorigin>
    <link rel="preload" href="{{ asset('css/shared-background.css') }}" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    
    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/shared-background.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Force image to load immediately -->
    <script>
        // Preload image in JavaScript to ensure it's cached
        const img = new Image();
        img.src = '{{ asset('images/mainmcc.jpg') }}';
        
        // Add to document head to force browser caching
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = '{{ asset('images/mainmcc.jpg') }}';
        document.head.appendChild(link);
    </script>
    
    @yield('additional-head')
</head>
<body>
    <!-- Background decorations -->
    <div class="bg-decorations">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    @yield('content')

    @yield('additional-scripts')
</body>
</html>