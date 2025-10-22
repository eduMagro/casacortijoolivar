<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galeria;
use App\Models\GaleriaImagen;
use Illuminate\Support\Facades\DB;

class GaleriaController extends Controller
{


    public function index(Request $request)
    {
        $query = Galeria::query()->with('imagenes')->orderBy('orden')->orderBy('id');

        // Invitados: solo visibles; autenticados: todas
        if (!$request->user()) {
            $query->where('visible', 1);
        }

        $galerias = $query->get();

        return view('instalaciones.index', compact('galerias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'imagenes'    => ['nullable', 'array'],
            'imagenes.*'  => ['image', 'mimes:jpg,jpeg,png,webp', 'max:12288'], // 12MB
        ]);

        DB::transaction(function () use ($data) {
            $galeria = Galeria::create([
                'titulo'      => $data['titulo'],
                'descripcion' => $data['descripcion'] ?? null,
                'orden'       => Galeria::max('orden') + 1,
                'visible'     => true,
            ]);

            if (!empty($data['imagenes'])) {
                foreach ($data['imagenes'] as $i => $imagen) {
                    $path = $imagen->store("galerias/{$galeria->id}", 'public');

                    GaleriaImagen::create([
                        'galeria_id'  => $galeria->id,
                        'ruta_imagen' => $path,
                        'orden'       => $i,
                    ]);
                }
            }
        });

        return redirect()->route('instalaciones.index')->with('status', 'Nueva galería creada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    // PATCH /galerias/{galeria}
    public function update(Request $request, Galeria $galeria)
    {
        $data = $request->validate([
            'titulo'      => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            // Si quieres permitir editar visibilidad/orden desde el mismo form:
            // 'visible'  => ['nullable','boolean'],
            // 'orden'    => ['nullable','integer','min:0'],
        ]);

        $galeria->update($data);

        return back()->with('status', 'Sección actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
