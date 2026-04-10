<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create professors
        $professors = [
            'Mateus Amaro',
            'Djulian',
            'Graziella',
            'Leticia',
            'Marcos',
            'Vini',
            'Thiago',
            'Vitin',
            'Enzo',
            'Julio',
            'Lara',
            'Maria Fernanda',
            'Victor Henrique',
        ];

        foreach ($professors as $name) {
            Person::create([
                'id' => (string) Str::uuid(),
                'name' => $name,
                'type' => 'professor',
            ]);
        }

        // Create mentors
        $mentors = [
            'André',
            'Davi',
            'Bea',
            'João',
            'Yuuki',
            'Manuela',
            'Fernanda',
        ];

        foreach ($mentors as $name) {
            Person::create([
                'id' => (string) Str::uuid(),
                'name' => $name,
                'type' => 'mentor',
            ]);
        }

        // Create classes
        $classes = [
            ['id' => 'adv1', 'name' => 'Turma Pleno 1', 'level' => 'advanced'],
            ['id' => 'adv2', 'name' => 'Turma Pleno 2', 'level' => 'advanced'],
            ['id' => 'ini1', 'name' => 'Turma Junior 1', 'level' => 'beginner'],
            ['id' => 'ini2', 'name' => 'Turma Junior 2', 'level' => 'beginner'],
        ];

        foreach ($classes as $class) {
            SchoolClass::create($class);
        }
    }
}
