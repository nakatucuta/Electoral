@props(['votante'])

<form method="POST" action="{{ route('votantes.certificado.upload', $votante) }}" enctype="multipart/form-data" class="inline-flex shrink-0">
    @csrf
    <label
        class="inline-flex min-w-[11rem] cursor-pointer items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 ring-1 ring-inset ring-indigo-500/20 transition hover:bg-indigo-500 active:scale-[0.99]"
        title="{{ $votante->foto_certificado ? 'Cambiar certificado' : 'Subir certificado' }}"
    >
        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 16V4m0 0 4 4m-4-4-4 4M5 20h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="whitespace-nowrap">{{ $votante->foto_certificado ? 'Cambiar certificado' : 'Subir certificado' }}</span>
        <input
            type="file"
            name="foto_certificado"
            accept="image/*"
            class="sr-only"
            required
            onchange="this.form.submit()"
        >
    </label>
</form>
