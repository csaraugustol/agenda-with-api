<?php

namespace App\Models;

use Exception;
use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalToken extends Model
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
        'system',
        'user_id',
    ];

    /**
     * Relacionamento ExternalToken com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
     * Setar o valor de type, protegendo contra valores não previstos
     */
    public function setTypeAttribute(string $value)
    {
        $value = strtoupper($value);
        if (!in_array($value, config('enum.system.type'))) {
            return new Exception(
                'O tipo não é válido!',
                21
            );
        }
        $this->attributes['type'] = $value;
    }
}
