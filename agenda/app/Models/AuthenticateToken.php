<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthenticateToken extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'expires_at',
        'user_id',
    ];

    /**
     * Relacionamento AuthenticateToken com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
