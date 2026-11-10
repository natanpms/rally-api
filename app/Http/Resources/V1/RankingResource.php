<?php

namespace App\Http\Resources\V1;

use App\Models\Ranking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ranking
 */
class RankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'points' => $this->points,
            'date' => $this->ranked_at->toDateString(),

            'player' => [
                'id' => $this->player_id,
                'name' => $this->player_name,
                'country' => [
                    'name' => $this->country_name,
                    'code' => $this->country_code,
                ],
                'image_url' => $this->photo_url,
            ],
        ];
    }
}
