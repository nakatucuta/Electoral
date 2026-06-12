<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Gestión de usuarios</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Busca un usuario y cambia su rol entre empleado y administrador.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('flash.banner'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('flash.banner') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <form method="GET" action="{{ route('users.roles') }}" class="grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label for="q" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Buscar</label>
                        <input id="q" name="q" value="{{ $search }}" type="text" placeholder="Nombre, cédula, email o sede" class="block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="role" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Rol</label>
                        <select id="role" name="role" class="block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Todos</option>
                            <option value="employee" @selected($role === 'employee')>Empleado</option>
                            <option value="admin" @selected($role === 'admin')>Administrador</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex justify-end">
                        <x-button>
                            Buscar usuarios
                        </x-button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-slate-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-gray-100">Usuarios encontrados</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Solo los administradores pueden cambiar estos roles.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700">
                        <thead class="bg-slate-50 dark:bg-gray-900/60">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Cédula</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Correo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Sede</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Rol</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                            @forelse ($users as $user)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900 dark:text-gray-100">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-gray-400">ID {{ $user->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300">{{ $user->documento_identificacion ?? 'Sin dato' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300">{{ $user->sede ?? 'Sin sede' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300' : 'bg-slate-100 text-slate-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ $user->role === 'admin' ? 'Administrador' : 'Empleado' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('users.roles.update', $user) }}" class="inline-flex items-center gap-3">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="rounded-xl border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                <option value="employee" @selected($user->role === 'employee')>Empleado</option>
                                                <option value="admin" @selected($user->role === 'admin')>Administrador</option>
                                            </select>
                                            <x-button>
                                                Guardar
                                            </x-button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                        No hay usuarios que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-6 py-4 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
