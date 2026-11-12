@extends('layout.layout_pdf')

@section('title', 'Año Académico')

@section('title_lista', 'AÑOS ACADÉMICOS')

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>AÑO</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
                <th>ESTADO</th>
                <th>LAPSOS ASOCIADOS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($anos as $i => $ano)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $ano->ano }}</td>
                    <td>{{ $ano->fecha_inicio?->format('d/m/Y') }}</td>
                    <td>{{ $ano->fecha_fin?->format('d/m/Y') }}</td>
                    <td>{{ strtoupper($ano->estado_real ?? $ano->status) }}</td>
                    <td>
                        @foreach ($ano->lapsos as $lapso)
                            {{ $lapso->nombre_lapso }},
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No hay años académicos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
