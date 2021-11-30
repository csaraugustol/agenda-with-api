<?php

namespace App\Models;

use App\Models\Traits\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagContact extends Model
{
    use SoftDeletes;
    use UuidAsPrimaryKey;

    /**
     * Nome da tabela
     *
     * @var string
     */
    protected $table = "tags_contacts";

    /**
     * Atributos da model para serem atribuídos
     *
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'contact_id',
    ];

    /**
     * Relacionamento da TagContact com tag
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Relacionamento da TagContact com contato
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
