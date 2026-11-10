<?php

namespace App\Console\Commands;

use App\Models\Ranking;
use App\Services\TennisApiService;
use App\Services\WikipediaPhotoService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Signature('tennis:sync-rankings {--tour=* : Circuitos a sincronizar (padrão: atp e wta)}')]
#[Description('Grava os rankings de simples da Tennis API no banco, com a foto de cada jogador')]
class SyncRankings extends Command
{
    private const MAX_PAGES = 6;

    public function handle(TennisApiService $tennisApiService, WikipediaPhotoService $wikipediaPhotoService): int
    {
        $tours = $this->option('tour') ?: ['atp', 'wta'];

        foreach ($tours as $tour) {
            $players = [];

            for ($pageNo = 1; $pageNo <= self::MAX_PAGES; $pageNo++) {
                $ranking = $tennisApiService->getRanking($tour, $pageNo, TennisApiService::MAX_PAGE_SIZE);
                $players = array_merge($players, $ranking['data']);

                if (! $ranking['hasNextPage']) {
                    break;
                }
            }

            $photos = $wikipediaPhotoService->getPhotoUrls(array_filter(array_map(
                fn(array $item): ?string => data_get($item, 'player.name'),
                $players,
            )));

            $rows = array_map(fn(array $item): array => $this->toRow($tour, $item, $photos), $players);

            $this->info(sprintf(
                '%s: %d posições, %d com foto da Wikipédia.',
                mb_strtoupper($tour),
                count($rows),
                count(array_filter(array_column($rows, 'photo_url'))),
            ));

            //  para a leitura nunca cair numa semana pela metade:
            // o service sempre lê a maior `ranked_at` gravada.
            DB::transaction(function () use ($rows): void {
                foreach (array_chunk($rows, 200) as $chunk) {
                    Ranking::upsert(
                        $chunk,
                        ['tour', 'ranked_at', 'position'],
                        ['player_id', 'player_name', 'country_name', 'country_code', 'points', 'photo_url', 'updated_at'],
                    );
                }
            });
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, string>  $photos
     * @return array<string, mixed>
     */
    private function toRow(string $tour, array $item, array $photos): array
    {
        $name = (string) data_get($item, 'player.name');

        return [
            'tour' => $tour,
            'ranked_at' => Carbon::parse($item['date'])->toDateString(),
            'position' => $item['position'],
            'player_id' => data_get($item, 'player.id'),
            'player_name' => $name,
            'country_name' => data_get($item, 'player.country.name'),
            'country_code' => data_get($item, 'player.country.acronym'),
            'points' => $item['rankingPoints'] ?? 0,
            'photo_url' => $photos[$name] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
