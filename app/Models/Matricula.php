<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $numero
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matricula whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Matricula extends Model
{
    use Auditable;
    protected $fillable = [
        'nombre',
        'numero'
    ];
}
