<x-guest-layout>
    @php
        $loginProps = [
            'appName' => config('app.name', 'Electoral'),
            'loginAction' => route('login'),
            'csrfToken' => csrf_token(),
            'emailValue' => old('email', ''),
            'status' => session('status', ''),
            'errors' => $errors->getMessages(),
            'canResetPassword' => Route::has('password.request'),
            'forgotPasswordUrl' => route('password.request'),
        ];
    @endphp

    <div class="min-h-screen bg-[#f4f7fb] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-7xl overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.12)]">
            <section class="hidden w-0 flex-1 bg-[radial-gradient(circle_at_top_left,_rgba(124,58,237,0.22),_transparent_32%),linear-gradient(135deg,_#1e1b4b_0%,_#312e81_38%,_#5b21b6_100%)] p-10 text-white xl:flex xl:flex-col xl:justify-between">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-3 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        Plataforma electoral segura
                    </div>

                    <h1 class="mt-8 text-5xl font-semibold leading-tight tracking-tight">
                        {{ config('app.name', 'Electoral') }}
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
                            <h2 class="text-xl font-semibold text-slate-900">{{ config('app.name', 'Electoral') }}</h2>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-8 shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
                        <div class="mb-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Bienvenido</p>
                            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Inicia sesión</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Accede a tu cuenta para continuar.
                            </p>
                        </div>

                        <x-validation-errors class="mb-4" />

                        @session('status')
                            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ $value }}
                            </div>
                        @endsession

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <x-label for="email" value="Correo electrónico" />
                                <x-input id="email" class="mt-1 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            </div>

                            <div>
                                <x-label for="password" value="Contraseña" />
                                <x-input id="password" class="mt-1 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="current-password" />
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <label for="remember_me" class="flex items-center gap-3 text-sm text-slate-600">
                                    <x-checkbox id="remember_me" name="remember" />
                                    <span>Recordarme</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>

                            <x-button class="flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                                Ingresar
                            </x-button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div id="login-app" data-props="{{ e(json_encode($loginProps)) }}" class="hidden"></div>
</x-guest-layout>
