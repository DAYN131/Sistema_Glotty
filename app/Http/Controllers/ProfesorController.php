<?php
namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfesorController extends Controller
{
    public function index()
    {
        $profesores = Profesor::all();
        return view('coordinador.profesores', compact('profesores'));
    }

    public function create()
    {
        return view('coordinador.registrar-profesor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rfc_profesor' => 'required|unique:profesores|max:255',
            'nombre_profesor' => 'required|max:255',
            'apellidos_profesor' => 'required|max:255',
            'correo_profesor' => 'required|email|unique:profesores',
            'num_telefono' => 'required|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            Profesor::create([
                'rfc_profesor' => $request->rfc_profesor,
                'nombre_profesor' => $request->nombre_profesor,
                'apellidos_profesor' => $request->apellidos_profesor,
                'correo_profesor' => $request->correo_profesor,
                'num_telefono' => $request->num_telefono,
                'contraseña' => Hash::make($request->password),
            ]);

            return redirect()->route('coordinador.profesores.index')
                ->with('success', 'Profesor registrado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar profesor: ' . $e->getMessage()
            ]);
        }
    }

    // ✅ MÉTODO EDIT FALTANTE
    public function edit($id)
    {
        $profesor = Profesor::findOrFail($id);
        return view('coordinador.profesores.edit', compact('profesor'));
    }

    // ✅ MÉTODO UPDATE FALTANTE
    public function update(Request $request, $id)
    {
        $profesor = Profesor::findOrFail($id);

        $request->validate([
            'rfc_profesor' => 'required|max:255|unique:profesores,rfc_profesor,' . $id . ',id_profesor',
            'nombre_profesor' => 'required|max:255',
            'apellidos_profesor' => 'required|max:255',
            'correo_profesor' => 'required|email|unique:profesores,correo_profesor,' . $id . ',id_profesor',
            'num_telefono' => 'required|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        try {
            $data = [
                'rfc_profesor' => $request->rfc_profesor,
                'nombre_profesor' => $request->nombre_profesor,
                'apellidos_profesor' => $request->apellidos_profesor,
                'correo_profesor' => $request->correo_profesor,
                'num_telefono' => $request->num_telefono,
            ];

            // Solo actualizar contraseña si se proporcionó
            if ($request->filled('password')) {
                $data['contraseña'] = Hash::make($request->password);
            }

            $profesor->update($data);

            return redirect()->route('coordinador.profesores.index')
                ->with('success', 'Profesor actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar profesor: ' . $e->getMessage()
            ]);
        }
    }

    // ✅ MÉTODO DESTROY FALTANTE
    public function destroy($id)
    {
        try {
            $profesor = Profesor::findOrFail($id);
            
            // Verificar si el profesor tiene grupos asignados
            if ($profesor->grupos()->exists()) {
                return redirect()->route('coordinador.profesores.index')
                    ->withErrors(['error' => 'No se puede eliminar el profesor porque tiene grupos asignados.']);
            }

            $profesor->delete();

            return redirect()->route('coordinador.profesores.index')
                ->with('success', 'Profesor eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('coordinador.profesores.index')
                ->withErrors(['error' => 'Error al eliminar profesor: ' . $e->getMessage()]);
        }
    }
}