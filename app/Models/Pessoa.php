<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pessoa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['biblioteca_id', 'name', 'email', 'password', 'matricula', 'telefone'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Relacionamento com Biblioteca.
     */
    public function biblioteca()
    {
        return $this->belongsTo(Biblioteca::class);
    }

    /**
     * Relacionamento many-to-many com Bibliotecas.
     */
    public function bibliotecas()
    {
        return $this->belongsToMany(Biblioteca::class, 'biblioteca_pessoa');
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
