<template>
    <main class="relative min-h-screen overflow-hidden bg-[#f3f6fb] text-slate-950">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,#f8fbff_0%,#eef4ff_42%,#f7f4ff_100%)]"></div>
        <div class="absolute inset-x-0 top-0 h-2 bg-[linear-gradient(90deg,#2563eb,#7c3aed,#14b8a6,#f59e0b)]"></div>
        <div class="pointer-events-none absolute inset-0 login-grid opacity-70"></div>

        <section class="relative z-10 grid min-h-screen items-stretch lg:grid-cols-[1.08fr_0.92fr]">
            <div class="relative hidden overflow-hidden bg-[#101827] px-10 py-9 text-white lg:flex lg:flex-col">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(37,99,235,0.34),rgba(124,58,237,0.2)_42%,rgba(20,184,166,0.18))]"></div>
                <div class="pointer-events-none absolute inset-0 login-scan"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-[#4f46e5] shadow-2xl shadow-indigo-950/30">
                            <span class="text-xl font-black">E</span>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-white/56">Centro operativo</p>
                            <h1 class="text-xl font-semibold">{{ appName }}</h1>
                        </div>
                    </div>

                    <div class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold text-white/80 backdrop-blur">
                        Acceso por cedula
                    </div>
                </div>

                <div class="relative flex flex-1 flex-col justify-center">
                    <div class="max-w-2xl">
                        <p class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-medium text-cyan-100">
                            Plataforma electoral profesional
                        </p>

                        <h2 class="mt-8 max-w-2xl text-6xl font-black leading-[0.95] tracking-tight">
                            Gestiona tu operacion con claridad total.
                        </h2>

                        <p class="mt-6 max-w-xl text-lg leading-8 text-white/72">
                            Un acceso moderno para registrar votantes, revisar pendientes y tomar decisiones con datos confiables.
                        </p>
                    </div>

                    <div class="mt-10 grid max-w-2xl grid-cols-3 gap-3">
                        <div
                            v-for="metric in metrics"
                            :key="metric.label"
                            class="rounded-2xl border border-white/12 bg-white/10 p-4 backdrop-blur transition duration-500 hover:-translate-y-1 hover:bg-white/15"
                        >
                            <p class="text-3xl font-black">{{ metric.value }}</p>
                            <p class="mt-2 text-xs font-medium uppercase text-white/55">{{ metric.label }}</p>
                        </div>
                    </div>
                </div>

                <div class="relative rounded-[28px] border border-white/12 bg-white/10 p-4 backdrop-blur">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold">Flujo de trabajo</p>
                            <p class="mt-1 text-xs text-white/55">Ingreso, registro, seguimiento y estadisticas.</p>
                        </div>
                        <div class="flex gap-2">
                            <span
                                v-for="step in 4"
                                :key="step"
                                class="h-2 w-8 rounded-full transition"
                                :class="activeStep >= step ? 'bg-emerald-300' : 'bg-white/18'"
                            ></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
                <Transition name="login-card" appear>
                    <div class="w-full max-w-[460px]">
                        <div class="mb-7 flex items-center justify-between lg:hidden">
                            <div class="flex items-center gap-3">
                                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/25">
                                    <span class="text-xl font-black">E</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase text-slate-500">Sistema</p>
                                    <h1 class="text-xl font-bold text-slate-950">{{ appName }}</h1>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[32px] border border-white bg-white/90 p-7 shadow-[0_24px_90px_rgba(15,23,42,0.16)] backdrop-blur-xl sm:p-9">
                            <div class="mb-8">
                                <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-bold uppercase text-indigo-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Acceso seguro
                                </div>
                                <h2 class="text-4xl font-black tracking-tight text-slate-950">Inicia sesion</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    Entra con tu numero de cedula y continua con la gestion electoral.
                                </p>
                            </div>

                            <div v-if="status" class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ status }}
                            </div>

                            <div v-if="hasErrors" class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                Revisa los datos e intenta nuevamente.
                            </div>

                            <form :action="loginAction" method="POST" class="space-y-5" @submit="submitting = true">
                                <input type="hidden" name="_token" :value="csrfToken">

                                <label class="block">
                                    <span class="mb-2 block text-sm font-bold text-slate-700">Numero de cedula</span>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M7 8h10M7 12h5M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <input
                                            v-model="documentoIdentificacion"
                                            name="documento_identificacion"
                                            type="text"
                                            inputmode="numeric"
                                            autocomplete="username"
                                            required
                                            autofocus
                                            class="block h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-base font-semibold text-slate-950 outline-none transition duration-300 placeholder:text-slate-400 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                            :class="documentoError ? 'border-rose-400 bg-rose-50 focus:border-rose-500 focus:ring-rose-100' : ''"
                                            placeholder="1118851434"
                                        >
                                    </div>
                                    <span v-if="documentoError" class="mt-2 block text-sm text-rose-600">{{ documentoError }}</span>
                                </label>

                                <label class="block">
                                    <span class="mb-2 block text-sm font-bold text-slate-700">Contrasena</span>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M7 10V8a5 5 0 0 1 10 0v2M6 10h12v10H6V10Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <input
                                            v-model="password"
                                            :type="showPassword ? 'text' : 'password'"
                                            name="password"
                                            autocomplete="current-password"
                                            required
                                            class="block h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-20 text-base font-semibold text-slate-950 outline-none transition duration-300 placeholder:text-slate-400 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                            :class="passwordError ? 'border-rose-400 bg-rose-50 focus:border-rose-500 focus:ring-rose-100' : ''"
                                            placeholder="Tu clave"
                                        >
                                        <button
                                            type="button"
                                            class="absolute inset-y-2 right-2 rounded-xl px-3 text-sm font-bold text-indigo-600 transition hover:bg-indigo-50"
                                            @click="showPassword = !showPassword"
                                        >
                                            {{ showPassword ? 'Ocultar' : 'Ver' }}
                                        </button>
                                    </div>
                                    <span v-if="passwordError" class="mt-2 block text-sm text-rose-600">{{ passwordError }}</span>
                                </label>

                                <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                                    <label class="inline-flex items-center gap-3 text-slate-600">
                                        <input
                                            v-model="remember"
                                            type="checkbox"
                                            name="remember"
                                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        >
                                        Mantener sesion
                                    </label>

                                    <a
                                        v-if="canResetPassword"
                                        :href="forgotPasswordUrl"
                                        class="font-bold text-indigo-600 transition hover:text-indigo-800"
                                    >
                                        Recuperar clave
                                    </a>
                                </div>

                                <button
                                    type="submit"
                                    class="group relative flex h-14 w-full items-center justify-center overflow-hidden rounded-2xl bg-slate-950 px-5 text-sm font-black uppercase text-white shadow-2xl shadow-slate-950/20 transition duration-300 hover:-translate-y-0.5 hover:shadow-indigo-500/30"
                                    :class="submitting ? 'pointer-events-none opacity-85' : ''"
                                >
                                    <span class="absolute inset-0 translate-x-[-105%] bg-[linear-gradient(90deg,transparent,rgba(255,255,255,0.28),transparent)] transition duration-700 group-hover:translate-x-[105%]"></span>
                                    <span class="relative flex items-center gap-2">
                                        {{ submitting ? 'Ingresando...' : 'Ingresar al sistema' }}
                                        <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </Transition>
            </div>
        </section>
    </main>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';

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
const submitting = ref(false);
const activeStep = ref(1);
let stepTimer = null;

