@extends('layout.layout_pdf')

@section('title', 'Sección')

@section('title_lista', 'SECCIONES')

@section('table')
            <table class="table text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NOMBRE</th>
                    <th>PNF</th>
                    <th>MATRICULA</th>
                    <th>TRAYECTO</th>
                    <th>SEDE</th>
                    <th>LAPSO</th>
                    <th>N° SECCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($secciones as $i => $s)
                    <tr>
                        <td class="td">{{ $i + 1 }}</td>
                        <td class="td">{{ $s->nombre }}</td>
                        <td class="td">{{ $s->pnf->nombre ?? '' }}</td>
                        <td class="td">{{ $s->matricula->nombre ?? '' }}</td>
                        <td class="td">{{ $s->trayecto->nombre ?? '' }}</td>
                        <td class="td">{{ $s->sede->nombre_sede ?? '' }}</td>
                        <td class="td">{{ $s->lapso->ano ?? '' }}</td>
                        <td class="td">{{ $s->numero_seccion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endsection


