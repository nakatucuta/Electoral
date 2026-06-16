<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Edición</p>
            <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">Editar votante</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form
                method="POST"
                action="{{ route('votantes.update', $votante) }}"
                enctype="multipart/form-data"
                x-data="votanteForm({
                    checkUrl: @js(route('votantes.check-numero')),
                    searchUrl: @js(route('catalogos.ubicacion.search')),
                    ignoreId: @js($votante->id),
                    submitting: false,
                    initial: @js([
                        'numero_identificacion' => old('numero_identificacion', $votante->numero_identificacion),
                        'telefono' => old('telefono', $votante->telefono),
                        'departamento' => old('departamento', $votante->departamento),
                        'municipio' => old('municipio', $votante->municipio),
                        'puesto_votacion' => old('puesto_votacion', $votante->puesto_votacion),
                        'comuna' => old('comuna', $votante->comuna),
                        'direccion' => old('direccion', $votante->direccion),
                    ]),
                })"
                @submit.prevent="startSubmit($event)"
                x-init="init()"
            >
                @method('PUT')
                @include('votantes._form')
            </form>
        </div>
    </div>
</x-app-layout>
