<table style="border-collapse: collapse;">
    <!-- Filas en blanco para dar espacio a la cabecera (Filas 1 a 4) -->
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    <!-- Fila 5: Título del lapso destino -->
    <tr>
        <td></td><!-- Col A -->
        <td></td><!-- Col B -->
        <td></td><!-- Col C -->
        <td colspan="8" style="font-family: Calibri, sans-serif; font-size: 12px; font-weight: bold; background-color: #C6EFCE; border: 1px solid #000000; text-align: center; vertical-align: middle;">PARA EL LAPSO {{ $anoProyectado }}</td>
    </tr>

    @foreach($trayectosDestino as $tDestinoNum => $trayecto)
        @php
            $tOrigenNombre  = $trayecto['trayecto_origen_nombre'];  // PIU, I, II...
            $tDestinoNombre = $trayecto['trayecto_destino_nombre']; // I, II, III...
            // Agrupar secciones por sede para el rowspan correcto de SEDE y TRAYECTO
            $seccionesPorSede = [];
            foreach ($trayecto['secciones'] as $key => $sec) {
                $seccionesPorSede[$sec['sede_nombre']][] = $sec;
            }
        @endphp

        <!-- Fila de encabezado sub-grupos de columnas -->
        <tr>
            <td></td><!-- Col A -->
            <td></td><!-- Col B: SEDE -->
            <td></td><!-- Col C: TRAYECTO -->
            <td></td><!-- Col D: SECCION -->
            <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">APROBADOS TRAYECTO {{ $tOrigenNombre }}</td>
            <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">REPROBADOS TRAYECTO {{ $tDestinoNombre }}</td>
            <td style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FFF2CC; border: 1px solid #000000; text-align: center; vertical-align: middle;">TOTAL</td>
            <td></td><!-- Col J: CREAR -->
        </tr>

        <!-- Fila de encabezados de columna -->
        <tr>
            <th></th><!-- Col A -->
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">SEDE</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">TRAYECTO</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">SECCION</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">DEL {{ $anoOrigenHistorico }}</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">NRO. ESTUD.</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">DEL {{ $anoOrigenHistorico }}</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">NRO. ESTUD.</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FFF2CC; border: 1px solid #000000; text-align: center; vertical-align: middle;">APROXIMADO</th>
            <th style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">CREAR</th>
        </tr>

        <!-- Filas de datos por sede -->
        @foreach($seccionesPorSede as $sedeNombre => $secciones)
            @php $sedeRowspan = count($secciones); @endphp
            @foreach($secciones as $si => $sec)
                @php
                    // Primera sección de origen aprobada
                    $primeraAprobada  = $sec['aprobados'][0]  ?? null;
                    $primeraReprobada = $sec['reprobados'][0] ?? null;
                    // Calcular máximo de filas para rowspan de SECCION, APROBADO_TOTAL, APROX, CREAR
                    $maxFilas = max(count($sec['aprobados']), count($sec['reprobados']), 1);
                @endphp

                @for($fi = 0; $fi < $maxFilas; $fi++)
                    @php
                        $aprobFila  = $sec['aprobados'][$fi]  ?? null;
                        $reprobFila = $sec['reprobados'][$fi] ?? null;
                    @endphp
                    <tr>
                        <td></td><!-- Col A -->

                        @if($fi === 0 && $si === 0)
                            <!-- Sede -->
                            <td rowspan="{{ $sedeRowspan * $maxFilas }}" style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sedeNombre }}</td>
                            <!-- Trayecto -->
                            <td rowspan="{{ $sedeRowspan * $maxFilas }}" style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $tDestinoNombre }}</td>
                        @elseif($fi === 0 && $si > 0)
                            <!-- Cuando cambia la sección dentro de la misma sede, ya no renderizamos SEDE/TRAYECTO -->
                        @endif

                        @if($fi === 0)
                            <!-- SECCION DESTINO (solo en primera fila de cada sección) -->
                            <td rowspan="{{ $maxFilas }}" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['seccion_destino'] }}</td>
                        @endif

                        <!-- Aprobados (sección origen) -->
                        <td style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $aprobFila ? $aprobFila['seccion'] : '' }}</td>
                        <td style="font-family: Calibri, sans-serif; font-size: 10px; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $aprobFila ? $aprobFila['cantidad'] : '' }}</td>

                        <!-- Reprobados (sección que se queda) -->
                        <td style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $reprobFila ? $reprobFila['seccion'] : '' }}</td>
                        <td style="font-family: Calibri, sans-serif; font-size: 10px; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $reprobFila ? $reprobFila['cantidad'] : '' }}</td>

                        @if($fi === 0)
                            <!-- TOTAL (solo en primera fila de cada sección) -->
                            <td rowspan="{{ $maxFilas }}" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FFF2CC; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['total'] }}</td>
                            <!-- CREAR (marcador) -->
                            <td rowspan="{{ $maxFilas }}" style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['crear_seccion'] ? '__CREAR_SI__' : '__CREAR_NO__' }}</td>
                        @endif
                    </tr>
                @endfor
            @endforeach
        @endforeach

        <!-- Fila vacía separadora entre trayectos -->
        <tr>
            <td colspan="11"></td>
        </tr>
    @endforeach

    <!-- Filas de leyenda -->
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="7" style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; vertical-align: middle;">NUMERO DE ESTUDIANTES QUE AVANZAN DEL LAPSO ANTERIOR</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="7" style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; vertical-align: middle;">NUMERO DE ESTUDIANTES QUE SE QUEDAN DEL LAPSO ANTERIOR</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; background-color: #FFF2CC; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="7" style="font-family: Calibri, sans-serif; font-size: 10px; border: 1px solid #000000; vertical-align: middle;">NUMERO TOTAL DE ESTUDIANTES APROXIMADO SEGUN SEA LA SUMATORIA</td>
        <td colspan="2"></td>
    </tr>
</table>
