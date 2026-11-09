<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LapsoAcademico> $lapsoAcademicos
 * @property-read int|null $lapso_academicos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoLapso whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoLapso extends Model
{
    use HasFactory;
    use Auditable;

    public function lapsoAcademicos()
    {
        return $this->hasMany(LapsoAcademico::class);
    }
}
