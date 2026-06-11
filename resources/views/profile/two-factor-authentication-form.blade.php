<x-action-section>
    <x-slot name="title">
        Autenticación de dos factores
    </x-slot>

    <x-slot name="description">
        Agrega una capa adicional de seguridad a tu cuenta usando autenticación de dos factores.
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    Termina de activar la autenticación de dos factores.
                @else
                    Has activado la autenticación de dos factores.
                @endif
            @else
                No has activado la autenticación de dos factores.
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-400">
            <p>
                Cuando la autenticación de dos factores está activada, se te pedirá un token seguro y aleatorio durante el inicio de sesión. Puedes obtener ese token desde la aplicación Google Authenticator de tu teléfono.
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            Para terminar de activar la autenticación de dos factores, escanea el siguiente código QR con la aplicación autenticadora de tu teléfono o ingresa la clave de configuración y proporciona el código OTP generado.
                        @else
                            La autenticación de dos factores ya está activada. Escanea el siguiente código QR con la aplicación autenticadora de tu teléfono o ingresa la clave de configuración.
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block bg-white">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold">
                        Clave de configuración: {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <x-label for="code" value="Código" />

                        <x-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />

                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold">
                        Guarda estos códigos de recuperación en un administrador de contraseñas seguro. Se pueden usar para recuperar el acceso a tu cuenta si pierdes el dispositivo de autenticación de dos factores.
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 dark:text-gray-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled">
                        Activar
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button class="me-3">
                            Regenerar códigos de recuperación
                        </x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" class="me-3" wire:loading.attr="disabled">
                            Confirmar
                        </x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button class="me-3">
                            Mostrar códigos de recuperación
                        </x-secondary-button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled">
                            Cancelar
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled">
                            Desactivar
                        </x-danger-button>
                    </x-confirms-password>
                @endif

            @endif
        </div>
    </x-slot>
</x-action-section>
