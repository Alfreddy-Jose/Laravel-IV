@extends('layout.layout_pdf')

@section('title', 'Coordinador')

@section('title_lista')
    COORDINADORES
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
                <th>DOCENTE</th>
                <th>PNF</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($coordinadores as $i => $coordinador)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $coordinador->docente->persona->cedula_persona ?? 'N/A' }}</td>
                    <td>
                        {{ ($coordinador->docente->persona->nombre ?? '') . ' ' . ($coordinador->docente->persona->apellido ?? '') }}
                    </td>
                    <td>
                        {{ $coordinador->pnf->nombre ?? $coordinador->docente->pnf->nombre ?? 'N/A' }}
                    </td>
                    <td>{{ $coordinador->fecha_inicio?->format('d/m/Y') }}</td>
                    <td>{{ $coordinador->fecha_fin?->format('d/m/Y') ?? 'VIGENTE' }}</td>
                    <td>{{ $coordinador->activo ? 'ACTIVO' : 'INACTIVO' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No hay coordinadores registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
