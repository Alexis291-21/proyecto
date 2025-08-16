@extends('main')

@section('title', 'Reportes')

@section('content')
<div class="container mx-auto p-6 -mt-8">
    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4 bg-white/80 backdrop-blur-sm rounded-2xl shadow px-5 py-4">
            <div class="rounded-full bg-indigo-50 p-3">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 13h2l1-2 2 4 4-8 3 6 3-4 3 8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg md:text-2xl font-semibold text-gray-800">Reportes de Citas Médicas</h1>
                <p class="text-sm text-gray-500">Análisis rápido de confirmadas vs canceladas</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Botón Detalle Confirmadas --}}
            <button type="button" onclick="mostrarModalConfirmadas()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium">Detalle Confirmadas</span>
            </button>

            {{-- Botón Detalle Canceladas --}}
            <button type="button" onclick="mostrarModalCanceladas()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium">Detalle Canceladas</span>
            </button>
        </div>
    </div>

    {{-- Estadísticas Rápidas (CUADROS REDUCIDOS) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow p-3 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-500 uppercase">Citas Confirmadas</h2>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($porcentajeConfirmadas ?? 0, 2) }}%</p>
                <p class="text-xs text-gray-400 mt-1">Porcentaje sobre total</p>
            </div>
            <div class="flex items-center justify-center w-10 h-10 bg-emerald-50 rounded-full">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-3 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-500 uppercase">Citas Canceladas</h2>
                <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($porcentajeCanceladas ?? 0, 2) }}%</p>
                <p class="text-xs text-gray-400 mt-1">Porcentaje sobre total</p>
            </div>
            <div class="flex items-center justify-center w-10 h-10 bg-rose-50 rounded-full">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-3 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-500 uppercase">Total de Citas</h2>
                @php
                    $totalConfirmadas = collect($citas_confirmadas ?? [])->sum('count');
                    $totalCanceladas  = collect($citas_canceladas  ?? [])->sum('count');
                    $totalGeneral     = $totalConfirmadas + $totalCanceladas;
                @endphp
                <p class="text-2xl font-extrabold text-slate-700 mt-1">{{ $totalGeneral }}</p>
                <p class="text-xs text-gray-400 mt-1">Suma de confirmadas y canceladas</p>
            </div>
            <div class="flex items-center justify-center w-10 h-10 bg-slate-50 rounded-full">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 12h18M3 6h18M3 18h18" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Gráfico Comparativo --}}
    <div class="bg-white shadow-lg rounded-2xl p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Comparativa: Confirmadas vs Canceladas</h3>
            <div class="text-sm text-gray-500">Visión porcentual</div>
        </div>
        <div class="w-full">
            <canvas id="totalesChart" class="w-full h-32"></canvas>
        </div>
    </div>
</div>

{{-- Modal Confirmadas --}}
<dialog id="modalConfirmadas" class="rounded-2xl w-full max-w-4xl p-0">
    <form method="dialog" class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="flex items-start justify-between p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">📅 Detalle de Citas Confirmadas</h2>
            <button type="button" onclick="document.getElementById('modalConfirmadas').close()" aria-label="Cerrar" class="text-gray-500 hover:text-gray-700">
                ✖
            </button>
        </div>

        <div class="p-6 max-h-96 overflow-auto">
            @php $totalC = collect($citas_confirmadas ?? [])->sum('count'); @endphp
            <table class="w-full text-sm divide-y">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($citas_confirmadas ?? [] as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if(!empty($item->paciente))
                                    {{ $item->paciente }}
                                @else
                                    {{ trim(($item->nombre ?? '') . ' ' . ($item->apellido_paterno ?? '') . ' ' . ($item->apellido_materno ?? '')) ?: '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php $f = $item->fecha ?? ($item->periodo ?? null); @endphp
                                {{ $f ? \Carbon\Carbon::parse($f)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->count ?? 0 }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($totalC ? ($item->count / $totalC * 100) : 0, 2) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No hay citas confirmadas para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-end gap-3 p-4 border-t">
            <button type="button" onclick="document.getElementById('modalConfirmadas').close()" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Cerrar</button>
        </div>
    </form>
</dialog>

{{-- Modal Canceladas --}}
<dialog id="modalCanceladas" class="rounded-2xl w-full max-w-4xl p-0">
    <form method="dialog" class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="flex items-start justify-between p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">📅 Detalle de Citas Canceladas</h2>
            <button type="button" onclick="document.getElementById('modalCanceladas').close()" aria-label="Cerrar" class="text-gray-500 hover:text-gray-700">
                ✖
            </button>
        </div>

        <div class="p-6 max-h-96 overflow-auto">
            @php $totalX = collect($citas_canceladas ?? [])->sum('count'); @endphp
            <table class="w-full text-sm divide-y">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($citas_canceladas ?? [] as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if(!empty($item->paciente))
                                    {{ $item->paciente }}
                                @else
                                    {{ trim(($item->nombre ?? '') . ' ' . ($item->apellido_paterno ?? '') . ' ' . ($item->apellido_materno ?? '')) ?: '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php $f = $item->fecha ?? ($item->periodo ?? null); @endphp
                                {{ $f ? \Carbon\Carbon::parse($f)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->count ?? 0 }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($totalX ? ($item->count / $totalX * 100) : 0, 2) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No hay citas canceladas para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-end gap-3 p-4 border-t">
            <button type="button" onclick="document.getElementById('modalCanceladas').close()" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Cerrar</button>
        </div>
    </form>
</dialog>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos (desde PHP)
    const confirmadas = @json($citas_confirmadas ?? []);
    const canceladas  = @json($citas_canceladas ?? []);
    const totalC = confirmadas.reduce((s, it) => s + (it.count || 0), 0);
    const totalX = canceladas.reduce((s, it) => s + (it.count || 0), 0);
    const totalAll = totalC + totalX;

    const dataPercent = totalAll ? [ (totalC/totalAll)*100, (totalX/totalAll)*100 ] : [0,0];

    // Crear gráfico
    const ctx = document.getElementById('totalesChart').getContext('2d');
    const totalesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Confirmadas', 'Canceladas'],
            datasets: [{
                label: 'Porcentaje',
                data: dataPercent.map(d => Number(d.toFixed(2))),
                backgroundColor: ['rgba(52,211,153,0.12)', 'rgba(248,113,113,0.12)'],
                borderColor: ['#34D399', '#F87171'],
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 48,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 6, bottom: 6 } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 600 } }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: (v) => v + '%'
                    },
                    title: { display: true, text: 'Porcentaje de Citas' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y}% — ${ctx.dataset.label || ''}`
                    }
                },
                datalabels: {
                    display: false
                }
            }
        }
    });

    // Funciones para mostrar modales (compatible con <dialog>)
    function mostrarModalConfirmadas() {
        const d = document.getElementById('modalConfirmadas');
        if (!d) return;
        if ((confirmadas || []).length) {
            if (typeof d.showModal === 'function') { d.showModal(); }
            else { d.setAttribute('open', ''); } // fallback
        }
    }

    function mostrarModalCanceladas() {
        const d = document.getElementById('modalCanceladas');
        if (!d) return;
        if ((canceladas || []).length) {
            if (typeof d.showModal === 'function') { d.showModal(); }
            else { d.setAttribute('open', ''); }
        }
    }

    // Cerrar al presionar Escape (mejor experiencia)
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const mod1 = document.getElementById('modalConfirmadas');
            const mod2 = document.getElementById('modalCanceladas');
            if (mod1 && mod1.open) mod1.close();
            if (mod2 && mod2.open) mod2.close();
        }
    });
</script>
@endsection
