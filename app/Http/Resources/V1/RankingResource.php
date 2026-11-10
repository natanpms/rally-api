<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $player = $this['player'] ?? [];
        $playerId = data_get($player, 'id');

        return [
            'id' => $this['id'],
            'position' => $this['position'],
            'points' => $this['rankingPoints'],
            'date' => $this['date'],

            'player' => [
                'id' => $playerId,
                'name' => data_get($player, 'name'),
                'country' => [
                    'name' => data_get($player, 'country.name'),
                    'code' => data_get($player, 'country.acronym'),
                ],
                'image_url' => $playerId
                    ? sprintf(
                        'https://%s/tennis/v2/ms-api/uploads/Photo/atp/%05d.jpg',
                        config('services.tennis.host'),
                        $playerId
                    )
                    : null,
            ],
        ];
    }
}
