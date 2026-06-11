@csrf

@php
    $tiposIdentificacion = [
        'cc' => 'Cédula de ciudadanía',
        'cd' => 'Cédula digital',
        'ce' => 'Cédula de extranjería',
        'pasaporte' => 'Pasaporte colombiano',
    ];

    $tipoSeleccionado = old('tipo_identificacion', $votante->tipo_identificacion ?? '');
@endphp
<div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Foto del certificado</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Carga una imagen clara y legible del certificado electoral.</p>

        @if (! empty($votante?->foto_certificado_url))
            <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <img src="{{ $votante->foto_certificado_url }}" alt="Certificado" class="h-72 w-full object-cover">
            </div>
        @endif

        <label class="mt-5 block">
            <span class="sr-only">Foto del certificado</span>
            <input type="file" name="foto_certificado" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500 dark:text-gray-400" @if(empty($votante?->foto_certificado)) required @endif>
        </label>
        @error('foto_certificado')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Datos personales</h3>

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div>
                    <x-label for="nombres" value="Nombres" />
                    <x-input id="nombres" name="nombres" type="text" class="mt-1 block w-full" :value="old('nombres', $votante->nombres ?? '')" required />
                    <x-input-error for="nombres" class="mt-2" />
                </div>

                <div>
                    <x-label for="apellidos" value="Apellidos" />
                    <x-input id="apellidos" name="apellidos" type="text" class="mt-1 block w-full" :value="old('apellidos', $votante->apellidos ?? '')" required />
                    <x-input-error for="apellidos" class="mt-2" />
                </div>

                <div>
                    <x-label for="tipo_identificacion" value="Tipo de identificación" />
                    <select id="tipo_identificacion" name="tipo_identificacion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                        <option value="" disabled @selected($tipoSeleccionado === '')>Selecciona un tipo</option>
                        @foreach ($tiposIdentificacion as $value => $label)
                            <option value="{{ $value }}" @selected($tipoSeleccionado === $value)>{{ $label }}</option>
                        @endforeach
                        @if ($tipoSeleccionado !== '' && ! array_key_exists($tipoSeleccionado, $tiposIdentificacion))
                            <option value="{{ $tipoSeleccionado }}" selected>{{ $tipoSeleccionado }}</option>
                        @endif
                    </select>
                    <x-input-error for="tipo_identificacion" class="mt-2" />
                </div>

                <div>
                    <x-label for="numero_identificacion" value="Número de identificación" />
                    <x-input
                        id="numero_identificacion"
                        name="numero_identificacion"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('numero_identificacion', $votante->numero_identificacion ?? '')"
                        x-model="numeroIdentificacion"
                        x-on:input.debounce.500ms="validarNumero()"
                        required
                    />
                    <div class="mt-2 min-h-5 text-sm">
                        <span x-show="numeroChequeando" class="text-gray-500">Validando número...</span>
                        <span x-show="!numeroChequeando && numeroMensaje" :class="numeroExiste ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'" x-text="numeroMensaje"></span>
                    </div>
                    <x-input-error for="numero_identificacion" class="mt-2" />
                </div>

                <div>
                    <x-label for="telefono" value="Número de teléfono" />
                    <x-input id="telefono" name="telefono" type="tel" class="mt-1 block w-full" :value="old('telefono', $votante->telefono ?? '')" placeholder="300 000 0000" required />
                    <x-input-error for="telefono" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Ubicación electoral</h3>
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 shadow-sm dark:border-indigo-900/40 dark:bg-indigo-500/10 dark:text-indigo-200">
                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M10 3.5v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        <path d="M6.5 9.5 10 13l3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4 16.5h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    Haz clic para elegir una opción. Si no aparece, escríbela.
                </div>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div class="relative" x-on:click.outside="closeSuggestions('departamento')">
                    <x-label for="departamento" value="Departamento" />
                    <div class="relative mt-1">
                        <x-input id="departamento" name="departamento" type="text" class="block w-full pr-10" x-model="departamento" x-on:focus="abrirCatalogo('departamento')" x-on:click="abrirCatalogo('departamento')" x-on:input.debounce.300ms="buscarCatalogo('departamento')" autocomplete="off" required />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div x-show="suggestionsVisible.departamento" x-transition class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-indigo-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900" style="display: none;">
                        <template x-if="suggestionsLoading.departamento && !suggestions.departamento.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Cargando opciones...</div>
                        </template>
                        <template x-if="!suggestionsLoading.departamento && !suggestions.departamento.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Escribe para buscar o agrega un valor nuevo.</div>
                        </template>
                        <template x-for="item in suggestions.departamento" :key="item">
                            <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left text-sm text-gray-700 transition hover:bg-indigo-50 dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800" x-text="item" x-on:click="selectValue('departamento', item)"></button>
                        </template>
                    </div>
                    <x-input-error for="departamento" class="mt-2" />
                </div>

                <div class="relative" x-on:click.outside="closeSuggestions('municipio')">
                    <x-label for="municipio" value="Municipio" />
                    <div class="relative mt-1">
                        <x-input id="municipio" name="municipio" type="text" class="block w-full pr-10" x-model="municipio" x-on:focus="abrirCatalogo('municipio')" x-on:click="abrirCatalogo('municipio')" x-on:input.debounce.300ms="buscarCatalogo('municipio')" autocomplete="off" required />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div x-show="suggestionsVisible.municipio" x-transition class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-indigo-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900" style="display: none;">
                        <template x-if="suggestionsLoading.municipio && !suggestions.municipio.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Cargando opciones...</div>
                        </template>
                        <template x-if="!suggestionsLoading.municipio && !suggestions.municipio.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Escribe para buscar o agrega un valor nuevo.</div>
                        </template>
                        <template x-for="item in suggestions.municipio" :key="item">
                            <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left text-sm text-gray-700 transition hover:bg-indigo-50 dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800" x-text="item" x-on:click="selectValue('municipio', item)"></button>
                        </template>
                    </div>
                    <x-input-error for="municipio" class="mt-2" />
                </div>

                <div class="relative" x-on:click.outside="closeSuggestions('puesto_votacion')">
                    <x-label for="puesto_votacion" value="Puesto de votación" />
                    <div class="relative mt-1">
                        <x-input id="puesto_votacion" name="puesto_votacion" type="text" class="block w-full pr-10" x-model="puesto_votacion" x-on:focus="abrirCatalogo('puesto_votacion')" x-on:click="abrirCatalogo('puesto_votacion')" x-on:input.debounce.300ms="buscarCatalogo('puesto_votacion')" autocomplete="off" required />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div x-show="suggestionsVisible.puesto_votacion" x-transition class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-indigo-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900" style="display: none;">
                        <template x-if="suggestionsLoading.puesto_votacion && !suggestions.puesto_votacion.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Cargando opciones...</div>
                        </template>
                        <template x-if="!suggestionsLoading.puesto_votacion && !suggestions.puesto_votacion.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Escribe para buscar o agrega un valor nuevo.</div>
                        </template>
                        <template x-for="item in suggestions.puesto_votacion" :key="item">
                            <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left text-sm text-gray-700 transition hover:bg-indigo-50 dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800" x-text="item" x-on:click="selectValue('puesto_votacion', item)"></button>
                        </template>
                    </div>
                    <x-input-error for="puesto_votacion" class="mt-2" />
                </div>

                <div class="relative" x-on:click.outside="closeSuggestions('comuna')">
                    <x-label for="comuna" value="Comuna" />
                    <div class="relative mt-1">
                        <x-input id="comuna" name="comuna" type="text" class="block w-full pr-10" x-model="comuna" x-on:focus="abrirCatalogo('comuna')" x-on:click="abrirCatalogo('comuna')" x-on:input.debounce.300ms="buscarCatalogo('comuna')" autocomplete="off" required />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div x-show="suggestionsVisible.comuna" x-transition class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-indigo-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900" style="display: none;">
                        <template x-if="suggestionsLoading.comuna && !suggestions.comuna.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Cargando opciones...</div>
                        </template>
                        <template x-if="!suggestionsLoading.comuna && !suggestions.comuna.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Escribe para buscar o agrega un valor nuevo.</div>
                        </template>
                        <template x-for="item in suggestions.comuna" :key="item">
                            <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left text-sm text-gray-700 transition hover:bg-indigo-50 dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800" x-text="item" x-on:click="selectValue('comuna', item)"></button>
                        </template>
                    </div>
                    <x-input-error for="comuna" class="mt-2" />
                </div>

                <div class="relative md:col-span-2" x-on:click.outside="closeSuggestions('direccion')">
                    <x-label for="direccion" value="Dirección" />
                    <div class="relative mt-1">
                        <x-input id="direccion" name="direccion" type="text" class="block w-full pr-10" x-model="direccion" x-on:focus="abrirCatalogo('direccion')" x-on:click="abrirCatalogo('direccion')" x-on:input.debounce.300ms="buscarCatalogo('direccion')" autocomplete="off" required />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div x-show="suggestionsVisible.direccion" x-transition class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-indigo-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900" style="display: none;">
                        <template x-if="suggestionsLoading.direccion && !suggestions.direccion.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Cargando opciones...</div>
                        </template>
                        <template x-if="!suggestionsLoading.direccion && !suggestions.direccion.length">
                            <div class="px-4 py-3 text-sm text-gray-500">Escribe para buscar o agrega un valor nuevo.</div>
                        </template>
                        <template x-for="item in suggestions.direccion" :key="item">
                            <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left text-sm text-gray-700 transition hover:bg-indigo-50 dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800" x-text="item" x-on:click="selectValue('direccion', item)"></button>
                        </template>
                    </div>
                    <x-input-error for="direccion" class="mt-2" />
                </div>

                <div>
                    <x-label for="mesa_votacion" value="Mesa de votación" />
                    <x-input id="mesa_votacion" name="mesa_votacion" type="text" class="mt-1 block w-full" :value="old('mesa_votacion', $votante->mesa_votacion ?? '')" required />
                    <x-input-error for="mesa_votacion" class="mt-2" />
                </div>

            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('votantes.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancelar
                </a>
                <x-button>
                    Guardar votante
                </x-button>
            </div>
        </div>
    </div>
</div>



