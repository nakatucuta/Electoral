<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-1">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ficha del votante</p>
                <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $votante->nombres }} {{ $votante->apellidos }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Consulta la información completa, el estado del certificado y el historial de cambios.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('votantes.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Volver al listado
                </a>
                <a href="{{ route('votantes.edit', $votante) }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                    Editar registro
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                    <p class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $votante->estado_registro === 'pendiente' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' }}">
                        {{ $votante->estado_registro_label }}
                    </p>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">El estado cambia automáticamente cuando se carga el certificado.</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Responsable</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $votante->user?->name ?? 'Sin dato' }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $votante->user?->email ?? 'Sin correo' }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Certificado</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $votante->foto_certificado ? 'Cargado' : 'Pendiente' }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Última actualización: {{ optional($votante->updated_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
                <div class="space-y-6">
                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Certificado electoral</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Vista actual del soporte cargado.</p>
                            </div>
                            @if ($votante->foto_certificado)
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Visible</span>
                            @endif
                        </div>

                        @if ($votante->foto_certificado_url)
                            <div class="mt-5 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                                <img src="{{ $votante->foto_certificado_url }}" alt="{{ $votante->nombres }}" class="h-80 w-full object-cover">
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Sin imagen cargada.
                            </div>
                        @endif

                        @include('votantes._certificado_upload', ['votante' => $votante])
                    </div>

                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Registro técnico</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-start justify-between gap-4 rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">
                                <dt class="text-gray-500 dark:text-gray-400">Registrado por</dt>
                                <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ $votante->user?->name ?? 'Sin dato' }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-4 rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">
                                <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                                <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ $votante->user?->email ?? 'Sin dato' }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-4 rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">
                                <dt class="text-gray-500 dark:text-gray-400">Creado</dt>
                                <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ optional($votante->created_at)->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-4 rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900/50">
                                <dt class="text-gray-500 dark:text-gray-400">Actualizado</dt>
                                <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ optional($votante->updated_at)->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-5 dark:border-gray-700">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Información del votante</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Datos personales y ubicación electoral registrados en la plataforma.</p>
                        </div>
                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                            ID {{ $votante->numero_identificacion }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nombres</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->nombres }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Apellidos</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->apellidos }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tipo de identificación</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->tipo_identificacion }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Número de identificación</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->numero_identificacion }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Número de teléfono</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->telefono ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Departamento</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->departamento ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Municipio</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->municipio ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Puesto de votación</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->puesto_votacion ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Comuna</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->comuna ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50 md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Dirección</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->direccion ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Mesa de votación</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->mesa_votacion ?? 'Sin dato' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Relación</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $votante->relacion ?? 'Sin dato' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Historial del registro</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cambios, cargas de certificado y acciones recientes.</p>
                            </div>
                            <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                {{ $votante->audits->count() }} eventos
                            </span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($votante->audits->take(8) as $audit)
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $audit->title }}</p>
                                        <span class="inline-flex rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                            {{ str_replace('_', ' ', $audit->action) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $audit->user?->name ?? 'Sistema' }} · {{ optional($audit->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                    @if (! empty($audit->details))
                                        <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                                            @foreach ($audit->details as $label => $value)
                                                <div class="rounded-xl bg-white px-3 py-2 dark:bg-gray-800">
                                                    <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                                                    <dd class="mt-1 break-words font-medium text-gray-900 dark:text-gray-100">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Aún no hay historial para este registro.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
