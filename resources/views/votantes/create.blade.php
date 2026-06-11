<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nuevo registro</p>
            <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">Registrar votante</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form
                method="POST"
                action="{{ route('votantes.store') }}"
                enctype="multipart/form-data"
                x-data="votanteForm({
                    checkUrl: @js(route('votantes.check-numero')),
                    searchUrl: @js(route('catalogos.ubicacion.search')),
                    ignoreId: null,
                    initial: @js([
                        'numero_identificacion' => old('numero_identificacion', ''),
                        'telefono' => old('telefono', ''),
                        'departamento' => old('departamento', ''),
                        'municipio' => old('municipio', ''),
                        'puesto_votacion' => old('puesto_votacion', ''),
                        'comuna' => old('comuna', ''),
                        'direccion' => old('direccion', ''),
                    ]),
                })"
                x-init="init()"
            >
                @php($votante = null)
                @include('votantes._form')
            </form>
        </div>
    </div>
</x-app-layout>
