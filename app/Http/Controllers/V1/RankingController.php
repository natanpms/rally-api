<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RankingResource;
use App\Services\RankingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RankingController extends Controller
{
    /**
     * Teto de registros por resposta.
     */
    private const MAX_PAGE_SIZE = 500;

    public function __construct(private RankingService $rankingService) {}

    /**
     * Exibe a lista de rankings ATP.
     */
    public function showAtp(Request $request): AnonymousResourceCollection
    {
        return $this->showRanking('atp', $request);
    }

    /**
     * Exibe a lista de rankings WTA.
     */
    public function showWta(Request $request): AnonymousResourceCollection
    {
        return $this->showRanking('wta', $request);
    }

    /**
     * @param  'atp'|'wta'  $tour
     */
    private function showRanking(string $tour, Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'pageNo' => ['integer', 'min:1'],
            'pageSize' => ['integer', 'min:1', 'max:' . self::MAX_PAGE_SIZE],
        ]);

        $ranking = $this->rankingService->page(
            $tour,
            (int) ($validated['pageNo'] ?? 1),
            (int) ($validated['pageSize'] ?? 20),
        );

        return RankingResource::collection($ranking->items())->additional([
            'meta' => [
                'tour' => $tour,
                'page' => $ranking->currentPage(),
                'per_page' => $ranking->perPage(),
                'total' => $ranking->total(),
                'has_next_page' => $ranking->hasMorePages(),
            ],
        ]);
    }
}
