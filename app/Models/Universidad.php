<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property string $nombre_univ
 * @property string $abreviado_univ
 * @property string $rif_univ
 * @property string $direccion
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereAbreviadoUniv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereNombreUniv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereRifUniv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Universidad whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Universidad extends Model
{
    use Auditable;

    protected $table = 'universidads';

    protected $fillable = [
        'nombre_univ',
        'abreviado_univ',
        'rif_univ',
        'direccion',
        'logo',
    ];
}
