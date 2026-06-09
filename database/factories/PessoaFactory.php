<?php

namespace Database\Factories;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pessoa>
 */
class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'matricula' => fake()->unique()->numerify('2026######'),
            'telefone' => fake()->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }
}
