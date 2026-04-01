<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('profile.index');
    }

    public function store(Request $request)
    {
        // Slug del username
        $request->request->add([
            'username' => Str::slug($request->username)
        ]);

        // Validación
        $this->validate($request, [
            'username' => [
                'required',
                'unique:users,username,' . auth()->user()->id,
                'min:3',
                'max:20',
                'not_in:twitter,editar-perfil'
            ],
            'imagen' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('imagen')) {

            $imagen = $request->file('imagen');
            $nombreImagen = Str::uuid() . "." . $imagen->extension();

            // ✅ v3 correcto
            $manager = new ImageManager(new Driver());
            $imagenServidor = $manager->read($imagen);

            // tipo avatar (cuadrado)
            $imagenServidor->cover(300, 300);

            // guardar
            $imagenPath = public_path('perfiles') . '/' . $nombreImagen;
            $imagenServidor->save($imagenPath);
        }

        // Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario->save();

        return redirect()->route('posts.index', $usuario->username);
    }
}
