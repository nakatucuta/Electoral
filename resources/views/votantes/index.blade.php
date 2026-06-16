<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-1">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ auth()->user()->isAdmin() ? 'Todos los registros' : 'Mis registros' }}</p>
                <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">Votantes</h2>
                <p class="max-w-2xl text-sm text-gray-500 dark:text-gray-400">Consulta, filtra y administra los votantes con una vista más clara para el trabajo operativo diario.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('votantes.export.excel', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-500">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v10m0 0 4-4m-4 4-4-4" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M5 19h14" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('votantes.export.pdf', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 3h7l5 5v13H7z" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14 3v6h6" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('votantes.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">Nuevo votante</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Visibles</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totales['visibles']) }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registros que cumplen los filtros actuales.</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10">
                    <p class="text-sm text-emerald-700 dark:text-emerald-200">Confirmados</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-800 dark:text-emerald-100">{{ number_format($totales['confirmados']) }}</p>
                    <p class="mt-1 text-sm text-emerald-700/80 dark:text-emerald-200/80">Con certificado cargado.</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10">
                    <p class="text-sm text-amber-700 dark:text-amber-200">Pendientes</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-800 dark:text-amber-100">{{ number_format($totales['pendientes']) }}</p>
                    <p class="mt-1 text-sm text-amber-700/80 dark:text-amber-200/80">Esperando carga del certificado.</p>
                </div>
            </div>

            <div x-data="{ open: false }" class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between gap-4 px-6 py-5">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Filtros avanzados</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Busca por responsable, ubicación, relación, estado y fecha.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('votantes.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">Limpiar</a>
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 6h16M7 12h10M10 18h4" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span x-text="open ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
                        </button>
                    </div>
                </div>

                <div x-show="open" x-transition.opacity style="display: none;" class="border-t border-gray-200 px-6 py-6 dark:border-gray-700">
                <form method="GET" class="grid gap-4 lg:grid-cols-6">
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="M20 20l-3.5-3.5"></path>
                                </svg>
                            </span>
                            <input id="search" type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, documento, teléfono..." class="block w-full rounded-xl border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <div>
                            <label for="responsable" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsable</label>
                            <select id="responsable" name="responsable" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Todos</option>
                                @foreach ($responsables as $responsable)
                                    <option value="{{ $responsable['id'] }}" @selected(($filters['responsable'] ?? '') == $responsable['id'])>{{ $responsable['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                        <select id="estado" name="estado" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Todos</option>
                            <option value="confirmado" @selected(($filters['estado'] ?? '') === 'confirmado')>Confirmados</option>
                            <option value="pendiente" @selected(($filters['estado'] ?? '') === 'pendiente')>Pendientes</option>
                        </select>
                    </div>

                    <div>
                        <label for="relacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Relación</label>
                        <select id="relacion" name="relacion" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Todas</option>
                            @foreach ($relaciones as $item)
                                <option value="{{ $item }}" @selected(($filters['relacion'] ?? '') === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha_desde" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desde</label>
                        <input id="fecha_desde" type="date" name="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hasta</label>
                        <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div class="lg:col-span-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departamento</label>
                            <select id="departamento" name="departamento" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Todos</option>
                                @foreach ($departamentos as $item)
                                    <option value="{{ $item }}" @selected(($filters['departamento'] ?? '') === $item)>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="municipio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Municipio</label>
                            <select id="municipio" name="municipio" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Todos</option>
                                @foreach ($municipios as $item)
                                    <option value="{{ $item }}" @selected(($filters['municipio'] ?? '') === $item)>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="puesto_votacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto</label>
                            <select id="puesto_votacion" name="puesto_votacion" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Todos</option>
                                @foreach ($puestos as $item)
                                    <option value="{{ $item }}" @selected(($filters['puesto_votacion'] ?? '') === $item)>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="mesa_votacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mesa</label>
                            <select id="mesa_votacion" name="mesa_votacion" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Todas</option>
                                @foreach ($mesas as $item)
                                    <option value="{{ $item }}" @selected(($filters['mesa_votacion'] ?? '') === $item)>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 lg:col-span-6">
                        <x-button type="submit">Aplicar filtros</x-button>
                    </div>
                </form>
                </div>
            </div>

            <div class="space-y-4 md:hidden">
                @forelse ($votantes as $votante)
                    <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start gap-3">
                            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-700">
                                @if ($votante->foto_certificado_url)
                                    <img src="{{ $votante->foto_certificado_url }}" alt="{{ $votante->nombres }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-[10px] font-semibold text-gray-500">SIN FOTO</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $votante->nombres }} {{ $votante->apellidos }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $votante->tipo_identificacion }} - {{ $votante->numero_identificacion }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide {{ $votante->estado_registro === 'pendiente' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' }}">
                                        {{ $votante->estado_registro_label }}
                                    </span>
                                </div>

                                <div class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                    <p><span class="font-medium">Teléfono:</span> {{ $votante->telefono ?? 'Sin dato' }}</p>
                                    <p><span class="font-medium">Ubicación:</span> {{ $votante->departamento ?? 'Sin dato' }} / {{ $votante->municipio ?? 'Sin dato' }}</p>
                                    <p><span class="font-medium">Puesto:</span> {{ $votante->puesto_votacion ?? 'Sin dato' }}</p>
                                    <p><span class="font-medium">Mesa:</span> {{ $votante->mesa_votacion ?? 'Sin dato' }}</p>
                                </div>

                                @if (auth()->user()->isAdmin())
                                    <div class="mt-3 rounded-xl bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $votante->user?->name }}</p>
                                        <p>{{ $votante->user?->email }}</p>
                                    </div>
                                @endif

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    @include('votantes._certificado_upload', ['votante' => $votante])
                                    <a href="{{ route('votantes.show', $votante) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">Ver</a>
                                    <a href="{{ route('votantes.edit', $votante) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">Editar</a>
                                    @if (auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('votantes.destroy', $votante) }}" onsubmit="return confirm('Eliminar este votante?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-700 transition hover:bg-red-50 dark:border-red-900/50 dark:text-red-300 dark:hover:bg-red-500/10">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        No hay votantes para mostrar.
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/90 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Votante</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ubicación electoral</th>
                                @if (auth()->user()->isAdmin())
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Registrado por</th>
                                @endif
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($votantes as $votante)
                                <tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-900/40">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-700">
                                                @if ($votante->foto_certificado_url)
                                                    <img src="{{ $votante->foto_certificado_url }}" alt="{{ $votante->nombres }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-[10px] font-semibold text-gray-500">SIN FOTO</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $votante->nombres }} {{ $votante->apellidos }}</p>
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
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            @include('votantes._certificado_upload', ['votante' => $votante])
                                            <a href="{{ route('votantes.show', $votante) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">Ver</a>
                                            <a href="{{ route('votantes.edit', $votante) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">Editar</a>
                                            @if (auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('votantes.destroy', $votante) }}" onsubmit="return confirm('Eliminar este votante?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-700 transition hover:bg-red-50 dark:border-red-900/50 dark:text-red-300 dark:hover:bg-red-500/10">Eliminar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-6 py-16 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay votantes para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    {{ $votantes->links() }}
                </div>
            </div>
        </div>
    </div>

    @if (! empty($notificationToasts) && $notificationToasts->count())
        <div
            x-data="toastStack({ initialToasts: @js($notificationToasts) })"
            class="pointer-events-none fixed bottom-4 right-4 z-[80] flex w-full max-w-sm flex-col gap-3 px-4 sm:bottom-6 sm:right-6 sm:px-0"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                    class="pointer-events-auto overflow-hidden rounded-2xl border shadow-xl backdrop-blur-sm"
                    :class="toneClasses(toast.tone).panel"
                >
                    <div class="flex items-start gap-3 p-4">
                        <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/70 dark:bg-black/20">
                            <svg class="h-5 w-5" :class="toneClasses(toast.tone).icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 6v6l4 2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold" x-text="toast.title"></p>
                                    <p class="mt-1 text-sm leading-5 opacity-90" x-text="toast.message"></p>
                                </div>
                                <button type="button" @click="dismiss(toast.id)" class="rounded-lg p-1 transition hover:bg-black/5 dark:hover:bg-white/10" aria-label="Cerrar notificación">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide" :class="toneClasses(toast.tone).badge" x-text="toast.subtext"></span>
                                <span class="inline-flex rounded-full bg-white/70 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-700 dark:bg-black/20 dark:text-gray-100" x-text="toast.count ? `${toast.count} pendientes` : 'Novedad'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif
</x-app-layout>
