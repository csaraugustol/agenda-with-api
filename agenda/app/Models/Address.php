<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;

    /**
     * Nome da tabela
     *
     * @var string
     */
    protected $table = "adresses";

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'street_name',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'postal_code',
        'country',
        'contact_id',
    ];

    /**
     * Relacionamento endereço com contato
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
