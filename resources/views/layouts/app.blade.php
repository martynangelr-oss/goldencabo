<!DOCTYPE html>
<html lang="es" id="gc-html">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Golden Cabo Transportation — Los Cabos')</title>
<meta name="description" content="Traslados privados de lujo desde y hacia el Aeropuerto Internacional de Los Cabos. Tours exclusivos a La Paz y Todos Santos.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;1,300&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@yield('content')
<script>
  window.APP_URL     = '{{ url('/') }}';
  window.CSRF_TOKEN  = '{{ csrf_token() }}';
  window.BOOKING_API = '{{ url("/api/bookings") }}';
  window.CONTACT_API = '{{ url("/api/contact") }}';
</script>
<script src="{{ asset('js/i18n.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
