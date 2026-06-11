<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Panel operativo</p>
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                    {{ auth()->user()->isAdmin() ? 'Administracion electoral' : 'Mis votantes' }}
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if (auth()->user()->isAdmin())
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = true"
                            class="inline-flex min-w-[152px] items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                        >
                            <svg class="h-4 w-4 shrink-0 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 6v6l4 2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span class="tracking-normal">Novedades</span>
                            @if ($employeeNotifications->count() > 0)
                                <span class="inline-flex items-center rounded-full bg-white px-2.5 py-0.5 text-xs font-bold text-indigo-700">
                                    {{ $employeeNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <div
                            x-show="open"
                            x-transition.opacity
                            style="display: none;"
                            class="fixed inset-0 z-50"
                            aria-labelledby="novedades-title"
                            role="dialog"
                            aria-modal="true"
                        >
                            <div class="absolute inset-0 bg-slate-950/60" @click="open = false"></div>

                            <div class="relative mx-auto flex min-h-full max-w-5xl items-center px-4 py-8 sm:px-6 lg:px-8">
                                <div
                                    x-show="open"
                                    x-transition
                                    style="display: none;"
                                    class="w-full overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900"
                                >
                                    <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                                        <div>
                                            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Seguimiento</p>
                                            <h3 id="novedades-title" class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">
                                                Novedades de empleados
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Empleados sin votantes cargados y resumen de actividad.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            @click="open = false"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="grid gap-6 p-6 lg:grid-cols-[320px_minmax(0,1fr)]">
                                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/40 dark:bg-amber-950/30">
                                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Pendientes</p>
                                            <p class="mt-2 text-4xl font-bold text-amber-950 dark:text-amber-50">{{ $employeeNotifications->count() }}</p>
                                            <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">
                                                Usuarios empleados que todavia no han cargado votantes.
                                            </p>

                                            <div class="mt-5 space-y-3 max-h-[360px] overflow-y-auto pr-1">
                                                @forelse ($employeeNotifications as $employee)
                                                    <div class="rounded-xl border border-amber-200 bg-white p-4 shadow-sm dark:border-amber-900/40 dark:bg-slate-900">
                                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                                                        <span class="mt-3 inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700 dark:bg-red-500/10 dark:text-red-300">
                                                            Sin votantes cargados
                                                        </span>
                                                    </div>
                                                @empty
                                                    <div class="rounded-xl border border-dashed border-amber-300 bg-white p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-slate-900 dark:text-amber-200">
                                                        No hay empleados pendientes de carga.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Carga por empleado</h4>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Cuantos votantes registra cada usuario empleado.
                                                </p>
                                            </div>

                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                                                        <tr>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Empleado</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Sede</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Votantes</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                                        @forelse ($employeeStats as $employee)
                                                            <tr>
                                                                <td class="px-5 py-4">
                                                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Rol: empleado</div>
                                                                </td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $employee->sede ?? 'Sin sede' }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $employee->email }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($employee->votantes_count) }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4">
                                                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $employee->votantes_count === 0 ? 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' }}">
                                                                        {{ $employee->votantes_count === 0 ? 'Pendiente' : 'Activo' }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="px-5 py-14 text-center text-sm text-gray-500 dark:text-gray-400">
                                                                    No hay usuarios empleados registrados.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('estadisticas') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm ring-1 ring-inset ring-indigo-200 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-800 dark:text-indigo-300 dark:ring-indigo-900/40 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19V5M4 19h16M8 17v-6M12 17V8M16 17v-3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Estadísticas
                </a>

                <a href="{{ route('votantes.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Registrar votante
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->isAdmin() ? 'Total de votantes' : 'Mis registros' }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalVotantes) }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->isAdmin() ? 'Empleados sin carga' : 'Mis registros' }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->isAdmin() ? number_format($employeeNotifications->count()) : number_format($myVotantes) }}
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-slate-900 to-slate-700 p-6 text-white shadow-sm">
                    <p class="text-sm text-slate-300">Tu perfil</p>
                    <p class="mt-2 text-3xl font-semibold">{{ auth()->user()->isAdmin() ? 'Administrador' : 'Empleado' }}</p>
                    <p class="mt-1 text-sm text-slate-200">Sede: {{ auth()->user()->sede ?? 'Sin sede' }}</p>
                    <p class="mt-1 text-sm text-slate-200">Acceso segun tu tipo de usuario.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Ultimos votantes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Los registros mas recientes que puedes revisar ahora.</p>
                    </div>
                    <a href="{{ route('votantes.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ver todos</a>
                </div>

                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Votante</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Identificación</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ubicación</th>
                                    @if (auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Registrado por</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                @forelse ($latestVotantes as $votante)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                                                    @if ($votante->foto_certificado_url)
                                                        <img src="{{ $votante->foto_certificado_url }}" alt="{{ $votante->nombres }}" class="h-full w-full object-cover">
                                                    @else
                                                        <span class="text-xs font-semibold text-gray-500">SIN FOTO</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $votante->nombres }} {{ $votante->apellidos }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $votante->tipo_identificacion }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $votante->numero_identificacion }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $votante->telefono ?? 'Sin dato' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $votante->departamento ?? 'Sin dato' }} / {{ $votante->municipio ?? 'Sin dato' }}</div>
                                            <div>{{ $votante->puesto_votacion ?? 'Sin dato' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $votante->comuna ?? 'Sin dato' }} - {{ $votante->direccion ?? 'Sin dato' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Mesa {{ $votante->mesa_votacion ?? 'Sin dato' }}</div>
                                        </td>
                                        @if (auth()->user()->isAdmin())
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $votante->user?->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $votante->user?->email }}</div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="px-6 py-14 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Aun no hay votantes registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

