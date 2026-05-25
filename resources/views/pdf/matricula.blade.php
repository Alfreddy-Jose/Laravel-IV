@extends('layout.layout_pdf')

@section('tilte', 'Tipo Matricula')

@section('title_lista', 'TIPOS DE MATRICULAS')

@section('table')
    <table>
        <thead>
            <th>#</th>
            <th>NÚMERO</th>
            <th>NOMBRE</th>
        </thead>
        <tbody>
            @foreach ($matriculas as $i => $matricula)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $matricula->numero }}</td>
                    <td>{{ $matricula->nombre }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection