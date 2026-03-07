<meta charset="utf-8" />
<title>{{ $title }} | SATA-QR - UGEL Huacaybamba</title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta
    content="Sistema de Alerta Temprana y Control de Asistencia mediante códigos QR para la prevención de la deserción escolar en la UGEL Huacaybamba."
    name="description" />
<meta content="UGEL Huacaybamba" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- 1. IDENTIDAD DE APLICACIÓN (PWA) -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SATA-QR">
<meta name="application-name" content="SATA-QR">
<meta name="theme-color" content="#1e3a8a"> {{-- Azul Institucional --}}

<!-- 2. OPEN GRAPH / REDES SOCIALES (WhatsApp, Slack, etc) -->
<meta property="og:type" content="website" />
<meta property="og:title" content="{{ $title }} | SATA-QR - UGEL Huacaybamba" />
<meta property="og:description"
    content="Protegiendo el futuro de nuestros estudiantes. Control de asistencia inteligente y alertas de deserción." />
<meta property="og:image" content="{{ asset('images/logo-ugel.png') }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:site_name" content="SATA-QR" />
<meta property="og:locale" content="es_PE" />

<!-- 2.1. IDIOMA / REGIÓN -->
<meta http-equiv="content-language" content="es-PE" />

<!-- 3. TWITTER CARDS -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="SATA-QR | UGEL Huacaybamba">
<meta name="twitter:description" content="Sistema de Prevención de Deserción Escolar mediante QR.">
<meta name="twitter:image" content="{{ asset('images/logo-ugel.png') }}">

<!-- App favicon -->
<link href="{{ asset('images/logo-ugel.png') }}" rel="shortcut icon" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo-ugel.png') }}">
