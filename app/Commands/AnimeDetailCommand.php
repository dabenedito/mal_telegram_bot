<?php

namespace App\Commands;

use App\Models\User;
use App\Services\MalApiService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\FileUpload\InputFile;

class AnimeDetailCommand extends Command
{
    private MalApiService $malApiService;

    public function __construct()
    {
        $this->malApiService = new MalApiService();
    }

    protected string $name = 'anime';
    protected string $pattern = '{id}';
    protected string $description = 'Get details from a especified anime. The anime\'s detail must match with his ID.';

    public function handle()
    {
        $id = $this->argument('id');

        if (!$id) {
            $this->replyWithMessage([
                'text' => 'You need to especify an ID for me to search it...'
            ]);
        } else {
            $user = User::find($this->getUpdate()->getMessage()->from->id);

            $anime = $this->malApiService->getAnime($id, $user);
            $parsedString = str_replace('.', '\\.', $anime['synopsis']);

            $this->replyWithPhoto([
                'photo' => new InputFile($anime['main_picture']['medium']),
                'caption' => '*' . $anime['title'] . '*' . "\n\n" . $parsedString,
                "parse_mode" => 'MarkdownV2'
            ]);
        }
    }
}
