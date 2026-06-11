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
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Resumen general</p>
                        <h3 class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">Votos registrados en el sistema</h3>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-[minmax(0,1fr)_220px]">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total acumulado</p>
                            <p class="mt-2 text-6xl font-black tracking-tight text-gray-900 dark:text-gray-100">{{ number_format($totalVotantes) }}</p>
                            <p class="mt-3 max-w-xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Esta es la cifra principal del sistema. Desde aquí puedes leer la actividad general, la cobertura por territorio y el ritmo reciente de registro.
                            </p>

                            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">Departamentos</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($departamentoCount) }}</p>
                                </div>
                                <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4 dark:border-teal-900/40 dark:bg-teal-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700 dark:text-teal-300">Municipios</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($municipioCount) }}</p>
                                </div>
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
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Top ubicaciones</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Las zonas con mayor concentración de registros.</p>

                    <div class="mt-5 space-y-4">
                        @foreach ($topDepartamentos as $item)
                            @php($percent = $totalVotantes > 0 ? round(($item->total / $totalVotantes) * 100) : 0)
                            <div>
                                <div class="mb-1 flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $item->label }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $item->total }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div class="h-2 rounded-full bg-indigo-600" style="width: {{ max(10, $percent) }}%;"></div>
                                </div>
                            </div>
                        @endforeach
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
                        <canvas x-ref="departmentChart"></canvas>
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
                        <canvas x-ref="municipalityChart"></canvas>
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
                    <canvas x-ref="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
