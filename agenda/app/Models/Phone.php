<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model
{
    use SoftDeletes;

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'phone_number',
        'contact_id',
    ];
}
