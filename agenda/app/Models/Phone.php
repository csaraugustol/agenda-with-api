<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'phone_number',
        'contact_id',
    ];

    /**
     * Relacionamento telefone com contato
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
