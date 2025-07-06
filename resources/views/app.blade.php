<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'DocraTech') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Meta tags -->
    <meta name="description" content="DocraTech - Modern Medical Website Platform">
    <meta name="keywords" content="medical, healthcare, platform, management">
    <meta name="author" content="DocraTech">
    
    <!-- Open Graph -->
    <meta property="og:title" content="DocraTech">
    <meta property="og:description" content="Modern Medical Website Platform">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    
    <!-- Additional styles -->
    <style>
        /* Critical CSS for initial load */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(90, 17, 136, 0.3);
            border-radius: 50%;
            border-top-color: #5A1188;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="h-full bg-docratech-background">
    <!-- Loading screen -->
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-docratech-background">
        <div class="text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <h2 class="text-xl font-semibold text-docratech-primary">Loading DocraTech...</h2>
            <p class="text-docratech-textSecondary">Please wait while we initialize the application</p>
        </div>
    </div>
    
    <!-- App container -->
    <div id="app" class="h-full"></div>
    
    <!-- Scripts -->
    <script>
        // Hide loading screen when app is ready
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.opacity = '0';
                    setTimeout(function() {
                        loadingScreen.style.display = 'none';
                    }, 300);
                }
            }, 1000);
        });
        
        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });
        
        // Global unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
        });
    </script>
</body>
</html> 