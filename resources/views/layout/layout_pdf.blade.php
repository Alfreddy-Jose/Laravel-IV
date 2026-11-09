<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'PDF')</title>

    {{-- importar estilos para la tabla --}}
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #e3f2fd;
            font-weight: bold;
        }

        .header-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .header-main {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .logo-container {
            text-align: center;
            width: 100%;
            margin: 10px 0;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
        }
    </style>

    @stack('stilos')

</head>

<body>
    <div class="logo-container">
        <img src="{{ public_path('images/cabecera exel.png') }}" alt="Logo de la institución." class="logo-img">
    </div>
    
    <div class="header-section">
        <div class="header-main">LISTADO DE @yield('title_lista')</div>
    </div>

    @yield('table')

</body>

</html>