<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property integer $tmdb_id
 * @property string $name
 * @property string $biography
 * @property string|null $date_of_birth
 * @property Movie[] $movies
 */
class Director extends Model
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
    protected $fillable = ['tmdb_id', 'name', 'biography', 'date_of_birth'];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movies_directors');
    }
}
