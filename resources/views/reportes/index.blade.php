@extends('main')

@section('title')
    Reportes
@endsection

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
    <div class="bg-white shadow-lg rounded-2xl p-6 mb-8 relative">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700">Comparativa: Confirmadas vs Canceladas</h3>
                <p class="text-sm text-gray-500">Visión porcentual — interactúa con las barras para ver detalles.</p>
            </div>

            <div class="flex items-center gap-3">
                <button id="btnExportPNG" type="button" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm border">Exportar PNG</button>
                <button id="btnViewOnly" type="button" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm">Ver sólo el gráfico</button>
            </div>
        </div>

        {{-- agregué id al contenedor para controlar overflow-anchor --}}
        <div id="chartContainer" class="w-full h-64 md:h-96 flex items-center justify-center">
            <canvas id="totalesChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

{{-- Estilos para modales profesionales + fullscreen chart --}}
<style>
    /* Personaliza el backdrop del dialog para mayor énfasis */
    dialog::backdrop {
        background: rgba(15, 23, 42, 0.6); /* slate-900/60 */
        backdrop-filter: blur(4px);
    }

    /* Animación suave al mostrar el dialog */
    dialog[open] > .modal-panel {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
    .modal-panel {
        transform: translateY(10px) scale(.98);
        transition: transform .18s cubic-bezier(.2,.8,.2,1), opacity .18s ease;
        opacity: 0;
    }

    /* Table improvements */
    .modal-table tbody tr:nth-child(odd) { background-color: rgba(0,0,0,0.02); }
    .modal-table thead th { background-color: #f8fafc; } /* gray-50 */
    .sticky-top { position: sticky; top: 0; z-index: 10; }

    /* Fullscreen chart overlay */
    #chartFullscreen {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15,23,42,0.75);
        z-index: 60;
        padding: 2rem;
    }
    #chartFullscreen.open { display: flex; }
    #chartFullscreen .panel {
        width: 100%;
        max-width: 1200px;
        height: 80vh;
        background: white;
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        display: flex;
        flex-direction: column;
    }
    #chartFullscreen .panel .header {
        display:flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding-bottom: .5rem;
    }
    #chartFullscreen .panel canvas { flex: 1 1 auto; }

    /* Evitar saltos de scroll cuando cambia el contenido */
    #chartContainer,
    #chartFullscreen .panel,
    .modal-panel {
        overflow-anchor: none;
    }
</style>

{{-- Modal Confirmadas --}}
<dialog id="modalConfirmadas" class="rounded-2xl w-full max-w-4xl p-0">
    <form method="dialog" class="bg-white rounded-2xl shadow-xl overflow-hidden modal-panel">
        <div class="flex items-center justify-between p-6 border-b">
            <div class="flex items-start gap-4">
                <div class="bg-emerald-50 rounded-full p-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">📅 Detalle de Citas Confirmadas</h2>
                    <p class="text-sm text-gray-500 mt-1">Listado detallado con porcentajes y totales. Puedes exportar o cerrar este panel.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 mr-2">{{ collect($citas_confirmadas ?? [])->sum('count') }} total</span>
                <button type="button" onclick="document.getElementById('modalConfirmadas').close()" aria-label="Cerrar" class="text-gray-500 hover:text-gray-700 p-2 rounded-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6 max-h-96 overflow-auto">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 uppercase font-medium">Buscar:</label>
                    <input id="searchConfirmadas" type="search" placeholder="Buscar paciente o fecha..." class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200" oninput="filterTable('confirmadas')">
                </div>
                <div class="text-sm text-gray-500">Registros: <span id="countConfirmadas">{{ collect($citas_confirmadas ?? [])->sum('count') }}</span></div>
            </div>

            @php $totalC = collect($citas_confirmadas ?? [])->sum('count'); @endphp
            <div class="rounded-lg border overflow-hidden">
                <table class="w-full text-sm modal-table">
                    <thead class="sticky-top">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paciente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white" id="tbody-confirmadas">
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
        </div>

        <div class="flex items-center justify-end gap-3 p-4 border-t bg-gray-50">
            <button type="button" onclick="exportTable('confirmadas')" class="px-4 py-2 rounded-lg bg-white border hover:bg-gray-50 text-sm">Exportar CSV</button>
            <button type="button" onclick="document.getElementById('modalConfirmadas').close()" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white">Cerrar</button>
        </div>
    </form>
