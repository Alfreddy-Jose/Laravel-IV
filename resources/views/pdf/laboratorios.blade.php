@extends('layout.layout_pdf')

@section('title', 'Laboratorios')

@section('title_lista', 'LABORATORIOS')

@section('table')
    <table>
        <thead>
            <th>#</th>
            <th>NOMBRE</th>
            <th>ETAPA</th>
            <th>ABREVIADO</th>
            <th>EQUIPOS</td>
            <th>SEDE</th>
        </thead>
        <tbody>
            @foreach ($laboratorios as $i => $lab)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $lab->nombre_aula }}</td>
                    <td>{{ $lab->etapa }}</td>
                    <td>{{ $lab->abreviado_lab }}</td>
                    <td>{{ $lab->equipos }}</td>
                    <td>{{ $lab->sede->nombre_sede }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection