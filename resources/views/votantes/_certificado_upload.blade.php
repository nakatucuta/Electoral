@props(['votante'])

<form
    method="POST"
    action="{{ route('votantes.certificado.upload', $votante) }}"
    enctype="multipart/form-data"
    class="inline-flex shrink-0"
    x-data="submissionFeedback({ name: @js($votante->nombres . ' ' . $votante->apellidos) })"
    @submit.prevent="startSubmit($event)"
>
    @csrf
    <label
        class="inline-flex min-w-[11rem] cursor-pointer items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 ring-1 ring-inset ring-indigo-500/20 transition hover:bg-indigo-500 active:scale-[0.99]"
        title="{{ $votante->foto_certificado ? 'Cambiar certificado' : 'Subir certificado' }}"
        onclick="this.querySelector('input[type=file]').value=''"
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
            @change="prepareUpload($event)"
        >
    </label>

    <div
        x-cloak
        x-show="submitting"
        x-transition.opacity
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm"
        role="status"
        aria-live="polite"
        aria-label="Subiendo certificado"
    >
        <div
            class="w-full max-w-[min(24rem,calc(100vw-2rem))] rounded-2xl border p-6 text-center shadow-2xl shadow-black/30 dark:bg-gray-900"
            :class="submissionStage === 'error'
                ? 'border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-gray-900'
                : 'border-white/10 bg-white'"
        >
            <div class="mx-auto flex justify-center">
                <x-application-logo class="h-10 w-auto text-gray-900 dark:text-white" />
            </div>
            <div
                class="mx-auto mt-4 flex h-14 w-14 items-center justify-center rounded-full"
                :class="submissionStage === 'error' ? 'bg-red-600/10 text-red-600' : 'bg-indigo-600/10 text-indigo-600'"
            >
                <template x-if="submissionStage === 'error'">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 9v4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="12" cy="16.5" r="1" fill="currentColor"/>
                        <path d="M10.3 4.5h3.4L21 17.5a1.8 1.8 0 0 1-1.5 2.7H4.5A1.8 1.8 0 0 1 3 17.5L10.3 4.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </template>
                <template x-if="submissionStage !== 'error'">
                    <svg class="h-7 w-7 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" class="opacity-20" stroke="currentColor" stroke-width="3"></circle>
                        <path d="M21 12a9 9 0 0 1-9 9" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                    </svg>
                </template>
            </div>
            <p class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="stageMessage"></p>
            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-200" x-text="targetName"></p>

            <div x-show="submissionStage !== 'error'" class="mx-auto mt-4 flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                <template x-if="selectedFilePreview">
                    <img :src="selectedFilePreview" alt="Vista previa del certificado" class="h-full w-full object-cover">
                </template>
                <template x-if="!selectedFilePreview">
                    <div class="px-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <span>Archivo seleccionado</span>
                    </div>
                </template>
            </div>

            <p class="mt-3 break-words text-sm font-medium text-gray-900 dark:text-gray-100" x-text="selectedFileName"></p>
            <p
                x-show="errorMessage"
                x-text="errorMessage"
                class="mx-auto mt-3 max-w-full rounded-xl border border-red-200 bg-red-100 px-4 py-3 text-center text-[13px] font-medium leading-5 break-words whitespace-normal text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200"
            ></p>
            <p x-show="!errorMessage" class="mt-2 whitespace-normal break-words text-sm leading-6 text-gray-500 text-balance dark:text-gray-400">
                Vamos a dejar el certificado listo para que se refleje en el sistema.
            </p>
        </div>
    </div>
</form>
