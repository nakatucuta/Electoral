<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css">
        <style>
            #responsablesDataTable_wrapper .dt-search input,
            #responsablesDataTable_wrapper .dt-length select {
                border-radius: 0.75rem;
                border-color: rgb(209 213 219);
                padding: 0.5rem 0.75rem;
            }

            #responsablesDataTable_wrapper .dt-container {
                color: rgb(17 24 39);
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Panel de analisis</p>
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">Estadisticas</h2>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm ring-1 ring-inset ring-indigo-200 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-800 dark:text-indigo-300 dark:ring-indigo-900/40 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Volver al tablero
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div
            class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8"
            x-data="dashboardStats({
                initialTab: 'estadisticas',
                totalVotantes: @js($totalVotantes),
                departamentoCount: @js($departamentoCount),
                municipioCount: @js($municipioCount),
                topDepartamentos: @js($topDepartamentos),
                topMunicipios: @js($topMunicipios),
                dailyTrend: @js($dailyTrend),
            })"
        >
            <div x-data="novedadesPanel({ baseUrl: @js(url('responsables')), globalUrl: @js(route('estadisticas.pendientes')) })" class="space-y-8">
                <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Resumen general</p>
                        <h3 class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">Estado de votantes y comprobantes</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Un vistazo rapido al total del sistema, los que ya tienen certificado y los que siguen pendientes.</p>
                    </div>

                    <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                        <div class="space-y-6">
                            <div class="grid gap-4 sm:grid-cols-3">
                                <article class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/50">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total de votos</p>
                                    <p class="mt-2 text-4xl font-black tracking-tight text-slate-950 dark:text-slate-50">{{ number_format($totalVotantes + $pendingVotantes) }}</p>
                                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Registros con y sin comprobante.</p>
                                </article>

                                <article class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Votos con comprobante</p>
                                    <p class="mt-2 text-4xl font-black tracking-tight text-emerald-950 dark:text-emerald-50">{{ number_format($totalVotantes) }}</p>
                                    <p class="mt-2 text-sm text-emerald-700/80 dark:text-emerald-200/80">Ya tienen certificado cargado.</p>
                                </article>

                                <article class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Votos pendientes</p>
                                    <p class="mt-2 text-4xl font-black tracking-tight text-amber-950 dark:text-amber-50">{{ number_format($pendingVotantes) }}</p>
                                    <p class="mt-2 text-sm text-amber-700/80 dark:text-amber-200/80">Aun esperan carga del certificado.</p>
                                </article>
                            </div>

                            <div class="rounded-3xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Mapa operativo</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Este panel resume la carga real en la plataforma y abre el detalle sin recargar la pantalla.</p>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                        @click="openDetalle({ id: 0, name: 'Votantes globales', email: '', sede: '', role: '', pendientes: {{ $pendingVotantes }}, confirmados: {{ $totalVotantes }}, total: {{ $totalVotantes + $pendingVotantes }} })"
                                    >
                                        Ver detalle completo
                                    </button>
                                </div>

                                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-gray-800">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Responsables con alerta</p>
                                        <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ number_format($pendingNotificationsTotal) }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pendientes detectados por responsable.</p>
                                    </div>
                                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-gray-800">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Gestionados</p>
                                        <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ number_format($totalManagedVotantes) }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total de votantes cargados por usuarios.</p>
                                    </div>
                                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-gray-800">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Confirmados</p>
                                        <p class="mt-2 text-3xl font-black text-gray-900 dark:text-gray-100">{{ number_format($confirmedVotantes) }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Con comprobante visible en sistema.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <aside class="rounded-3xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900/40">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Distribucion actual</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Confirmados vs pendientes.</p>
                                </div>
                            </div>

                            @php($distributionTotal = max(1, $totalVotantes + $pendingVotantes))
                            <div class="mt-5 space-y-4">
                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm">
                                        <span class="font-medium text-emerald-700 dark:text-emerald-300">Con comprobante</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format(round(($totalVotantes / $distributionTotal) * 100)) }}%</span>
                                    </div>
                                    <div class="h-3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ max(8, round(($totalVotantes / $distributionTotal) * 100)) }}%;"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm">
                                        <span class="font-medium text-amber-700 dark:text-amber-300">Pendientes</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format(round(($pendingVotantes / $distributionTotal) * 100)) }}%</span>
                                    </div>
                                    <div class="h-3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-full rounded-full bg-amber-500" style="width: {{ max(8, round(($pendingVotantes / $distributionTotal) * 100)) }}%;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 rounded-2xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-500/10">
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">Ultimos 7 dias</p>
                                <p class="mt-2 text-3xl font-black text-gray-950 dark:text-gray-50">{{ number_format($dailyTrend->sum('total')) }}</p>
                                <p class="mt-1 text-sm text-indigo-700/80 dark:text-indigo-200/80">Votantes confirmados cargados recientemente.</p>
                            </div>
                        </aside>
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Estado por responsables</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cada tarjeta muestra total gestionado, confirmados y pendientes.</p>
                        </div>
                        @if ($pendingNotificationsTotal > 0)
                            <div class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-800 dark:bg-amber-500/10 dark:text-amber-200">
                                {{ number_format($pendingNotificationsTotal) }} alertas activas
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 grid gap-4 xl:grid-cols-2">
                        @forelse ($employeeStats as $employee)
                            <button
                                type="button"
                                class="rounded-3xl border border-gray-200 bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:hover:border-indigo-500/40"
                                @click="openDetalle({ id: {{ $employee->id }}, name: @js($employee->name), email: @js($employee->email), sede: @js($employee->sede), role: @js($employee->role), pendientes: {{ $employee->votantes_pendientes_count }}, confirmados: {{ $employee->votantes_confirmados_count }}, total: {{ $employee->votantes_total_gestionados }} })"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                                        <p class="mt-1 break-all text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $employee->sede ?? 'Sin sede' }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">
                                        {{ $employee->votantes_pendientes_count }} pendientes
                                    </span>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-800">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total</p>
                                        <p class="mt-1 text-2xl font-black text-slate-950 dark:text-slate-50">{{ number_format($employee->votantes_total_gestionados) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 dark:bg-emerald-500/10">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Confirmados</p>
                                        <p class="mt-1 text-2xl font-black text-emerald-950 dark:text-emerald-50">{{ number_format($employee->votantes_confirmados_count) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-amber-50 px-4 py-3 dark:bg-amber-500/10">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Pendientes</p>
                                        <p class="mt-1 text-2xl font-black text-amber-950 dark:text-amber-50">{{ number_format($employee->votantes_pendientes_count) }}</p>
                                    </div>
                                </div>

                                @php($employeeTotal = max(1, $employee->votantes_confirmados_count + $employee->votantes_pendientes_count))
                                <div class="mt-4 h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-indigo-600" style="width: {{ max(8, round(($employee->votantes_confirmados_count / $employeeTotal) * 100)) }}%;"></div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Haz clic para ver el detalle con pendientes y confirmados.</p>
                            </button>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                No hay responsables registrados todavia.
                            </div>
                        @endforelse
                    </div>
                </section>

                <div
                    x-show="open"
                    x-transition.opacity
                    style="display: none;"
                    class="fixed inset-0 z-50"
                    aria-labelledby="detalle-pendientes-title"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="absolute inset-0 bg-slate-950/60" @click="close()"></div>
                    <div class="relative mx-auto flex min-h-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
                        <div x-show="open" x-transition style="display:none;" class="w-full overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900">
                            <div class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-start sm:justify-between dark:border-gray-700">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Detalle bajo demanda</p>
                                    <h3 id="detalle-pendientes-title" class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100" x-text="selected ? selected.name : 'Detalle'"></h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Se carga solo cuando abres el panel, para no afectar el rendimiento.</p>
                                </div>

                                <button type="button" @click="close()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid gap-6 p-6 xl:grid-cols-[360px_minmax(0,1fr)]">
                                <aside class="space-y-4">
                                    <div class="rounded-3xl border border-gray-200 bg-slate-50 p-5 dark:border-gray-700 dark:bg-slate-900/50">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Resumen rapido</p>
                                        <div class="mt-4 grid grid-cols-3 gap-3">
                                            <div class="rounded-2xl bg-white p-3 shadow-sm dark:bg-gray-800">
                                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</p>
                                                <p class="mt-1 text-2xl font-black text-gray-950 dark:text-gray-50" x-text="selected ? (selected.total ?? 0) : 0"></p>
                                            </div>
                                            <div class="rounded-2xl bg-emerald-50 p-3 shadow-sm dark:bg-emerald-500/10">
                                                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Con comp.</p>
                                                <p class="mt-1 text-2xl font-black text-emerald-950 dark:text-emerald-50" x-text="selected ? (selected.confirmados ?? 0) : 0"></p>
                                            </div>
                                            <div class="rounded-2xl bg-amber-50 p-3 shadow-sm dark:bg-amber-500/10">
                                                <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Pend.</p>
                                                <p class="mt-1 text-2xl font-black text-amber-950 dark:text-amber-50" x-text="selected ? (selected.pendientes ?? 0) : 0"></p>
                                            </div>
                                        </div>
                                        <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                            <p><span class="font-semibold text-gray-900 dark:text-gray-100">Responsable:</span> <span x-text="selected ? selected.name : 'Sin seleccion'"></span></p>
                                            <p class="mt-1"><span class="font-semibold text-gray-900 dark:text-gray-100">Sede:</span> <span x-text="selected && selected.sede ? selected.sede : 'Sin sede'"></span></p>
                                            <p class="mt-1"><span class="font-semibold text-gray-900 dark:text-gray-100">Estado:</span> <span x-text="selected && (selected.pendientes ?? 0) > 0 ? 'Con pendientes' : 'Al dia'"></span></p>
                                        </div>
                                    </div>

                                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/40 dark:bg-amber-950/30">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Pendientes</p>
                                                <p class="text-sm text-amber-800 dark:text-amber-200">Votantes sin comprobante.</p>
                                            </div>
                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-amber-800 shadow-sm dark:bg-gray-900 dark:text-amber-200" x-text="selected ? (selected.pendientes ?? 0) : 0"></span>
                                        </div>

                                        <div class="mt-4 space-y-3 max-h-[340px] overflow-y-auto pr-1">
                                            <template x-if="loading">
                                                <div class="rounded-xl border border-dashed border-amber-300 bg-white p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-slate-900 dark:text-amber-200">Cargando...</div>
                                            </template>
                                            <template x-if="error">
                                                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200" x-text="error"></div>
                                            </template>
                                            <template x-if="!loading && !error">
                                                <template x-for="pendiente in detalle" :key="pendiente.id">
                                                    <div class="rounded-2xl border border-white bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-slate-900">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div>
                                                                <p class="font-semibold text-gray-900 dark:text-gray-100" x-text="`${pendiente.nombres} ${pendiente.apellidos}`"></p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="`${pendiente.tipo_identificacion} - ${pendiente.numero_identificacion}`"></p>
                                                            </div>
                                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-200" x-text="pendiente.estado_registro_label"></span>
                                                        </div>
                                                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400" x-text="`${pendiente.departamento || 'Sin dato'} / ${pendiente.municipio || 'Sin dato'}`"></p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="pendiente.responsable_nombre ? `Responsable: ${pendiente.responsable_nombre}` : 'Responsable: Sin dato'"></p>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between">
                                            <button type="button" @click="prevPage()" :disabled="meta.current_page <= 1 || loading" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300">Anterior</button>
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="meta.total ? `Pagina ${meta.current_page} de ${meta.last_page}` : 'Sin resultados'"></span>
                                            <button type="button" @click="nextPage()" :disabled="meta.current_page >= meta.last_page || loading" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300">Siguiente</button>
                                        </div>
                                    </div>
                                </aside>

                                <section class="space-y-4">
                                    <div class="rounded-3xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Confirmados recientes</h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Votantes con comprobante, para contrastar el avance del responsable.</p>
                                        </div>
                                        <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                                            <template x-if="loading">
                                                <div class="rounded-2xl border border-dashed border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-100">Cargando...</div>
                                            </template>
                                            <template x-if="!loading && !error && confirmados.length">
                                                <template x-for="confirmado in confirmados" :key="confirmado.id">
                                                    <article class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="min-w-0">
                                                                <p class="truncate font-semibold text-gray-900 dark:text-gray-100" x-text="`${confirmado.nombres} ${confirmado.apellidos}`"></p>
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="`${confirmado.tipo_identificacion} - ${confirmado.numero_identificacion}`"></p>
                                                            </div>
                                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200" x-text="confirmado.estado_registro_label"></span>
                                                        </div>
                                                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400" x-text="`${confirmado.departamento || 'Sin dato'} / ${confirmado.municipio || 'Sin dato'}`"></p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="confirmado.responsable_nombre ? `Responsable: ${confirmado.responsable_nombre}` : 'Responsable: Sin dato'"></p>
                                                    </article>
                                                </template>
                                            </template>
                                            <template x-if="!loading && !error && !confirmados.length">
                                                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                    Aun no hay confirmados para mostrar en esta vista.
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="grid gap-6 lg:grid-cols-2">
                                        <div class="rounded-3xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Resumen del responsable</h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Confirmados, pendientes y carga por usuario.</p>
                                            <div class="mt-4 overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                                                        <tr>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Campo</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Valor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                                        <tr><td class="px-5 py-4 text-sm text-gray-500">Responsable</td><td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="selected ? selected.name : 'Sin seleccion'"></td></tr>
                                                        <tr><td class="px-5 py-4 text-sm text-gray-500">Sede</td><td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="selected && selected.sede ? selected.sede : 'Sin sede'"></td></tr>
                                                        <tr><td class="px-5 py-4 text-sm text-gray-500">Total</td><td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="selected ? (selected.total ?? 0) : 0"></td></tr>
                                                        <tr><td class="px-5 py-4 text-sm text-gray-500">Con comprobante</td><td class="px-5 py-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300" x-text="selected ? (selected.confirmados ?? 0) : 0"></td></tr>
                                                        <tr><td class="px-5 py-4 text-sm text-gray-500">Pendientes</td><td class="px-5 py-4 text-sm font-semibold text-amber-700 dark:text-amber-300" x-text="selected ? (selected.pendientes ?? 0) : 0"></td></tr>
                                                        <tr><td class="px-5 py-4 text-sm text-gray-500">Estado</td><td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="selected && (selected.pendientes ?? 0) > 0 ? 'Con pendientes' : 'Al dia'"></td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="rounded-3xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Notas rapidas</h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">La idea es que este panel te sirva como semaforo operativo, no solo como alerta.</p>
                                            <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                                                <li class="rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">El numero grande resume el total y evita dudas al abrir la pagina.</li>
                                                <li class="rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">La columna izquierda prioriza pendientes y la derecha te deja ver confirmados recientes.</li>
                                                <li class="rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">Los responsables con carga real quedan visibles arriba y se abren sin recargar.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Distribucion por departamento</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Vista de los departamentos mas cargados con comprobante.</p>
                            </div>
                        </div>
                        <div class="mt-5 h-80">
                            <canvas id="departmentChart" x-ref="departmentChart"></canvas>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Distribucion por municipio</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Comparacion de los municipios con mayor actividad.</p>
                            </div>
                        </div>
                        <div class="mt-5 h-80">
                            <canvas id="municipalityChart" x-ref="municipalityChart"></canvas>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tendencia de los ultimos 7 dias</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Linea de tiempo con el ritmo de carga reciente.</p>
                        </div>
                    </div>
                    <div class="mt-5 h-80">
                        <canvas id="trendChart" x-ref="trendChart"></canvas>
                    </div>
                </section>

                @if (auth()->user()->isAdmin())
                    <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tabla dinámica de responsables</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Se carga por AJAX, con búsqueda y paginación sin recargar la vista.</p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700 dark:bg-slate-700 dark:text-slate-100">Responsables cargados: {{ number_format($employeeStats->count()) }}</span>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">Confirmados: {{ number_format($confirmedVotantes) }}</span>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">Pendientes: {{ number_format($pendingNotificationsTotal) }}</span>
                            </div>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table id="responsablesDataTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Responsable</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Confirmados</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pendientes</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach ($employeeStats as $employee)
                                        <tr>
                                            <td class="px-4 py-4 text-sm">
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($employee->votantes_total_gestionados) }}</td>
                                            <td class="px-4 py-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format($employee->votantes_confirmados_count) }}</td>
                                            <td class="px-4 py-4 text-sm font-semibold text-amber-700 dark:text-amber-300">{{ number_format($employee->votantes_pendientes_count) }}</td>
                                            <td class="px-4 py-4 text-sm">
                                                @if ($employee->votantes_pendientes_count > 0)
                                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pendientes</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Al dia</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    @if (auth()->user()->isAdmin())
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const table = document.getElementById('responsablesDataTable');

                if (!table || typeof DataTable === 'undefined') {
                    return;
                }

                const escapeHtml = (value) => String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');

                const dataTable = new DataTable(table, {
                    ajax: {
                        url: @json(route('estadisticas.responsables-datatable')),
                        dataSrc: 'data',
                    },
                    deferRender: true,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25],
                    order: [[5, 'desc']],
                    language: {
                        decimal: '',
                        emptyTable: 'No hay responsables para mostrar',
                        info: 'Mostrando _START_ a _END_ de _TOTAL_ responsables',
                        infoEmpty: 'No hay registros disponibles',
                        infoFiltered: '(filtrado de _MAX_ responsables)',
                        infoPostFix: '',
                        thousands: ',',
                        lengthMenu: 'Mostrar _MENU_ registros',
                        loadingRecords: 'Cargando...',
                        processing: 'Procesando...',
                        search: 'Buscar:',
                        zeroRecords: 'No se encontraron coincidencias',
                        paginate: {
                            first: 'Primero',
                            last: 'Ultimo',
                            next: 'Siguiente',
                            previous: 'Anterior',
                        },
                    },
                    columns: [
                        {
                            data: 'name',
                            render: (data) => `<div class="font-semibold text-gray-900 dark:text-gray-100">${escapeHtml(data || 'Sin nombre')}</div>`,
                        },
                        {
                            data: 'votantes_total_gestionados',
                            render: (data) => `<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${Number(data ?? 0).toLocaleString('es-CO')}</span>`,
                        },
                        {
                            data: 'votantes_confirmados_count',
                            render: (data) => `<span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">${Number(data ?? 0).toLocaleString('es-CO')}</span>`,
                        },
                        {
                            data: 'votantes_pendientes_count',
                            render: (data) => `<span class="text-sm font-semibold text-amber-700 dark:text-amber-300">${Number(data ?? 0).toLocaleString('es-CO')}</span>`,
                        },
                        {
                            data: 'estado',
                            orderable: false,
                            searchable: false,
                        },
                    ],
                });

                window.responsablesDataTable = dataTable;
            });
        </script>
    @endif
@endpush
