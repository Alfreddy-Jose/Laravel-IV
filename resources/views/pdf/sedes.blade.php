@extends('layout.layout_pdf')

@section('title', 'Sede')

@section('title_lista', 'SEDES')

@section('table')
    <table>
        <tr>
            <th>#</th>
            <th>NÚMERO SEDE</th>
            <th>NOMBRE</th>
            <th>ABREVIADO</th>
            <th>MUNICIPIO</th>
            <th>DIRECCION</th>
        </tr>
        @foreach ($sedes as $i => $sede)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $sede->nro_sede }}</td>
                <td>{{ $sede->nombre_sede }}</td>
                <td>{{ $sede->nombre_abreviado }}</td>
                {{-- mostrar municipio en mayusculas --}}
                <td>{{ strtoupper($sede->municipio->municipio) }}</td>
                <td>{{ $sede->direccion }}</td>
            </tr>
        @endforeach
    </table>
@endsection