</dialog>

{{-- Modal Canceladas --}}
<dialog id="modalCanceladas" class="rounded-2xl w-full max-w-4xl p-0">
    <form method="dialog" class="bg-white rounded-2xl shadow-xl overflow-hidden modal-panel">
        <div class="flex items-center justify-between p-6 border-b">
            <div class="flex items-start gap-4">
                <div class="bg-rose-50 rounded-full p-3">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">📅 Detalle de Citas Canceladas</h2>
                    <p class="text-sm text-gray-500 mt-1">Historial de cancelaciones para análisis y auditoría.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 mr-2">{{ collect($citas_canceladas ?? [])->sum('count') }} total</span>
                <button type="button" onclick="document.getElementById('modalCanceladas').close()" aria-label="Cerrar" class="text-gray-500 hover:text-gray-700 p-2 rounded-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6 max-h-96 overflow-auto">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 uppercase font-medium">Buscar:</label>
                    <input id="searchCanceladas" type="search" placeholder="Buscar paciente o fecha..." class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-rose-200" oninput="filterTable('canceladas')">
                </div>
                <div class="text-sm text-gray-500">Registros: <span id="countCanceladas">{{ collect($citas_canceladas ?? [])->sum('count') }}</span></div>
            </div>

            @php $totalX = collect($citas_canceladas ?? [])->sum('count'); @endphp
            <div class="rounded-lg border overflow-hidden">
                <table class="w-full text-sm modal-table">
                    <thead class="sticky-top">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paciente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white" id="tbody-canceladas">
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
        </div>

        <div class="flex items-center justify-end gap-3 p-4 border-t bg-gray-50">
            <button type="button" onclick="exportTable('canceladas')" class="px-4 py-2 rounded-lg bg-white border hover:bg-gray-50 text-sm">Exportar CSV</button>
            <button type="button" onclick="document.getElementById('modalCanceladas').close()" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white">Cerrar</button>
        </div>
    </form>
</dialog>

{{-- Fullscreen chart overlay (solo gráfico) --}}
<div id="chartFullscreen" role="dialog" aria-hidden="true" aria-labelledby="chartFullTitle">
    <div class="panel">
        <div class="header">
            <div>
                <h3 id="chartFullTitle" class="text-lg font-semibold text-gray-700">Comparativa — Solo Gráfico</h3>
                <p class="text-sm text-gray-500">Click en una barra para ver el detalle.</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="downloadFullPNG" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm border">Descargar PNG</button>
                <button id="closeFull" class="px-3 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm">Cerrar</button>
            </div>
        </div>
        <canvas id="totalesChartFull" class="w-full"></canvas>
    </div>
</div>

