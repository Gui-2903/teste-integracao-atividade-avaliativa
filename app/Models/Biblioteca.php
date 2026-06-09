<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biblioteca extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'nome', 'endereco', 'telefone', 'email'];

    /**
     * Relacionamento com o criador (User).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento many-to-many com Users (admins).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'biblioteca_user')->withPivot('role');
    }

    /**
     * Relacionamento com Pessoas (pivot biblioteca_pessoa).
     */
    public function pessoas()
    {
        return $this->belongsToMany(Pessoa::class, 'biblioteca_pessoa')->withTimestamps();
    }
}
