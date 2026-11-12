@extends('layout.layout_pdf')

@section('title', 'Reporte de Registros PER')

@section('title_lista')
    LISTADO DE REGISTROS PER
    @if(isset($pnfUser) && $pnfUser)
        - {{ strtoupper($pnfUser->nombre ?? $pnfUser->abreviado) }}
    @endif
@endsection

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>CÉDULA ESTUDIANTE</th>
                <th>ESTUDIANTE</th>
                <th>UNIDAD CURRICULAR</th>
                <th>DOCENTE</th>
                <th>CALIFICACIÓN</th>
                <th>LAPSO ACADÉMICO</th>
                <th>OBSERVACIONES</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $i => $item)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center;">
                        {{ $item->estudiante->persona->cedula_persona ?? 'N/A' }}
                    </td>
                    <td>
                        {{ ($item->estudiante->persona->nombre ?? '') . ' ' . ($item->estudiante->persona->apellido ?? '') }}
                    </td>
                    <td>
                        {{ $item->unidadCurricular->nombre ?? 'N/A' }}
                    </td>
                    <td>
                        @if ($item->docente && $item->docente->persona)
                            {{ 'V-' . $item->docente->persona->cedula_persona . ' - ' . $item->docente->persona->nombre . ' ' . $item->docente->persona->apellido }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $item->calificacion ?? 'S/C' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $item->lapsoAcademico->nombre_lapso ?? 'N/A' }}
                    </td>
                    <td>{{ $item->observaciones ?? 'S/O' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No hay registros PER encontrados para este filtro.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection