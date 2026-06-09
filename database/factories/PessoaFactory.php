<?php

namespace Database\Factories;

use App\Models\Biblioteca;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pessoa>
 */
class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    public function definition(): array
    {
        return [
            'biblioteca_id' => function () {
                $user = User::factory()->create();

                return Biblioteca::create([
                    'created_by' => $user->id,
                    'nome' => fake()->company(),
                ])->id;
            },
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'matricula' => (string) fake()->numberBetween(100000, 999999),
            'telefone' => fake()->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }
}
