<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Panel de análisis</p>
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">Estadísticas</h2>
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
            <div x-data="novedadesPanel({ baseUrl: @js(url('responsables')), globalUrl: @js(route('estadisticas.pendientes')) })">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Resumen general</p>
                        <h3 class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">Votantes registrados en el sistema</h3>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-[minmax(0,1fr)_220px]">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Confirmados acumulados</p>
                            <p class="mt-2 text-6xl font-black tracking-tight text-gray-900 dark:text-gray-100">{{ number_format($totalVotantes) }}</p>
                            <p class="mt-3 max-w-xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Este número solo incluye votantes con certificado cargado. A su lado ves los pendientes para tener el mapa completo.
                            </p>

                            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">Pendientes</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($pendingVotantes) }}</p>
                                </div>
                                <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4 dark:border-teal-900/40 dark:bg-teal-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700 dark:text-teal-300">Departamentos</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($departamentoCount) }}</p>
                                </div>
                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-900/40 dark:bg-emerald-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Municipios</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($municipioCount) }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                    @click="openDetalle({ id: 0, name: 'Pendientes globales', email: '', sede: '', role: '', pendientes: {{ $pendingVotantes }}, confirmados: {{ $totalVotantes }} })"
                                >
                                    Ver detalle de pendientes
                                </button>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Abre un panel ligero para revisar el detalle sin recargar la vista.</p>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-900/40 dark:bg-emerald-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Últimos 7 días</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($dailyTrend->sum('total')) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Actividad reciente</p>
                            <div class="mt-4 space-y-3">
                                @foreach ($dailyTrend as $day)
                                    @php($maxTrend = max(1, (int) $dailyTrend->max('total')))
                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-xs font-medium text-gray-500 dark:text-gray-400">
                                            <span>{{ $day['label'] }}</span>
                                            <span>{{ $day['total'] }}</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500" style="width: {{ max(8, round(($day['total'] / $maxTrend) * 100)) }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Estado por responsables</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Haz clic en un responsable para abrir el detalle de sus pendientes.</p>

                    <div class="mt-5 space-y-4">
                        @forelse ($employeeStats as $employee)
                            <button
                                type="button"
                                class="w-full rounded-2xl border border-gray-200 bg-white p-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-indigo-950/20"
                                @click="openDetalle({ id: {{ $employee->id }}, name: @js($employee->name), email: @js($employee->email), sede: @js($employee->sede), role: @js($employee->role), pendientes: {{ $employee->votantes_pendientes_count }}, confirmados: {{ $employee->votantes_confirmados_count }} })"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">
                                        {{ $employee->votantes_pendientes_count }} pendientes
                                    </span>
                                </div>
                                <div class="mt-3 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                    @php($employeeTotal = max(1, $employee->votantes_confirmados_count + $employee->votantes_pendientes_count))
                                    <div class="h-2 rounded-full bg-indigo-600" style="width: {{ max(10, round(($employee->votantes_confirmados_count / $employeeTotal) * 100)) }}%;"></div>
                                </div>
                            </button>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                No hay responsables registrados todavía.
                            </div>
                        @endforelse
                    </div>
                    </div>
                </div>

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
                <div class="relative mx-auto flex min-h-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
                    <div x-show="open" x-transition style="display:none;" class="w-full overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <div>
                                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Detalle bajo demanda</p>
                                <h3 id="detalle-pendientes-title" class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100" x-text="selected ? selected.name : 'Pendientes'"></h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Se carga solo cuando abres el panel, para no afectar el rendimiento.</p>
                            </div>

                            <button type="button" @click="close()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid gap-6 p-6 lg:grid-cols-[320px_minmax(0,1fr)]">
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/40 dark:bg-amber-950/30">
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Pendientes</p>
                                <p class="mt-2 text-4xl font-bold text-amber-950 dark:text-amber-50" x-text="selected ? (selected.pendientes ?? 0) : 0"></p>
                                <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">Listado detallado de votantes pendientes.</p>
                                <div class="mt-5 space-y-3 max-h-[360px] overflow-y-auto pr-1">
                                    <template x-if="loading">
                                        <div class="rounded-xl border border-dashed border-amber-300 bg-white p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-slate-900 dark:text-amber-200">Cargando...</div>
                                    </template>
                                    <template x-if="error">
                                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200" x-text="error"></div>
                                    </template>
                                    <template x-if="!loading && !error">
                                        <template x-for="pendiente in detalle" :key="pendiente.id">
                                            <div class="rounded-xl border border-white bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-slate-900">
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
                                    <button type="button" @click="nextPage()" :disabled="meta.current_page >= meta.last_page || loading" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300">Siguiente</button>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Resumen del responsable</h4>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Confirmados, pendientes y carga por usuario.</p>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                                            <tr>
                                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Campo</th>
                                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                            <tr><td class="px-5 py-4 text-sm text-gray-500">Responsable</td><td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="selected ? selected.name : 'Sin selección'"></td></tr>
                                            <tr><td class="px-5 py-4 text-sm text-gray-500">Sede</td><td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="selected && selected.sede ? selected.sede : 'Sin sede'"></td></tr>
                                            <tr><td class="px-5 py-4 text-sm text-gray-500">Confirmados</td><td class="px-5 py-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300" x-text="selected ? (selected.confirmados ?? 0) : 0"></td></tr>
                                            <tr><td class="px-5 py-4 text-sm text-gray-500">Pendientes</td><td class="px-5 py-4 text-sm font-semibold text-amber-700 dark:text-amber-300" x-text="selected ? (selected.pendientes ?? 0) : 0"></td></tr>
                                            <tr><td class="px-5 py-4 text-sm text-gray-500">Estado</td><td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="selected && (selected.pendientes ?? 0) > 0 ? 'Con pendientes' : 'Al día'"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Distribución por departamento</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vista radial de los departamentos más cargados.</p>
                        </div>
                    </div>
                    <div class="mt-5 h-80">
                        <canvas id="departmentChart" x-ref="departmentChart"></canvas>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Distribución por municipio</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Comparación de los municipios con mayor actividad.</p>
                        </div>
                    </div>
                    <div class="mt-5 h-80">
                        <canvas id="municipalityChart" x-ref="municipalityChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tendencia de los últimos 7 días</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Línea de tiempo con el ritmo de carga reciente.</p>
                    </div>
                </div>
                <div class="mt-5 h-80">
                    <canvas id="trendChart" x-ref="trendChart"></canvas>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
