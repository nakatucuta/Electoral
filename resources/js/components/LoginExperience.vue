<template>
    <div class="min-h-screen bg-[#f4f7fb] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-7xl overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.12)]">
            <section class="hidden w-0 flex-1 bg-[radial-gradient(circle_at_top_left,_rgba(124,58,237,0.22),_transparent_32%),linear-gradient(135deg,_#1e1b4b_0%,_#312e81_38%,_#5b21b6_100%)] p-10 text-white xl:flex xl:flex-col xl:justify-between">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-3 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        Plataforma electoral segura
                    </div>

                    <h1 class="mt-8 text-5xl font-semibold leading-tight tracking-tight">
                        {{ appName }}
                    </h1>

                    <p class="mt-5 max-w-lg text-lg leading-8 text-white/82">
                        Ingresa a un panel diseñado para equipos electorales: ordenado, rápido y claro para trabajar sin fricción.
                    </p>

                    <div class="mt-10 grid max-w-xl grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-white/12 bg-white/10 p-5 backdrop-blur">
                            <p class="text-sm text-white/70">Acceso</p>
                            <p class="mt-2 text-2xl font-semibold">Rápido y claro</p>
                        </div>
                        <div class="rounded-2xl border border-white/12 bg-white/10 p-5 backdrop-blur">
                            <p class="text-sm text-white/70">Experiencia</p>
                            <p class="mt-2 text-2xl font-semibold">Más moderna</p>
                        </div>
                    </div>
                </div>

                <div class="max-w-xl rounded-3xl border border-white/12 bg-white/10 p-6 backdrop-blur">
                    <p class="text-sm uppercase tracking-[0.22em] text-white/60">Flujo principal</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-white/10 px-4 py-3">
                            <p class="text-sm text-white/70">1. Accede</p>
                            <p class="mt-1 font-medium">Con tu usuario</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-4 py-3">
                            <p class="text-sm text-white/70">2. Gestiona</p>
                            <p class="mt-1 font-medium">Tu información</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-4 py-3">
                            <p class="text-sm text-white/70">3. Continúa</p>
                            <p class="mt-1 font-medium">Sin distracciones</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="flex w-full flex-1 items-center justify-center bg-[linear-gradient(180deg,_#ffffff_0%,_#f8fafc_100%)] px-5 py-8 sm:px-8 lg:px-12">
                <div class="w-full max-w-md">
                    <div class="mb-6 flex items-center gap-3 xl:hidden">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/30">
                            <span class="text-xl font-bold">E</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Sistema</p>
                            <h2 class="text-xl font-semibold text-slate-900">{{ appName }}</h2>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-8 shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
                        <div class="mb-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Bienvenido</p>
                            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Inicia sesión</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Accede para administrar votantes, estadísticas y novedades.
                            </p>
                        </div>

                        <div v-if="status" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ status }}
                        </div>

                        <div v-if="hasErrors" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Revisa los campos marcados y vuelve a intentarlo.
                        </div>

                        <form :action="loginAction" method="POST" class="space-y-5">
                            <input type="hidden" name="_token" :value="csrfToken">

                            <div>
                                <label for="documento_identificacion" class="mb-2 block text-sm font-medium text-slate-700">Número de cédula</label>
                                <input
                                    id="documento_identificacion"
                                    v-model="documentoIdentificacion"
                                    type="text"
                                    name="documento_identificacion"
                                    autocomplete="username"
                                    required
                                    autofocus
                                    inputmode="numeric"
                                    class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    :class="documentoError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : ''"
                                >
                                <p v-if="documentoError" class="mt-2 text-sm text-rose-600">{{ documentoError }}</p>
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Contraseña</label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        autocomplete="current-password"
                                        required
                                        class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-14 text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                        :class="passwordError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : ''"
                                    >
                                    <button
                                        type="button"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-sm font-medium text-slate-500 transition hover:text-slate-900"
                                        @click="showPassword = !showPassword"
                                    >
                                        {{ showPassword ? 'Ocultar' : 'Ver' }}
                                    </button>
                                </div>
                                <p v-if="passwordError" class="mt-2 text-sm text-rose-600">{{ passwordError }}</p>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input
                                        v-model="remember"
                                        type="checkbox"
                                        name="remember"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                    Mantener sesión abierta
                                </label>

                                <a
                                    v-if="canResetPassword"
                                    :href="forgotPasswordUrl"
                                    class="text-sm font-medium text-indigo-600 transition hover:text-indigo-800"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <button
                                type="submit"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-500/40"
                            >
                                <span>Ingresar al sistema</span>
                                <span class="transition group-hover:translate-x-0.5">→</span>
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    appName: { type: String, required: true },
    loginAction: { type: String, required: true },
    csrfToken: { type: String, required: true },
    documentoValue: { type: String, default: '' },
    status: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
    canResetPassword: { type: Boolean, default: false },
    forgotPasswordUrl: { type: String, default: '#' },
});

const documentoIdentificacion = ref(props.documentoValue);
const password = ref('');
const remember = ref(true);
const showPassword = ref(false);

const firstError = (field) => {
    const value = props.errors?.[field];

    if (Array.isArray(value)) {
        return value[0] ?? '';
    }

    return typeof value === 'string' ? value : '';
};

const documentoError = computed(() => firstError('documento_identificacion'));
const passwordError = computed(() => firstError('password'));
const hasErrors = computed(() => Boolean(documentoError.value || passwordError.value));
</script>
