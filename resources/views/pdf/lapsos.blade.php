@extends('layout.layout_pdf')

@section('title', 'Lapso Académico')

@section('title_lista', 'LAPSOS ACADÉMICOS')


@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NOMBRE</th>
                <th>AÑO</th>
                <th>TIPO DE LAPSO</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lapsos as $i => $lapso)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $lapso->nombre_lapso }}</td>
                    <td>{{ $lapso->anoAcademico->ano ?? 'N/A' }}</td>
                    <td>{{ $lapso->tipoLapso->nombre ?? 'N/A' }}</td>
                    {{-- Las fechas pueden heredarse del año académico padre --}}
                    <td>{{ $lapso->fecha_inicio?->format('d/m/Y') ?? $lapso->anoAcademico?->fecha_inicio?->format('d/m/Y') }}</td>
                    <td>{{ $lapso->fecha_fin?->format('d/m/Y') ?? $lapso->anoAcademico?->fecha_fin?->format('d/m/Y') }}</td>
                    <td>{{ strtoupper($lapso->estado) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No hay lapsos académicos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