{{-- Chart.js + plugin datalabels --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    // Evitar que el navegador restaure/restablezca el scroll automáticamente al navegar/recargar.
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // Datos (desde PHP)
    const confirmadas = @json($citas_confirmadas ?? []);
    const canceladas  = @json($citas_canceladas ?? []);
    const totalC = confirmadas.reduce((s, it) => s + (it.count || 0), 0);
    const totalX = canceladas.reduce((s, it) => s + (it.count || 0), 0);
    const totalAll = totalC + totalX;

    // Valores para el gráfico
    const values = [ totalC, totalX ];
    const labels = ['Confirmadas', 'Canceladas'];
    const dataPercent = totalAll ? values.map(v => Number(((v/totalAll)*100).toFixed(2))) : [0,0];

    // Register plugin
    Chart.register(ChartDataLabels);

    // Helper para crear gradiente (se actualiza en resize)
    function createGradients(canvas) {
        const ctx = canvas.getContext('2d');
        const h = (canvas.height && canvas.height > 0) ? canvas.height : (canvas.clientHeight || 300);

        // Gradiente verde — un poco más oscuro que antes (verde-600)
        const gGreen = ctx.createLinearGradient(0, 0, 0, h);
        gGreen.addColorStop(0, 'rgba(22,163,74,0.95)');  // rgb(22,163,74) = #16A34A (más oscuro)
        gGreen.addColorStop(1, 'rgba(22,163,74,0.45)');

        // Gradiente rojo — un poco más oscuro que antes (rose-600)
        const gRed = ctx.createLinearGradient(0, 0, 0, h);
        gRed.addColorStop(0, 'rgba(225,29,72,0.95)');     // rgb(225,29,72) = #E11D48 (más oscuro)
        gRed.addColorStop(1, 'rgba(225,29,72,0.45)');

        // <-- devolvemos en orden [rojo, verde] (mismo orden que antes, sólo tonos más oscuros)
        return [gRed, gGreen];
    }

    // Chart config factory (sin animación para "aparecer de golpe"; datalabels con % y total)
    function buildChartConfig(canvas, isFull=false) {
        const gradients = createGradients(canvas);
        return {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Porcentaje',
                    data: dataPercent,
                    backgroundColor: gradients,
                    // Actualizamos borderColor para que coincida con los tonos más oscuros
                    borderColor: ['#E11D48', '#16A34A'],
                    borderWidth: 0,
                    borderRadius: 12,
                    maxBarThickness: isFull ? 140 : 64
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 0 },
                layout: { padding: { top: 12, bottom: 12, left: 8, right: 8 } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 700, size: isFull ? 14 : 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: (v) => v + '%',
                            font: { size: isFull ? 13 : 11 }
                        },
                        // <-- AQUI: título del eje Y con el mismo color verde de "Confirmadas"
                        title: { display: true, text: 'Porcentaje de Citas', font: { size: isFull ? 14 : 12 }, color: '#2E2E2E' }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => items[0].label,
                            label: (ctx) => {
                                const idx = ctx.dataIndex;
                                const absolute = values[idx] || 0;
                                const percent = ctx.parsed.y;
                                return `${absolute} citas — ${percent}%`;
                            }
                        },
                        bodySpacing: 6,
                        padding: 10,
                    },
                    // Configuración de datalabels:
                    datalabels: {
                        display: true,
                        labels: {
                            title: {
                                anchor: 'center',
                                align: 'center',
                                formatter: (value, ctx) => {
                                    return value ? `${value}%` : '';
                                },
                                font: {
                                    weight: 800,
                                    size: isFull ? 20 : 14
                                },
                                color: '#ffffff',
                                clamp: true
                            },
                            value: {
                                anchor: 'center',
                                align: 'end',
                                formatter: (value, ctx) => {
                                    const idx = ctx.dataIndex;
                                    const absolute = values[idx] || 0;
                                    return absolute ? `${absolute}` : '';
                                },
                                font: {
                                    weight: 900,
                                    size: isFull ? 20 : 14
                                },
                                color: 'rgba(255,255,255,0.95)',
                                clamp: true
                            }
                        }
                    }
                },
                onClick: (evt, activeEls) => {
                    if (!activeEls.length) return;
                    const index = activeEls[0].index;
                    if (index === 0) mostrarModalConfirmadas();
                    else if (index === 1) mostrarModalCanceladas();
                }
            },
            plugins: [ChartDataLabels]
        };
    }

    // Create embedded chart
    const ctx = document.getElementById('totalesChart').getContext('2d');
    let totalesChart = new Chart(ctx.canvas, buildChartConfig(ctx.canvas, false));

    // Create fullscreen chart
    const ctxFull = document.getElementById('totalesChartFull').getContext('2d');
    let totalesChartFull = new Chart(ctxFull.canvas, buildChartConfig(ctxFull.canvas, true));

    // Recreate gradients & update on resize
    window.addEventListener('resize', () => {
        const g = createGradients(ctx.canvas);
        totalesChart.data.datasets[0].backgroundColor = g;
        totalesChart.update();

        const g2 = createGradients(ctxFull.canvas);
        totalesChartFull.data.datasets[0].backgroundColor = g2;
        totalesChartFull.update();
    });

    // Buttons: export embedded chart as PNG
    document.getElementById('btnExportPNG').addEventListener('click', () => {
        const url = document.getElementById('totalesChart').toDataURL('image/png', 1);
        const link = document.createElement('a');
        link.href = url;
        link.download = `grafico_citas_${new Date().toISOString().slice(0,10)}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Fullscreen open/close
    const overlay = document.getElementById('chartFullscreen');
    document.getElementById('btnViewOnly').addEventListener('click', () => {
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        setTimeout(() => totalesChartFull.resize(), 120);
    });
    document.getElementById('closeFull').addEventListener('click', () => {
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
    });

    // Download full png
    document.getElementById('downloadFullPNG').addEventListener('click', () => {
        const url = document.getElementById('totalesChartFull').toDataURL('image/png', 1);
        const link = document.createElement('a');
        link.href = url;
        link.download = `grafico_citas_full_${new Date().toISOString().slice(0,10)}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Funciones para mostrar modales (compatible con <dialog>)
    function mostrarModalConfirmadas() {
        const d = document.getElementById('modalConfirmadas');
        if (!d) return;
        if ((confirmadas || []).length) {
            if (typeof d.showModal === 'function') { d.showModal(); }
            else { d.setAttribute('open', ''); }
        } else {
            alert('No hay citas confirmadas para mostrar.');
        }
    }

    function mostrarModalCanceladas() {
        const d = document.getElementById('modalCanceladas');
        if (!d) return;
        if ((canceladas || []).length) {
            if (typeof d.showModal === 'function') { d.showModal(); }
            else { d.setAttribute('open', ''); }
        } else {
            alert('No hay citas canceladas para mostrar.');
        }
    }

    // Cerrar al presionar Escape (mejor experiencia)
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const mod1 = document.getElementById('modalConfirmadas');
            const mod2 = document.getElementById('modalCanceladas');
            if (mod1 && mod1.open) mod1.close();
            if (mod2 && mod2.open) mod2.close();
            if (overlay.classList.contains('open')) overlay.classList.remove('open');
        }
    });

    // Filtrado sencillo en el cliente para cada tabla
    function filterTable(type) {
        const q = document.getElementById(type === 'confirmadas' ? 'searchConfirmadas' : 'searchCanceladas').value.toLowerCase().trim();
        const tbody = document.getElementById(type === 'confirmadas' ? 'tbody-confirmadas' : 'tbody-canceladas');
        if (!tbody) return;
        let visibleCount = 0;
        Array.from(tbody.querySelectorAll('tr')).forEach(row => {
            const cells = row.querySelectorAll('td');
            if (!cells.length) return;
            const text = Array.from(cells).map(c => c.textContent.toLowerCase()).join(' ');
            const show = !q || text.includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visibleCount += parseInt(cells[2]?.textContent || 0, 10) || 0;
        });
        if (type === 'confirmadas') document.getElementById('countConfirmadas').textContent = visibleCount || {{ collect($citas_confirmadas ?? [])->sum('count') }};
        else document.getElementById('countCanceladas').textContent = visibleCount || {{ collect($citas_canceladas ?? [])->sum('count') }};
    }

    // Exportar tabla a CSV (cliente). Genera y descarga un CSV simple.
    function exportTable(type) {
        const tbody = document.getElementById(type === 'confirmadas' ? 'tbody-confirmadas' : 'tbody-canceladas');
        if (!tbody) return alert('No hay datos para exportar.');
        const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.style.display !== 'none');
        if (!rows.length) return alert('No hay filas visibles para exportar.');
        const csv = [];
        csv.push(['Paciente','Fecha','Total','%'].join(','));
        rows.forEach(r => {
            const cols = Array.from(r.querySelectorAll('td')).map(td => `"${td.textContent.replace(/"/g,'""').trim()}"`);
            if (cols.length) csv.push(cols.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        const filename = `citas_${type}_${new Date().toISOString().slice(0,10)}.csv`;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>
@endsection
