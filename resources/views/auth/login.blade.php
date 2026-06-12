<x-guest-layout>
    @php
        $loginProps = [
            'appName' => config('app.name', 'Electoral'),
            'loginAction' => route('login'),
            'csrfToken' => csrf_token(),
            'documentoValue' => old('documento_identificacion', ''),
            'status' => session('status', ''),
            'errors' => $errors->getMessages(),
            'canResetPassword' => Route::has('password.request'),
            'forgotPasswordUrl' => route('password.request'),
        ];
    @endphp

    <style>
        .login-grid {
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.055) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(180deg, transparent 0%, black 18%, black 86%, transparent 100%);
        }

        .login-scan {
            background:
                linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.12) 46%, transparent 54%),
                repeating-linear-gradient(0deg, rgba(255,255,255,0.04) 0 1px, transparent 1px 10px);
            background-size: 220% 100%, auto;
            animation: loginScan 6s linear infinite;
        }

        .login-float {
            animation: loginFloat 4s ease-in-out infinite;
        }

        .login-card-in {
            animation: loginCardIn 760ms cubic-bezier(.16, 1, .3, 1) both;
        }

        .login-shine::before {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-110%);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.30), transparent);
            transition: transform 700ms ease;
        }

        .login-shine:hover::before {
            transform: translateX(110%);
        }

        @keyframes loginScan {
            from { background-position: 130% 0, 0 0; }
            to { background-position: -130% 0, 0 0; }
        }

        @keyframes loginFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        @keyframes loginCardIn {
            from { opacity: 0; transform: translateY(28px) scale(.98); filter: blur(10px); }
            to { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-scan,
            .login-float,
            .login-card-in {
                animation: none;
            }
        }
    </style>

    <div id="login-fallback" class="relative min-h-screen overflow-hidden bg-[#f3f6fb] text-slate-950">
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
                            <h1 class="text-xl font-semibold">{{ config('app.name', 'Electoral') }}</h1>
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
                        <div class="login-float rounded-2xl border border-white/12 bg-white/10 p-4 backdrop-blur">
                            <p class="text-3xl font-black">360</p>
                            <p class="mt-2 text-xs font-medium uppercase text-white/55">Empleados base</p>
                        </div>
                        <div class="login-float rounded-2xl border border-white/12 bg-white/10 p-4 backdrop-blur [animation-delay:180ms]">
                            <p class="text-3xl font-black">24/7</p>
                            <p class="mt-2 text-xs font-medium uppercase text-white/55">Seguimiento</p>
                        </div>
                        <div class="login-float rounded-2xl border border-white/12 bg-white/10 p-4 backdrop-blur [animation-delay:360ms]">
                            <p class="text-3xl font-black">100%</p>
                            <p class="mt-2 text-xs font-medium uppercase text-white/55">Control</p>
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
                            <span class="h-2 w-8 rounded-full bg-emerald-300"></span>
                            <span class="h-2 w-8 rounded-full bg-emerald-300/70"></span>
                            <span class="h-2 w-8 rounded-full bg-white/24"></span>
                            <span class="h-2 w-8 rounded-full bg-white/24"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
                <div class="login-card-in w-full max-w-[460px]">
                    <div class="mb-7 flex items-center justify-between lg:hidden">
                        <div class="flex items-center gap-3">
                            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/25">
                                <span class="text-xl font-black">E</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-500">Sistema</p>
                                <h1 class="text-xl font-bold text-slate-950">{{ config('app.name', 'Electoral') }}</h1>
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

                        <x-validation-errors class="mb-5" />

                        @session('status')
                            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ $value }}
                            </div>
                        @endsession

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <label class="block">
                                <span class="mb-2 block text-sm font-bold text-slate-700">Numero de cedula</span>
                                <input
                                    id="documento_identificacion"
                                    name="documento_identificacion"
                                    type="text"
                                    value="{{ old('documento_identificacion') }}"
                                    inputmode="numeric"
                                    autocomplete="username"
                                    required
                                    autofocus
                                    placeholder="1118851434"
                                    class="block h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base font-semibold text-slate-950 outline-none transition duration-300 placeholder:text-slate-400 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                >
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-sm font-bold text-slate-700">Contrasena</span>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    placeholder="Tu clave"
                                    class="block h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base font-semibold text-slate-950 outline-none transition duration-300 placeholder:text-slate-400 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                >
                            </label>

                            <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                                <label for="remember_me" class="inline-flex items-center gap-3 text-slate-600">
                                    <x-checkbox id="remember_me" name="remember" />
                                    <span>Mantener sesion</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="font-bold text-indigo-600 transition hover:text-indigo-800" href="{{ route('password.request') }}">
                                        Recuperar clave
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="login-shine relative flex h-14 w-full items-center justify-center overflow-hidden rounded-2xl bg-slate-950 px-5 text-sm font-black uppercase text-white shadow-2xl shadow-slate-950/20 transition duration-300 hover:-translate-y-0.5 hover:shadow-indigo-500/30">
                                <span class="relative">Ingresar al sistema</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="login-app" data-props="{{ e(json_encode($loginProps)) }}" class="hidden"></div>
</x-guest-layout>
