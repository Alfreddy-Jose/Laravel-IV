@extends('layout.layout_pdf')

@section('title', 'Docentes')

@section('title_lista')
    DOCENTES
    @if(isset($pnfUser) && $pnfUser)
        - {{ strtoupper($pnfUser->nombre ?? $pnfUser->abreviado) }}
    @endif
@endsection

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>CÉDULA</th>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>CATEGORÍA</th>
                <th>DEDICACIÓN</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
                <th>GRADO</th>
                <th>HORAS</th>
                <th>PNF</th>
                <th>UNIDADES CURRICULARES</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($docentes as $i => $docente)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $docente->persona->cedula_persona ?? 'N/A' }}</td>
                    <td>{{ $docente->persona->nombre ?? 'N/A' }}</td>
                    <td>{{ $docente->persona->apellido ?? 'N/A' }}</td>
                    <td>{{ $docente->categoria ?? 'N/A' }}</td>
                    <td>{{ $docente->condicionContrato->dedicacion ?? 'N/A' }}</td>
                    <td>{{ $docente->condicionContrato->fecha_inicio ?? 'N/A' }}</td>
                    <td>{{ $docente->condicionContrato->fecha_fin ?? 'N/A' }}</td>
                    <td>{{ $docente->persona->grado_inst ?? 'N/A' }}</td>
                    <td>{{ $docente->horas_dedicacion }}</td>
                    <td>{{ $docente->pnf->nombre ?? 'N/A' }}</td>
                    {{-- mostrar todos las unidades curriculares --}}
                    <td>
                        @foreach ($docente->unidades_curriculares as $unidad)
                            {{ $unidad->nombre }},
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center;">No hay docentes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
