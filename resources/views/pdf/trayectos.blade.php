@extends('layout.layout_pdf')

@section('title', 'Trayecto')

@section('title_lista')
    TRAYECTOS
    @if(isset($pnfUser) && $pnfUser)
        - {{ strtoupper($pnfUser->nombre ?? $pnfUser->abreviado) }}
    @endif
@endsection

@section('table')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NOMBRE</th>
                <th>PNF</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trayectos as $i => $trayecto)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $trayecto->nombre }}</td>
                    {{-- mostrar todos los pnfs asociados al trayecto --}}
                    <td>
                        @foreach ($trayecto->pnfs as $pnf)
                            {{ $pnf->nombre }},
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No hay trayectos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
