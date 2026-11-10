<?php

use App\Console\Commands\SyncRankings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// O ranking novo sai na segunda-feira; roda na terça
Schedule::command(SyncRankings::class)
    ->weeklyOn(2, '06:00')
    ->timezone('America/Sao_Paulo');
