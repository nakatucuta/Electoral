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

    <div id="login-fallback" class="min-h-screen bg-slate-50 px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/50">
            <div class="mb-6 text-center">
                <x-authentication-card-logo />
                <h1 class="mt-6 text-3xl font-semibold text-slate-900">Inicia sesión</h1>
                <p class="mt-2 text-sm text-slate-500">Accede a tu cuenta para continuar.</p>
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
                    <x-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>

                <div>
                    <x-label for="password" value="Contraseña" />
                    <x-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
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

    <div id="login-app" data-props="{{ e(json_encode($loginProps)) }}" class="hidden"></div>
</x-guest-layout>
