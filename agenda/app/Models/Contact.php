<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Contact extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;
    use SoftCascadeTrait;

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
     * Relacionamentos das models que serão deletadas junto com o contato
     *
     * @var array
     */
    protected $softCascade = [
        'adresses',
        'phones',
        'tagContacts',
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
     * Relacionamento contato com TagContact
     */
    public function tagContacts()
    {
        return $this->hasMany(TagContact::class);
    }

    /**
     * Relacionamento contato com a Tag
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tags_contacts', 'contact_id', 'tag_id');
    }
}
