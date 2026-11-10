<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RankingResource;
use App\Services\TennisApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RankingController extends Controller
{
    public function __construct(private TennisApiService $tennisApiService) {}

    /**
     * Exibe a lista de rankings ATP.
     */
    public function showAtp(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'pageNo' => ['integer', 'min:1'],
            'pageSize' => ['integer', 'min:1', 'max:' . TennisApiService::MAX_PAGE_SIZE],
        ]);

        $ranking = $this->tennisApiService->getAtpRanking(
            (int) ($validated['pageNo'] ?? 1),
            (int) ($validated['pageSize'] ?? 20),
        );

        return RankingResource::collection($ranking['data'])->additional([
            'meta' => [
                'page' => $ranking['pageNo'],
                'per_page' => $ranking['pageSize'],
                'has_next_page' => $ranking['hasNextPage'],
            ],
        ]);
    }
}
