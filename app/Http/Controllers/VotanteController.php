<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVotanteRequest;
use App\Http\Requests\UpdateVotanteRequest;
use App\Models\Votante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VotanteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Votante::class, 'votante');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('search'));

        $votantes = Votante::query()
            ->with('user')
            ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('numero_identificacion', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%")
                        ->orWhere('departamento', 'like', "%{$search}%")
                        ->orWhere('municipio', 'like', "%{$search}%")
                        ->orWhere('puesto_votacion', 'like', "%{$search}%")
                        ->orWhere('comuna', 'like', "%{$search}%")
                        ->orWhere('direccion', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('votantes.index', [
            'votantes' => $votantes,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('votantes.create');
    }

    public function checkNumeroIdentificacion(Request $request): JsonResponse
    {
        $numero = trim((string) $request->string('numero'));
        $ignore = $request->integer('ignore');

        $query = Votante::query()->where('numero_identificacion', $numero);

        if ($ignore > 0) {
            $query->whereKeyNot($ignore);
        }

        $votante = $query->with('user')->first();

        return response()->json([
            'exists' => (bool) $votante,
            'message' => $votante
                ? 'Este número de identificación ya está registrado en la plataforma.'
                : 'Número disponible.',
            'owner' => $votante?->user?->name,
        ]);
    }

    public function store(StoreVotanteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_certificado')) {
            $data['foto_certificado'] = $request->file('foto_certificado')->store('votantes/certificados', 'public');
        }

        $request->user()->votantes()->create($data);

        return redirect()
            ->route('votantes.index')
            ->with('flash.banner', 'Votante registrado correctamente.');
    }

    public function show(Votante $votante): View
    {
        $votante->loadMissing('user');

        return view('votantes.show', compact('votante'));
    }

    public function edit(Votante $votante): View
    {
        $votante->loadMissing('user');

        return view('votantes.edit', compact('votante'));
    }

    public function update(UpdateVotanteRequest $request, Votante $votante): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_certificado')) {
            if ($votante->foto_certificado) {
                Storage::disk('public')->delete($votante->foto_certificado);
            }

            $data['foto_certificado'] = $request->file('foto_certificado')->store('votantes/certificados', 'public');
        }

        $votante->update($data);

        return redirect()
            ->route('votantes.index')
            ->with('flash.banner', 'Votante actualizado correctamente.');
    }

    public function destroy(Votante $votante): RedirectResponse
    {
        if ($votante->foto_certificado) {
            Storage::disk('public')->delete($votante->foto_certificado);
        }

        $votante->delete();

        return redirect()
            ->route('votantes.index')
            ->with('flash.banner', 'Votante eliminado correctamente.');
    }
}
