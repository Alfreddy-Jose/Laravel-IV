@extends('layout.layout_pdf')

@section('title', 'Vocero')

@section('title_lista')
    VOCEROS
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
                <th>TIPO VOCERO</th>
                <th>SECCIÓN</th>
                <th>PNF</th>
                <th>LAPSO ACADÉMICO</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($voceros as $i => $vocero)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center;">
                        {{ $vocero->estudiante->persona->cedula_persona ?? 'N/A' }}
                    </td>
                    <td>
                        {{ ($vocero->estudiante->persona->nombre ?? '') . ' ' . ($vocero->estudiante->persona->apellido ?? '') }}
                    </td>
                    <td style="text-align: center;">{{ $vocero->tipo_vocero ?? 'N/A' }}</td>
                    <td>{{ $vocero->seccion->nombre ?? 'N/A' }}</td>
                    <td>{{ $vocero->seccion->pnf->nombre ?? 'N/A' }}</td>
                    <td style="text-align: center;">
                        {{ $vocero->seccion->lapso->nombre_lapso ?? 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No hay voceros registrados para este filtro.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
