<?php

namespace App\Models;

use Database\Factories\RankingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    /** @use HasFactory<RankingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tour',
        'ranked_at',
        'position',
        'player_id',
        'player_name',
        'country_name',
        'country_code',
        'points',
        'photo_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ranked_at' => 'date',
        ];
    }
}
