<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'user_id',
    ];

    /**
     * Relacionamento da tag com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento da tag com TagContact
     */
    public function tagContact()
    {
        return $this->belongsTo(TagContact::class);
    }
}
