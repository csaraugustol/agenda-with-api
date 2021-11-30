<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
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
        'user_id',
    ];

    /**
     * Relacionamento contato com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento contato com endereço
     */
    public function adresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Relacionamento contato com telefone
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Relacionamento da contato com TagContact
     */
    public function tagContacts()
    {
        return $this->hasMany(TagContact::class);
    }
}
