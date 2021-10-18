<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property integer $tmdb_id
 * @property string $name
 * @property Movie[] $movies
 */
class Genre extends Model
{
    public $timestamps = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['tmdb_id', 'name'];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movies_genres');
    }
}
