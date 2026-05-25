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
            </tr>
        </thead>
        <tbody>
            @foreach ($lapsos as $i => $lapso)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $lapso->nombre_lapso }}</td>
                    <td>{{ $lapso->ano }}</td>
                    <td>{{ $lapso->tipolapso->nombre }}</td>
                    <td>{{ $lapso->fecha_inicio }}</td>
                    <td>{{ $lapso->fecha_fin }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
