<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TennisApiService
{
    /**
     * Maior `pageSize` aceito pela API.
     */
    public const MAX_PAGE_SIZE = 500;

    /**
     * Retorna uma página do ranking ATP de simples.
     *
     * @return array{data: array<int, array<string, mixed>>, pageNo: int, pageSize: int, hasNextPage: bool}
     */
    public function getAtpRanking(int $pageNo = 1, int $pageSize = 20): array
    {
        return $this->get('tennis/v2/atp/ranking/singles', [
            'pageNo' => $pageNo,
            'pageSize' => min($pageSize, self::MAX_PAGE_SIZE),
        ]);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function get(string $path, array $query = []): array
    {
        $host = config('services.tennis.host');

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => config('services.tennis.key'),
            'X-RapidAPI-Host' => $host,
        ])->get("https://{$host}/{$path}", $query);

        if ($response->failed()) {
            throw new \RuntimeException("Falha ao consultar a Tennis API: {$path} ({$response->status()}).");
        }

        return $response->json();
    }
}
