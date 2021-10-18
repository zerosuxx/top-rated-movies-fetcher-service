<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property string $title
 * @property int $length
 * @property string $release_date
 * @property string $overview
 * @property string $poster_url
 * @property integer $tmdb_id
 * @property float $tmdb_vote_average
 * @property integer $tmdb_vote_count
 * @property string $tmdb_url
 * @property Director[] $directors
 * @property Genre[] $genres
 */
class Movie extends Model
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
    protected $fillable = [
        'title',
        'length',
        'release_date',
        'overview',
        'poster_url',
        'tmdb_id',
        'tmdb_vote_average',
        'tmdb_vote_count',
        'tmdb_url',
    ];

    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'movies_directors');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movies_genres');
    }
}
