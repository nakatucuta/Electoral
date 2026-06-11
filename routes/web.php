<?php

use App\Http\Controllers\VotanteController;
use App\Http\Controllers\CatalogoUbicacionController;
use App\Models\User;
use App\Models\Votante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

$buildPanelData = function (User $user): array {
    $query = Votante::query()->with('user');

    if (! $user->isAdmin()) {
        $query->where('user_id', $user->id);
    }

    $latestVotantes = (clone $query)->latest()->take(5)->get();
    $employeeNotifications = collect();
    $employeeStats = collect();
    $topDepartamentos = collect();
    $topMunicipios = collect();
    $dailyTrend = collect();
    $departamentoCount = 0;
    $municipioCount = 0;

    if ($user->isAdmin()) {
        $employeeStats = User::query()
            ->where('role', 'employee')
            ->withCount('votantes')
            ->orderBy('name')
            ->get();

        $employeeNotifications = $employeeStats->where('votantes_count', 0)->values();
    }

    $topDepartamentos = (clone $query)
        ->select('departamento', DB::raw('COUNT(*) as total'))
        ->groupBy('departamento')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->map(function ($item) {
            $item->label = $item->departamento ? trim((string) $item->departamento) : 'Sin dato';
            if ($item->label === '') {
                $item->label = 'Sin dato';
            }

            return $item;
        })
        ->values();

    $topMunicipios = (clone $query)
        ->select('municipio', DB::raw('COUNT(*) as total'))
        ->groupBy('municipio')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->map(function ($item) {
            $item->label = $item->municipio ? trim((string) $item->municipio) : 'Sin dato';
            if ($item->label === '') {
                $item->label = 'Sin dato';
            }

            return $item;
        })
        ->values();

    $dailyTrend = collect(range(6, 0))
        ->map(function ($daysAgo) use ($query) {
            $date = now()->subDays($daysAgo)->startOfDay();

            return [
                'label' => $date->format('d/m'),
                'total' => (clone $query)->whereDate('created_at', $date->toDateString())->count(),
            ];
        });

    $departamentoCount = (clone $query)
        ->pluck('departamento')
        ->filter()
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->unique()
        ->count();

    $municipioCount = (clone $query)
        ->pluck('municipio')
        ->filter()
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->unique()
        ->count();

    return [
        'totalVotantes' => (clone $query)->count(),
        'latestVotantes' => $latestVotantes,
        'myVotantes' => Votante::where('user_id', $user->id)->count(),
        'employeeStats' => $employeeStats,
        'employeeNotifications' => $employeeNotifications,
        'topDepartamentos' => $topDepartamentos,
        'topMunicipios' => $topMunicipios,
        'dailyTrend' => $dailyTrend,
        'departamentoCount' => $departamentoCount,
        'municipioCount' => $municipioCount,
    ];
};

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () use ($buildPanelData) {
    Route::get('/dashboard', function () use ($buildPanelData) {
        return view('dashboard', $buildPanelData(auth()->user()));
    })->name('dashboard');

    Route::get('/estadisticas', function () use ($buildPanelData) {
        $user = auth()->user();

        return view('estadisticas', $buildPanelData($user));
    })->name('estadisticas');

    Route::get('votantes/validar-numero-identificacion', [VotanteController::class, 'checkNumeroIdentificacion'])
        ->name('votantes.check-numero');

    Route::get('catalogos/ubicacion/buscar', [CatalogoUbicacionController::class, 'search'])
        ->name('catalogos.ubicacion.search');

    Route::resource('votantes', VotanteController::class);
});
