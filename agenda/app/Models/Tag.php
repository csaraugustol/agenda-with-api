<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Tag extends Model
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
        'description',
        'user_id',
    ];

    /**
     * Deleta o relacionamento com o contato quando excluir a tag
     *
     * @var array
     */
    protected $softCascade = [
        'tagContacts',
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
    public function tagContacts()
    {
        return $this->hasMany(TagContact::class);
    }
}
