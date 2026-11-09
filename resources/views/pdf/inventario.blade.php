@extends('layout.layout_pdf')

@section('title', 'Reporte de Inventario')

@section('title_lista')
    REPORTE DE INVENTARIO DE BIENES Y EQUIPOS
    @if(isset($pnfUser) && $pnfUser)
        - {{ strtoupper($pnfUser->nombre ?? $pnfUser->abreviado) }}
    @endif
@endsection

@section('table')
    <style>
        .img-bien {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>IMAGEN</th>
                <th>CÓDIGO ÚNICO</th>
                <th>NOMBRE</th>
                <th>DESCRIPCIÓN</th>
                <th>PNF</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bienes as $i => $bien)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center;">
                        @php
                            // Busca la ruta física completa dentro de storage/app/public/
                            $storagePath = !empty($bien->imagen) ? storage_path('app/public/' . $bien->imagen) : null;
                            
                            // Ruta alternativa usando el enlace simbólico en public/storage/
                            $publicStoragePath = !empty($bien->imagen) ? public_path('storage/' . $bien->imagen) : null;

                            $base64Image = null;

                            if ($storagePath && file_exists($storagePath)) {
                                $type = pathinfo($storagePath, PATHINFO_EXTENSION);
                                $data = file_get_contents($storagePath);
                                $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            } elseif ($publicStoragePath && file_exists($publicStoragePath)) {
                                $type = pathinfo($publicStoragePath, PATHINFO_EXTENSION);
                                $data = file_get_contents($publicStoragePath);
                                $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp

                        @if ($base64Image)
                            <img src="{{ $base64Image }}" class="img-bien" alt="Bien">
                        @elseif (!empty($bien->imagen) && filter_var($bien->imagen, FILTER_VALIDATE_URL))
                            <img src="{{ $bien->imagen }}" class="img-bien" alt="Bien">
                        @else
                            <span style="font-size: 10px; color: #888;">Sin imagen</span>
                        @endif
                    </td>
                    <td>{{ $bien->codigo_uni ?? 'S/C' }}</td>
                    <td>{{ $bien->nombre ?? 'N/A' }}</td>
                    <td>{{ $bien->descripcion ?? 'Sin descripción' }}</td>
                    <td>
                        {{ $bien->pnf->nombre ?? $bien->pnf->abreviado ?? 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No hay bienes registrados en el inventario.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection