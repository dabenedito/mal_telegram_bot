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

    public function handle(): void
    {
        $now = Carbon::now();
        $fallbackSeason = match ($now->month) {
            1, 2, 3 => 'winter',
            4, 5, 6 => 'spring',
            7, 8, 9 => 'summer',
            default => 'fall',
        };

        $year = intval($this->argument('year', $now->year));
        $season = $this->argument('season', $fallbackSeason);

        $sesonalAnimes = $this->malServce->getSeasonalAnime($season, $year);
        $animesAcc = "";
        dump($sesonalAnimes);
        foreach ($sesonalAnimes['data'] as $i => $anime) {
            $animeTitle = $anime['node']['title'] ?? '--';
            $animeMean = $anime['node']['mean'] ?? '--';
            $animeRank = $anime['node']['rank'] ?? '--';
            $animesAcc .= "$i. $animeTitle <b>[$animeMean]</b> <u>#$animeRank</u>\n";
        }

        $this->replyWithMessage([
            'text' => "###### <b>$season/$year</b> ######\n\n<code>n. Anime name [mean] #rank</code>\n\n$animesAcc",
            "parse_mode" => 'HTML'
        ]);
    }
}
