@extends('layout.layout_pdf')

@section('title', 'Tipo Matricula')

@section('title_lista', 'TIPOS DE MATRICULAS')

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NÚMERO</th>
                <th>NOMBRE</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($matriculas as $i => $matricula)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $matricula->numero }}</td>
                    <td>{{ $matricula->nombre }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No hay tipos de matrícula registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
