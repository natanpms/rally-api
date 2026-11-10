<?php

namespace App\Services;

use App\Models\Ranking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RankingService
{
    /**
     * Página do ranking mais recente que o comando `tennis:sync-rankings`
     * gravou. A leitura é só do banco: a Tennis API é consultada uma vez por
     * semana pelo agendamento, não durante a requisição do cliente.
     *
     * @param  'atp'|'wta'  $tour
     * @return LengthAwarePaginator<int, Ranking>
     */
    public function page(string $tour, int $pageNo, int $pageSize): LengthAwarePaginator
    {
        $rankedAt = Ranking::query()->where('tour', $tour)->max('ranked_at');

        return Ranking::query()
            ->where('tour', $tour)
            ->where('ranked_at', $rankedAt)
            ->orderBy('position')
            ->paginate(perPage: $pageSize, pageName: 'pageNo', page: $pageNo);
    }
}
