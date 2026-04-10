<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Store a new person
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:people,name',
                'type' => 'required|in:professor,mentor',
            ]);

            $person = Person::create($validated);

            return response()->json([
                'success' => true,
                'person' => $person,
                'message' => 'Pessoa adicionada com sucesso!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()[array_key_first($e->errors())][0] ?? 'Erro de validação',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Erro ao adicionar pessoa',
            ], 500);
        }
    }

    /**
     * Delete a person
     */
    public function destroy(Person $person)
    {
        $person->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pessoas removida com sucesso!',
        ]);
    }

    /**
     * Get all people grouped by type
     */
    public function all()
    {
        $professors = Person::where('type', 'professor')->get();
        $mentors = Person::where('type', 'mentor')->get();

        return response()->json([
            'professors' => $professors,
            'mentors' => $mentors,
        ]);
    }
}
