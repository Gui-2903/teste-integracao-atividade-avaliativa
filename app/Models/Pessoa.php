<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pessoa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'matricula', 'telefone'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Relacionamento com Bibliotecas (pivot biblioteca_pessoa).
     */
    public function bibliotecas()
    {
        return $this->belongsToMany(Biblioteca::class, 'biblioteca_pessoa')->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
