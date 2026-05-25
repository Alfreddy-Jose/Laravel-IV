@extends('layout.layout_pdf')

@section('title', 'Docentes')

@section('title_lista', 'DOCENTES')

@section('table')
    <table>
        <thead>
            <th>#</th>
            <th>CÉDULA</th>
            <th>NOMBRE</th>
            <th>APELLIDO</th>
            <th>DEDICACIÓN</th>
            <th>FECHA INICIO</th>
            <th>FECHA FIN</th>
            <th>GRADO</th>
            <th>HORAS</th>
            <th>UNIDADES CURRICULARES</th>
        </thead>
        <tbody>
            @foreach ($docentes as $i => $docente)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $docente->persona->cedula_persona }}</td>
                    <td>{{ $docente->persona->nombre }}</td>
                    <td>{{ $docente->persona->apellido }}</td>
                    <td>{{ $docente->condicionContrato->dedicacion }}</td>
                    <td>{{ $docente->condicionContrato->fecha_inicio }}</td>
                    <td>{{ $docente->condicionContrato->fecha_fin }}</td>
                    <td>{{ $docente->persona->grado_inst }}</td>
                    <td>{{ $docente->horas_dedicacion }}</td>
                    {{-- mostrar todos las unidades curriculares --}}
                    <td>
                        @foreach ($docente->unidades_curriculares as $unidad)
                            {{ $unidad->nombre }}, 
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection