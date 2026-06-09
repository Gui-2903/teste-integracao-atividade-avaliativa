<?php

namespace Database\Seeders;

use App\Models\Biblioteca;
use App\Models\Pessoa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PessoaBibliotecaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with pessoas and attach them to bibliotecas.
     */
    public function run(): void
    {
        $bibliotecas = Biblioteca::all();

        if ($bibliotecas->isEmpty()) {
            $this->command->warn('Nenhuma biblioteca encontrada. Crie bibliotecas antes de rodar este seeder.');
            return;
        }

        $pessoas = Pessoa::factory(10)->create();

        foreach ($pessoas as $pessoa) {
            $bibliotecaCount = min($bibliotecas->count(), rand(1, 2));
            $bibliotecaIds = $bibliotecas->random($bibliotecaCount)->pluck('id')->toArray();
            $pessoa->bibliotecas()->attach($bibliotecaIds);
        }

        $this->command->info('10 pessoas criadas e associadas a bibliotecas dinamicamente.');
    }
}
