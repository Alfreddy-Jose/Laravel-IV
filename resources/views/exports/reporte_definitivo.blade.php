<table style="border-collapse: collapse;">
    <!-- Filas en blanco para dar espacio a la cabecera (Filas 1 a 4) -->
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    <!-- Fila 5: Título principal -->
    <tr>
        <td></td><!-- Col A -->
        <td></td><!-- Col B -->
        <td></td><!-- Col C -->
        <td colspan="4" style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">PROYECCIÓN DE SECCIONES - LAPSO ACADEMICO {{ $anoProyectado }}</td>
    </tr>

    @foreach($trayectosAgrupados as $tDestinoStr => $trayecto)

        <!-- Sub-encabezado de sección origen/destino (se repite por trayecto) -->
        <tr>
            <td></td><!-- Col A -->
            <td></td><!-- Col B -->
            <td></td><!-- Col C -->
            <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #F2F2F2; text-align: center; vertical-align: middle; border: 1px solid #000000;">SECCION ORIGEN</td>
            <td colspan="2" style="font-family: Calibri, sans-serif; font-size: 10px; font-weight: bold; background-color: #F2F2F2; text-align: center; vertical-align: middle; border: 1px solid #000000;">SECCION DESTINO</td>
        </tr>

        <!-- Encabezados de tabla (se repite por trayecto) -->
        <tr>
            <th></th><!-- Col A -->
            <th style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">SEDE</th>
            <th style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">TRAYECTO</th>
            <th style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">DEL {{ $anoOrigenHistorico }}</th>
            <th style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">VAN</th>
            <th style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">PARA EL {{ $anoProyectado }}</th>
            <th style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; background-color: #B4C6E7; border: 1px solid #000000; text-align: center; vertical-align: middle;">TOTAL SECCIONES</th>
        </tr>

        @foreach($trayecto['destinos'] as $grupo)
            @php
                $origenes          = $grupo['origenes'];
                $totalFilasBloque  = max(count($origenes), 1) + 2; // origenes + repitientes + reincorporacion
            @endphp

            <!-- Filas de Orígenes -->
            @if(count($origenes) > 0)
                @foreach($origenes as $i => $origen)
                    <tr>
                        <td></td><!-- Col A -->
                        @if($i === 0 && $grupo['mostrar_total'])
                            {{-- SEDE rowspan abarca todas las filas de esta sede en el trayecto --}}
                            <td rowspan="{{ $grupo['total_rowspan'] }}" style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $grupo['sede_nombre'] }}</td>
                            {{-- TRAYECTO rowspan igual --}}
                            <td rowspan="{{ $grupo['total_rowspan'] }}" style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trayecto['nombre_romano'] }}</td>
                        @elseif($i !== 0)
                            {{-- Celdas B y C cubiertas por rowspan, no se emiten --}}
                        @elseif($i === 0 && !$grupo['mostrar_total'])
                            {{-- Celdas B y C cubiertas por rowspan de la primera sección de la sede --}}
                        @endif
                        <!-- Col D: Sección Origen -->
                        <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $origen['seccion'] }}</td>
                        <!-- Col E: VAN -->
                        <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $origen['van_si'] ? '__VAN_SI__' : '__VAN_NO__' }}</td>
                        @if($i === 0)
                            {{-- Col F: Sección Destino — rowspan cubre todo el bloque de esta sección --}}
                            <td rowspan="{{ $totalFilasBloque }}" style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $grupo['seccion_destino'] }}</td>
                            @if($grupo['mostrar_total_trayecto'])
                                {{-- Col G: Total Secciones — rowspan cubre todas las filas del trayecto completo --}}
                                <td rowspan="{{ $trayecto['total_rowspan_trayecto'] }}" style="font-family: Calibri, sans-serif; font-size: 14px; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trayecto['total_secciones_trayecto'] }}</td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            @else
                {{-- Sin orígenes: emitir fila placeholder para mantener la estructura del bloque --}}
                <tr>
                    <td></td><!-- Col A -->
                    @if($grupo['mostrar_total'])
                        <td rowspan="{{ $grupo['total_rowspan'] }}" style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $grupo['sede_nombre'] }}</td>
                        <td rowspan="{{ $grupo['total_rowspan'] }}" style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trayecto['nombre_romano'] }}</td>
                    @endif
                    <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;"></td>
                    <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;"></td>
                    <td rowspan="{{ $totalFilasBloque }}" style="font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $grupo['seccion_destino'] }}</td>
                    @if($grupo['mostrar_total_trayecto'])
                        <td rowspan="{{ $trayecto['total_rowspan_trayecto'] }}" style="font-family: Calibri, sans-serif; font-size: 14px; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trayecto['total_secciones_trayecto'] }}</td>
                    @endif
                </tr>
            @endif

            {{-- Fila: REPITIENTES — Solo emite Col A (spacer), D (texto), E (VAN).
                 Cols B, C, F, G ya están cubiertas por los rowspan anteriores. --}}
            <tr>
                <td></td><!-- Col A spacer -->
                <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">REPITIENTES {{ $grupo['seccion_destino'] }}</td>
                <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">__VAN_SI__</td>
            </tr>

            {{-- Fila: REINCORPORACION — Solo emite Col A (spacer), D (texto), E (VAN).
                 Cols B, C, F, G ya están cubiertas por los rowspan anteriores. --}}
            <tr>
                <td></td><!-- Col A spacer -->
                <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">REINCORPORACION</td>
                <td style="font-family: Calibri, sans-serif; font-size: 11px; border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $grupo['tiene_reincorporados'] ? '__VAN_SI__' : '__VAN_NO__' }}</td>
            </tr>
        @endforeach

        <!-- Fila vacía separadora entre trayectos -->
        <tr><td colspan="7"></td></tr>

    @endforeach
</table>
