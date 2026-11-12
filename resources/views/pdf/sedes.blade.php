@extends('layout.layout_pdf')

@section('title', 'Sede')

@section('title_lista')
    SEDES
    @if(isset($pnfUser) && $pnfUser)
        - {{ strtoupper($pnfUser->nombre ?? $pnfUser->abreviado) }}
    @endif
@endsection

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NÚMERO SEDE</th>
                <th>NOMBRE</th>
                <th>ABREVIADO</th>
                <th>MUNICIPIO</th>
                <th>DIRECCION</th>
                <th>PNF</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sedes as $i => $sede)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $sede->nro_sede }}</td>
                    <td>{{ $sede->nombre_sede }}</td>
                    <td>{{ $sede->nombre_abreviado }}</td>
                    {{-- mostrar municipio en mayusculas --}}
                    <td>{{ strtoupper($sede->municipio->municipio ?? 'N/A') }}</td>
                    <td>{{ $sede->direccion }}</td>
                    {{-- mostrar todos los pnfs asociados a la sede --}}
                    <td>
                        @foreach ($sede->pnfs as $pnf)
                            {{ $pnf->nombre }},
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No hay sedes registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
