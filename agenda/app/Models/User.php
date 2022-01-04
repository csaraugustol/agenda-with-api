<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Oculta os atributos
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relacionamento do usuário com contatos
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Relacionamento do usuário com tag
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Relacionamento do usuário com AuthenticateToken
     */
    public function authenticateTokens()
    {
        return $this->hasMany(AuthenticateToken::class);
    }

    /**
     * Relacionamento do usuário com ChangePassword
     */
    public function changePasswords()
    {
        return $this->hasMany(ChangePassword::class);
    }
}
