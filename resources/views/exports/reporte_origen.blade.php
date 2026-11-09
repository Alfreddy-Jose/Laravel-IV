<table style="border-collapse: collapse;">
    <!-- Filas en blanco para dar espacio a la cabecera (Filas 1 a 4) -->
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    <!-- Fila 5: Título del lapso anterior -->
    <tr>
        <td></td> <!-- Col A: Spacer -->
        <td></td> <!-- Col B -->
        <td></td> <!-- Col C -->
        <td colspan="5" style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">VIENEN DEL LAPSO {{ $anoOrigenHistorico }}</td>
    </tr>

    <!-- Iteramos sobre los Trayectos (Ya ordenados: PIU, luego I, II...) -->
    @foreach($trayectos as $tNum => $trayecto)
        <!-- CABECERAS: Se imprimen de nuevo por cada trayecto -->
        <tr>
            <th></th> <!-- Col A: Spacer -->
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">SEDE</th>
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">TRAYECTO</th>
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">SECCION</th>
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">GRUPOS APROB.</th>
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">NRO. ESTUD.</th>
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">GRUPOS REPROB.</th>
            <th style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">NRO. ESTUD.</th>
        </tr>

        <!-- Iteramos sobre las sedes de ese trayecto -->
        @foreach($trayecto['sedes'] as $sedeNombre => $secciones)
            @php
                $rowspan = count($secciones);
            @endphp

            <!-- Iteramos sobre las secciones de esa sede -->
            @foreach($secciones as $sec)
                <tr>
                    <td></td> <!-- Col A: Spacer -->

                    <!-- Solo se imprime la Sede y Trayecto en la 1ra vuelta con rowspan -->
                    @if($loop->first)
                        <td rowspan="{{ $rowspan }}" style="font-family: 'Calibri', sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sedeNombre }}</td>
                        <td rowspan="{{ $rowspan }}" style="font-family: 'Calibri', sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trayecto['nombre_trayecto'] }}</td>
                    @endif

                    <!-- DATOS REGULARES DE LA SECCIÓN (Calculados x5 en el Backend) -->
                    <td style="font-family: 'Calibri', sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['seccion'] }}</td>
                    <td style="font-family: 'Calibri', sans-serif; font-size: 11px; font-weight: bold; background-color: #00B050; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['g_aprob'] }}</td>
                    <td style="font-family: 'Calibri', sans-serif; font-size: 11px; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['e_aprob'] }}</td>
                    <td style="font-family: 'Calibri', sans-serif; font-size: 11px; background-color: #D9E1F2; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['g_reprob'] }}</td>
                    <td style="font-family: 'Calibri', sans-serif; font-size: 11px; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $sec['e_reprob'] }}</td>
                </tr>
            @endforeach
        @endforeach
    @endforeach
    
    <!-- Añadir leyendas al final -->
    <tr><td colspan="8"></td></tr>
    
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: normal; color: black; text-align: center; vertical-align: middle;">IUNAV</td>
        <td colspan="5" style="font-family: Calibri, sans-serif; font-size: 10px; vertical-align: middle;">SU INFORMACION NO ES RELEVANTE PORQUE SU MATRICULA DEPENDE DE SU INSCRIPCION PAGA PORQUE ES UNA UNIVERSIDAD PRIVADA</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #00B050; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="5" style="font-family: Calibri, sans-serif; font-size: 10px; vertical-align: middle;">NUMERO DE PROYECTO APROBADOS (INFORMACION DEL DOCENTE DE PROYECTO)</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #C6E0B4; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="5" style="font-family: Calibri, sans-serif; font-size: 10px; vertical-align: middle;">SE MULTIPLICA EL NUMERO DE PROYECTO APROBADO POR 5</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #D9E1F2; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="5" style="font-family: Calibri, sans-serif; font-size: 10px; vertical-align: middle;">NUMERO DE PROYECTO REPROBADOS (INFORMACION DEL DOCENTE DE PROYECTO)</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #FCE4D6; border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="5" style="font-family: Calibri, sans-serif; font-size: 10px; vertical-align: middle;">SE MULTIPLICA EL NUMERO DE PROYECTO REPROBADO POR 5</td>
    </tr>
</table>
