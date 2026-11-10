<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WikipediaPhotoService
{
    /**
     * Limite de títulos por requisição imposto pela API da Wikipédia.
     */
    private const NAMES_PER_REQUEST = 50;

    /**
     * Busca na Wikipédia a foto de cada jogador, em lotes de 50 nomes.
     *
     * @param  array<int, string>  $names
     * @return array<string, string> nome consultado => URL da foto
     */
    public function getPhotoUrls(array $names): array
    {
        $photos = [];

        foreach (array_chunk(array_values(array_unique($names)), self::NAMES_PER_REQUEST) as $chunk) {
            $photos += $this->fetchPhotoUrls($chunk);
        }

        return $photos;
    }

    /**
     * @param  array<int, string>  $names
     * @return array<string, string>
     */
    private function fetchPhotoUrls(array $names): array
    {
        $response = Http::withHeaders([
            'User-Agent' => config('services.wikipedia.user_agent'),
        ])->get('https://en.wikipedia.org/w/api.php', [
            'action' => 'query',
            'prop' => 'pageimages',
            'titles' => implode('|', $names),
            'pithumbsize' => 200,
            'redirects' => 1,
            'format' => 'json',
            'formatversion' => 2,
        ]);

        if ($response->failed()) {
            return [];
        }

        $photos = [];

        foreach ($response->json('query.pages', []) as $page) {
            if ($url = data_get($page, 'thumbnail.source')) {
                // A URL vem com parâmetros de analytics da Wikimedia grudados,
                // que só aumentam o tamanho do campo.
                $photos[$page['title']] = explode('?', $url)[0];
            }
        }

        foreach ($response->json('query.redirects', []) as $redirect) {
            if (isset($photos[$redirect['to']])) {
                $photos[$redirect['from']] = $photos[$redirect['to']];
            }
        }

        return $photos;
    }
}
