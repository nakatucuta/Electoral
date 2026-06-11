<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ficha del votante</p>
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">{{ $votante->nombres }} {{ $votante->apellidos }}</h2>
            </div>
            <a href="{{ route('votantes.edit', $votante) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Editar registro</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl grid gap-6 px-4 sm:px-6 lg:grid-cols-[320px_minmax(0,1fr)] lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Certificado electoral</h3>
                @if ($votante->foto_certificado_url)
                    <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                        <img src="{{ $votante->foto_certificado_url }}" alt="{{ $votante->nombres }}" class="h-80 w-full object-cover">
                    </div>
                @else
                    <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Sin imagen cargada.</div>
                @endif

                <div class="mt-6 rounded-xl bg-gray-50 p-4 dark:bg-gray-900/60">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Registrado por</p>
                    <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $votante->user?->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $votante->user?->email }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nombres</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->nombres }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Apellidos</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->apellidos }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tipo de identificacion</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->tipo_identificacion }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Numero de identificacion</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->numero_identificacion }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Número de teléfono</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->telefono ?? 'Sin dato' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Departamento</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->departamento ?? 'Sin dato' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Municipio</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->municipio ?? 'Sin dato' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Puesto de votacion</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->puesto_votacion ?? 'Sin dato' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Comuna</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->comuna ?? 'Sin dato' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Dirección</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->direccion ?? 'Sin dato' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Mesa de votacion</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->mesa_votacion }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Registrador</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->user?->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
