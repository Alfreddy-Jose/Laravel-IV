<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Horario Académico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #e3f2fd;
            font-weight: bold;
        }
        td {
            font-size: 10px;
        }

        .evento {
            font-size: 9px;
            line-height: 1.2;
            overflow: hidden;
            padding: 2px;
            margin: 1px;
            border-radius: 2px;
            text-align: center;
        }

        .header-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .header-main {
            font-size: 14px;
            font-weight: bold;
        }

        .header-sub {
            font-size: 12px;
        }

        .info-section {
            font-size: 10px;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .materia {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .docente {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .aula {
            font-size: 10px;
            font-style: italic;
        }
    </style>
</head>

<body>
    {{-- Logo --}}
    <div style="display: flex; justify-content: center; align-items: center; margin: 10px 0;">
        <img src="{{ public_path('img/PDF.jpg') }}" alt="Logo de la institución." style="max-width: 100%; margin: 0 8px;">
    </div>

    {{-- Encabezado --}}
    <div class="header-section">
        <div class="header-main">
            COORDINACIÓN DEL {{$encabezado['pnf'] ?? 'SIN PNF'}}
        </div>
        <div class="header-sub">
            SEDE: {{ $encabezado['sede'] ?? 'SEDE CENTRAL (UPTYAB)' }}
        </div>
    </div>

    {{-- Info adicional --}}
    <div class="info-section">
        TRAYECTO: {{ $encabezado['trayecto'] ?? '' }} | 
        TRIMESTRE: {{ $encabezado['trimestre'] ?? '' }} | 
        SECCIÓN: {{ $encabezado['seccion'] ?? '' }} | 
        LAPSO: {{ $encabezado['lapso'] ?? '' }}
        
        {{-- Laboratorios --}}
        @if (!empty($encabezado['laboratorios']) && count($encabezado['laboratorios']) > 0)
            <br>
            <strong>LABORATORIOS:</strong><br>
            @foreach ($encabezado['laboratorios'] as $laboratorio)
                • {{ $laboratorio }}<br>
            @endforeach
        @endif
    </div>

    {{-- Tabla --}}
    <table>
        <thead>
            <tr>
                <th style="width: 15%">HORA</th>
                @foreach ($dias as $dia)
                    <th>{{ $dia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bloques as $i => $bloque)
                <tr>
                    <td>{{ $bloque['rango'] }}</td>
                    @foreach ($dias as $dia)
                        @php
                            // Buscar si hay un evento que inicia en este bloque y día
                            $evento = collect($eventos)->first(function($e) use ($dia, $i) {
                                return $e['dia'] == $dia && $e['bloque_inicio'] == ($i + 1);
                            });
                            // Verificar si este bloque está cubierto por un evento que empezó antes (para no repetir la celda)
                            $eventoEnCurso = collect($eventos)->first(function($e) use ($dia, $i) {
                                return $e['dia'] == $dia && $e['bloque_inicio'] < ($i + 1) && $e['bloque_fin'] >= ($i + 1);
                            });
                        @endphp

                        @if ($evento)
                            <td rowspan="{{ $evento['bloque_fin'] - $evento['bloque_inicio'] + 1 }}"
                                style="vertical-align: middle; text-align: center; background-color: {{ $evento['color'] ?? '#e3f2fd' }};">
                                <div class="evento">
                                    <div class="materia">
                                        {{ $evento['materia'] ?? 'Sin materia' }}
                                    </div>
                                    <div class="docente">
                                        {{ $evento['docente'] ?? 'Sin docente' }}
                                    </div>
                                    <div class="aula">
                                        {{ $evento['aula'] ?? 'Sin aula' }}
                                    </div>
                                </div>
                            </td>
                        @elseif ($eventoEnCurso)
                            {{-- No renderizar celda, ya que está cubierta por un rowspan --}}
                        @else
                            {{-- Celda vacía normal --}}
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pie --}}
    <div style="margin-top: 20px; text-align: right; font-size: 9px;">
        ______________________________________ <br> <br>
        COORDINACIÓN DEL {{$encabezado['pnf_abreviado'] ?? ''}}
    </div>
</body>
</html>