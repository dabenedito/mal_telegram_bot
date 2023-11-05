<?php

namespace App\Commands;


use App\Models\User;
use App\Services\MalApiService;
use Telegram\Bot\Commands\Command;

class AnimeListCommand extends Command
{
    private MalApiService $malApiService;

    public function __construct()
    {
        $this->malApiService = new MalApiService();
    }

    protected string $name = 'list';
    protected string $pattern = '{status}';
    protected string $description = 'Get yor anime list by status. Defult is "watching"';

    public function handle()
    {
        $status = $this->argument('status', 'watching');
        $chatId = intval($this->getUpdate()->getChat()->id);
        $user = User::where('chat_id', $chatId)->first();

        $list = $this->malApiService->getMyAnimeList($user, $status);
        $formatedList = '';

        if ($list) {
            foreach ($list['data'] as $anime) {
                $formatedList .= '`[' . $anime['node']['id'] . '] ' . $anime['node']['title'] . '`' . "\n";
            }

            $this->replyWithMessage([
                'text'       => "Here yours anime $status list:\n\n" . $formatedList,
                "parse_mode" => 'MarkdownV2',
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Something went wrong...'
            ]);
        }
    }
}
