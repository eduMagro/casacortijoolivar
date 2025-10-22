<?php

namespace App\Http\Controllers;

use App\Models\Galeria;
use App\Models\GaleriaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GaleriaImagenController extends Controller
{
    // POST /galerias/{galeria}/imagenes
    public function store(Request $request, Galeria $galeria)
    {
        $validated = $request->validate([
            'imagenes'   => ['required', 'array', 'min:1'],
            'imagenes.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:12288'], // 12MB
        ]);

        DB::transaction(function () use ($validated, $galeria) {
            foreach ($validated['imagenes'] as $file) {
                $path = $file->store("galerias/{$galeria->id}", 'public');

                // Siguiente orden dentro de la galería
                $siguienteOrden = (int) GaleriaImagen::where('galeria_id', $galeria->id)->max('orden') + 1;

                GaleriaImagen::create([
                    'galeria_id' => $galeria->id,
                    'ruta_imagen' => $path, // relativo al disco 'public'
                    'orden'      => $siguienteOrden,
                ]);
            }
        });

        return back()->with('status', 'Imágenes subidas.');
    }

    // DELETE /galerias/imagenes/{imagen}
    public function destroy(GaleriaImagen $imagen)
    {
        // Borra el archivo físico (si existe) y luego el registro
        if ($imagen->ruta_imagen && Storage::disk('public')->exists($imagen->ruta_imagen)) {
            Storage::disk('public')->delete($imagen->ruta_imagen);
        }

        $imagen->delete();

        return back()->with('status', 'Imagen eliminada.');
    }
}
