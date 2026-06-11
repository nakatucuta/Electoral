<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ auth()->user()->isAdmin() ? 'Todos los registros' : 'Mis registros' }}</p>
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">Votantes</h2>
            </div>
            <a href="{{ route('votantes.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">Nuevo votante</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <form method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                        <input id="search" type="text" name="search" value="{{ $search }}" placeholder="Nombres, apellidos, departamento, municipio, puesto, comuna, dirección..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div class="flex gap-3">
                        <x-secondary-button type="submit">Filtrar</x-secondary-button>
                        <a href="{{ route('votantes.index') }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Limpiar</a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
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
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700">
                                                @if ($votante->foto_certificado_url)
                                                    <img src="{{ $votante->foto_certificado_url }}" alt="{{ $votante->nombres }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-[10px] font-semibold text-gray-500">SIN FOTO</span>
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
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('votantes.show', $votante) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">Ver</a>
                                            <a href="{{ route('votantes.edit', $votante) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">Editar</a>
                                            <form method="POST" action="{{ route('votantes.destroy', $votante) }}" onsubmit="return confirm('Eliminar este votante?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50 dark:border-red-900/50 dark:text-red-300 dark:hover:bg-red-500/10">Eliminar</button>
                                            </form>
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
</x-app-layout>
