@extends('layout.layout_pdf')

@section('title', 'Personas')

@section('title_lista', 'PERSONAS')

@section('table')
    <table>
        <thead>
            <th>#</th>
            <th>CÉDULA</th>
            <th>NOMBRE</th>
            <th>APELLIDO</th>
            <th>EMAIL</th>
            <th>TIPO</th>
            <th>GRADO</th>
            <th>MUNICIPIO</th>
            <th>DIRECCION</th>
        </thead>
        <tbody>
            @foreach ($personas as $i => $persona)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $persona->cedula_persona }}</td>
                    <td>{{ $persona->nombre }}</td>
                    <td>{{ $persona->apellido }}</td>
                    <td>{{ $persona->email }}</td>
                    <td>{{ $persona->tipo_persona }}</td>
                    <td>{{ $persona->grado_inst }}</td>
                    <td>{{ $persona->municipio->municipio }}</td>
                    <td>{{ $persona->direccion }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