const metrics = [
    { value: 'Seguro', label: 'Acceso protegido' },
    { value: 'Rápido', label: 'Ingreso directo' },
    { value: 'Claro', label: 'Interfaz simple' },
];

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

onMounted(() => {
    stepTimer = window.setInterval(() => {
        activeStep.value = activeStep.value >= 4 ? 1 : activeStep.value + 1;
    }, 1300);
});

onBeforeUnmount(() => {
    if (stepTimer) {
        window.clearInterval(stepTimer);
    }
});
</script>

<style scoped>
.login-grid {
    background-image:
        linear-gradient(rgba(15, 23, 42, 0.055) 1px, transparent 1px),
        linear-gradient(90deg, rgba(15, 23, 42, 0.055) 1px, transparent 1px);
    background-size: 42px 42px;
    mask-image: linear-gradient(180deg, transparent 0%, black 18%, black 86%, transparent 100%);
}

.login-scan {
    background:
        linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.08) 46%, transparent 52%),
        repeating-linear-gradient(0deg, rgba(255,255,255,0.04) 0 1px, transparent 1px 10px);
    background-size: 220% 100%, auto;
    animation: scan 7s linear infinite;
}

.login-card-enter-active {
    transition: opacity 700ms ease, transform 700ms cubic-bezier(.16, 1, .3, 1), filter 700ms ease;
}

.login-card-enter-from {
    opacity: 0;
    transform: translateY(28px) scale(0.98);
    filter: blur(12px);
}

@keyframes scan {
    from { background-position: 130% 0, 0 0; }
    to { background-position: -130% 0, 0 0; }
}

@media (prefers-reduced-motion: reduce) {
    .login-scan {
        animation: none;
    }

    .login-card-enter-active {
        transition: none;
    }
}
</style>
