<?php

namespace App\Commands;

use App\Services\MalApiService;
use Carbon\Carbon;
use Telegram\Bot\Commands\Command;

class SeasonalCommand extends Command
{
    private MalApiService $malServce;

    public function __construct()
    {
        $this->malServce = new MalApiService();
    }

    protected string $name = 'seasonal';
    protected string $pattern = '{season}{year}';
    protected string $description = 'Get a seasonal list of anime';

    public function handle()
    {
        $fallbackSeason = match (Carbon::now()->month) {
            1, 2, 3 => 'winter',
            4, 5, 6 => 'spring',
            7, 8, 9 => 'summer',
            default => 'fall',
        };

        $year = intval($this->argument('year', Carbon::now()->year));
        $season = $this->argument('season', $fallbackSeason);

        $sesonalAnimes = $this->malServce->getSeasonalAnime($season, $year);

        $this->replyWithMessage([
            'text' => 'Vinicinhos, mama minha pika',
        ]);
    }
}
