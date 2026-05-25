@extends('layout.layout_pdf')

@section('title', 'PNF')

@section('title_lista', 'PNF')

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NOMBRE</th>
                <th>CÓDIGO</th>
                <th>ABREVIADO</th>
                <th>ABREVIADO COORDINACIÓN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pnfs as $i => $pnf)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pnf->nombre }}</td>
                    <td>{{ $pnf->codigo }}</td>
                    <td>{{ $pnf->abreviado }}</td>
                    <td>{{ $pnf->abreviado_coord }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
