<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nombre
 * @property string $alcance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ControlEntrega> $entregas
 * @property-read int|null $entregas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereAlcance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoDocumento whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoDocumento extends Model
{
    protected $fillable = ['nombre', 'alcance'];

    // Relación inversa: Un tipo de documento tiene muchas entregas asociadas
    public function entregas()
    {
        return $this->hasMany(ControlEntrega::class);
    }
}
