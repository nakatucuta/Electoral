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
                    <div x-data="novedadesPanel({ baseUrl: @js(url('responsables')) })" class="relative">
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
                            @if ($pendingNotificationsTotal > 0)
                                <span class="inline-flex items-center rounded-full bg-white px-2.5 py-0.5 text-xs font-bold text-indigo-700">
                                    {{ $pendingNotificationsTotal }}
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
                                                Certificados pendientes por responsable
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Aquí ves quién tiene registros pendientes porque aún no ha cargado la foto del certificado.
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
                                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Certificados pendientes</p>
                                            <p class="mt-2 text-4xl font-bold text-amber-950 dark:text-amber-50">{{ $pendingNotificationsTotal }}</p>
                                            <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">
                                                Total de votantes sin foto del certificado, agrupados por responsable.
                                            </p>

                                            <div class="mt-5 space-y-3 max-h-[360px] overflow-y-auto pr-1">
                                                @forelse ($employeeNotifications as $employee)
                                                    <button
                                                        type="button"
                                                        class="w-full rounded-xl border border-amber-200 bg-white p-4 text-left shadow-sm transition hover:border-amber-300 hover:bg-amber-50/70 dark:border-amber-900/40 dark:bg-slate-900 dark:hover:bg-amber-950/20"
                                                        @click="openDetalle({ id: {{ $employee->id }}, name: @js($employee->name), email: @js($employee->email), sede: @js($employee->sede), role: @js($employee->role), pendientes: {{ $employee->votantes_pendientes_count }}, confirmados: {{ $employee->votantes_confirmados_count }} })"
                                                    >
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div>
                                                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                                                            </div>

                                                            <svg class="mt-1 h-5 w-5 shrink-0 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="m9 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </div>

                                                        <div class="mt-3 flex flex-wrap gap-2">
                                                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">
                                                                {{ $employee->votantes_pendientes_count }} pendientes
                                                            </span>
                                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                                                {{ $employee->votantes_confirmados_count }} confirmados
                                                            </span>
                                                        </div>

                                                        <p class="mt-3 text-xs font-medium uppercase tracking-wide text-amber-700 dark:text-amber-300">
                                                            Haz clic para ver el detalle completo
                                                        </p>
                                                    </button>
                                                @empty
                                                    <div class="rounded-xl border border-dashed border-amber-300 bg-white p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-slate-900 dark:text-amber-200">
                                                No hay responsables con certificados pendientes.
                                            </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Carga por responsable</h4>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Cuantos votantes registra cada usuario responsable.
                                                </p>
                                            </div>

                                            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                                                <template x-if="selected">
                                                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/20">
                                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                            <div>
                                                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="selected.name"></p>
                                                                <p class="text-sm text-gray-600 dark:text-gray-300" x-text="selected.email"></p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selected.sede ? ('Sede: ' + selected.sede) : 'Sin sede'"></p>
                                                            </div>

                                                            <div class="flex flex-wrap gap-2">
                                                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">
                                                                    <span x-text="selected.pendientes ?? 0"></span> pendientes
                                                                </span>
                                                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                                                    <span x-text="selected.confirmados ?? 0"></span> confirmados
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="mt-4">
                                                            <template x-if="loading">
                                                                <div class="rounded-xl border border-dashed border-indigo-200 bg-white p-4 text-sm text-gray-500 dark:border-indigo-900/40 dark:bg-slate-900 dark:text-gray-300">
                                                                    Cargando pendientes...
                                                                </div>
                                                            </template>

                                                            <template x-if="error">
                                                                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200" x-text="error"></div>
                                                            </template>

                                                            <template x-if="!loading && !error">
                                                                <div>
                                                                    <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
                                                                        <template x-for="pendiente in detalle" :key="pendiente.id">
                                                                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                                                    <div>
                                                                                        <p class="font-semibold text-gray-900 dark:text-gray-100" x-text="`${pendiente.nombres} ${pendiente.apellidos}`"></p>
                                                                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="`${pendiente.tipo_identificacion} - ${pendiente.numero_identificacion}`"></p>
                                                                                    </div>
                                                                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-200" x-text="pendiente.estado_registro_label"></span>
                                                                                </div>

                                                                                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Teléfono</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.telefono || 'Sin dato'"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Departamento / Municipio</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="`${pendiente.departamento || 'Sin dato'} / ${pendiente.municipio || 'Sin dato'}`"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Puesto</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.puesto_votacion || 'Sin dato'"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Comuna</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.comuna || 'Sin dato'"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Dirección</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.direccion || 'Sin dato'"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Mesa</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.mesa_votacion || 'Sin dato'"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Relación</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.relacion || 'Sin dato'"></p>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Registrado</p>
                                                                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="pendiente.created_at || 'Sin dato'"></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                                                                        <button type="button" @click="prevPage()" :disabled="meta.current_page <= 1 || loading" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                                                            Anterior
                                                                        </button>
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="`Página ${meta.current_page} de ${meta.last_page}`"></p>
                                                                        <button type="button" @click="nextPage()" :disabled="meta.current_page >= meta.last_page || loading" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                                                            Siguiente
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                <template x-if="!selected">
                                                    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                        Selecciona un responsable para ver el listado de sus votantes pendientes.
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                                                        <tr>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Responsable</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Sede</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Confirmados</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pendientes</th>
                                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                                        @forelse ($employeeStats as $employee)
                                                            <tr>
                                                                <td class="px-5 py-4">
                                                                    <button
                                                                        type="button"
                                                                        class="text-left font-medium text-gray-900 transition hover:text-indigo-600 dark:text-gray-100 dark:hover:text-indigo-300"
                                                                        @click="openDetalle({ id: {{ $employee->id }}, name: @js($employee->name), email: @js($employee->email), sede: @js($employee->sede), role: @js($employee->role), pendientes: {{ $employee->votantes_pendientes_count }}, confirmados: {{ $employee->votantes_confirmados_count }} })"
                                                                    >
                                                                        {{ $employee->name }}
                                                                    </button>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Rol: {{ $employee->role }}</div>
                                                                </td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $employee->sede ?? 'Sin sede' }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $employee->email }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($employee->votantes_confirmados_count) }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-amber-700 dark:text-amber-300">{{ number_format($employee->votantes_pendientes_count) }}</td>
                                                                <td class="whitespace-nowrap px-5 py-4">
                                                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $employee->votantes_pendientes_count > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' }}">
                                                                        {{ $employee->votantes_pendientes_count > 0 ? 'Con pendientes' : 'Al día' }}
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->isAdmin() ? 'Votantes confirmados' : 'Mis votantes confirmados' }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalVotantes) }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->isAdmin() ? 'Votantes pendientes' : 'Mis votantes pendientes' }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->isAdmin() ? number_format($pendingVotantes) : number_format($myPendingVotantes) }}
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
                                                    <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide {{ $votante->estado_registro === 'pendiente' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' }}">
                                                        {{ $votante->estado_registro_label }}
                                                    </span>
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

