@extends('layout.layout_pdf')

@section('title', 'Personas')

@section('title_lista', 'PERSONAS')

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>CÉDULA</th>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>EMAIL</th>
                <th>TELÉFONO</th>
                <th>TIPO</th>
                <th>GRADO</th>
                <th>MUNICIPIO</th>
                <th>DIRECCION</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($personas as $i => $persona)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $persona->cedula_persona }}</td>
                    <td>{{ $persona->nombre }}</td>
                    <td>{{ $persona->apellido }}</td>
                    <td>{{ $persona->email }}</td>
                    <td>{{ $persona->telefono ?? 'N/A' }}</td>
                    <td>{{ $persona->tipo_persona }}</td>
                    <td>{{ $persona->grado_inst }}</td>
                    <td>{{ $persona->municipio->municipio ?? 'N/A' }}</td>
                    <td>{{ $persona->direccion ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">No hay personas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
