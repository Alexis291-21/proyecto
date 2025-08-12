@extends('main')

@section('title')
    Reportes
@endsection

@section('content')
<div class="container mx-auto p-6 -mt-8">
    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-white shadow-md rounded-lg p-4">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-800">📊 Reportes de Citas Médicas</h1>
        <div class="flex space-x-4 mt-4 md:mt-0">
            {{-- Botón Detalle Confirmadas --}}
            <button onclick="mostrarModalConfirmadas()"
                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-sm transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm">Detalle Confirmadas</span>
            </button>

            {{-- Botón Detalle Canceladas --}}
            <button onclick="mostrarModalCanceladas()"
                    class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-sm transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm">Detalle Canceladas</span>
            </button>
        </div>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
        <div class="bg-white rounded-lg shadow-md p-5 flex items-center">
            <div class="flex-1">
                <h2 class="text-sm font-medium text-gray-600 uppercase">Citas Confirmadas</h2>
                <p class="text-2xl font-semibold text-green-600 mt-1">{{ number_format($porcentajeConfirmadas ?? 0, 2) }}%</p>
            </div>
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-5 flex items-center">
            <div class="flex-1">
                <h2 class="text-sm font-medium text-gray-600 uppercase">Citas Canceladas</h2>
                <p class="text-2xl font-semibold text-red-600 mt-1">{{ number_format($porcentajeCanceladas ?? 0, 2) }}%</p>
            </div>
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Gráfico Comparativo --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Comparativa Confirmadas vs Canceladas</h3>
        <canvas id="totalesChart" class="w-full h-40"></canvas>
    </div>
</div>

{{-- Modal Confirmadas --}}
<dialog id="modalConfirmadas" class="rounded-lg w-full max-w-3xl">
    <div class="relative bg-white p-6 rounded-lg shadow-xl">
        <button onclick="document.getElementById('modalConfirmadas').close()"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition">
            ❌
        </button>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📅 Detalle de Citas Confirmadas</h2>
        @php $totalC = collect($citas_confirmadas ?? [])->sum('count'); @endphp
        <div class="overflow-auto max-h-80">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 font-medium text-gray-600 uppercase">Paciente</th>
                        <th class="p-2 font-medium text-gray-600 uppercase">Fecha</th>
                        <th class="p-2 font-medium text-gray-600 uppercase">Total</th>
                        <th class="p-2 font-medium text-gray-600 uppercase">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas_confirmadas ?? [] as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2">
                                @if(!empty($item->paciente))
                                    {{ $item->paciente }}
                                @else
                                    {{ trim(($item->nombre ?? '') . ' ' . ($item->apellido_paterno ?? '') . ' ' . ($item->apellido_materno ?? '')) ?: '—' }}
                                @endif
                            </td>
                            <td class="p-2">
                                @php
                                    $f = $item->fecha ?? ($item->periodo ?? null);
                                @endphp
                                {{ $f ? \Carbon\Carbon::parse($f)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="p-2">{{ $item->count ?? 0 }}</td>
                            <td class="p-2">{{ number_format($totalC ? ($item->count / $totalC * 100) : 0, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</dialog>

{{-- Modal Canceladas --}}
<dialog id="modalCanceladas" class="rounded-lg w-full max-w-3xl">
    <div class="relative bg-white p-6 rounded-lg shadow-xl">
        <button onclick="document.getElementById('modalCanceladas').close()"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition">
            ❌
        </button>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📅 Detalle de Citas Canceladas</h2>
        @php $totalX = collect($citas_canceladas ?? [])->sum('count'); @endphp
        <div class="overflow-auto max-h-80">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 font-medium text-gray-600 uppercase">Paciente</th>
                        <th class="p-2 font-medium text-gray-600 uppercase">Fecha</th>
                        <th class="p-2 font-medium text-gray-600 uppercase">Total</th>
                        <th class="p-2 font-medium text-gray-600 uppercase">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas_canceladas ?? [] as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2">
                                @if(!empty($item->paciente))
                                    {{ $item->paciente }}
                                @else
                                    {{ trim(($item->nombre ?? '') . ' ' . ($item->apellido_paterno ?? '') . ' ' . ($item->apellido_materno ?? '')) ?: '—' }}
                                @endif
                            </td>
                            <td class="p-2">
                                @php
                                    $f = $item->fecha ?? ($item->periodo ?? null);
                                @endphp
                                {{ $f ? \Carbon\Carbon::parse($f)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="p-2">{{ $item->count ?? 0 }}</td>
                            <td class="p-2">{{ number_format($totalX ? ($item->count / $totalX * 100) : 0, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</dialog>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const confirmadas = @json($citas_confirmadas ?? []);
    const canceladas  = @json($citas_canceladas ?? []);
    const totalC      = confirmadas.reduce((sum, it) => sum + (it.count || 0), 0);
    const totalX      = canceladas.reduce((sum, it) => sum + (it.count || 0), 0);
    const totalAll    = totalC + totalX;
    const data = totalAll ? [ (totalC/totalAll)*100, (totalX/totalAll)*100 ] : [0,0];

    new Chart(document.getElementById('totalesChart'), {
        type: 'bar',
        data: {
            labels: ['Confirmadas', 'Canceladas'],
            datasets: [{
                data: data.map(d => Number(d.toFixed(2))),
                backgroundColor: ['#34D399', '#F87171'],
                borderRadius: 6,
                maxBarThickness: 60,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: v => v + '%' },
                    title: { display: true, text: 'Porcentaje de Citas' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } }
            }
        }
    });

    function mostrarModalConfirmadas() {
        if ((confirmadas || []).length) document.getElementById('modalConfirmadas').showModal();
    }
    function mostrarModalCanceladas() {
        if ((canceladas || []).length) document.getElementById('modalCanceladas').showModal();
    }
</script>
@endsection
