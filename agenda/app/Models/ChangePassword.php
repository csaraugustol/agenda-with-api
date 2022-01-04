<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangePassword extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;

    /**
     * Nome da tabela
     *
     * @var string
     */
    protected $table = "change_passwords";

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    /**
     * Relacionamento ChangePassword com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
