<?php

namespace App\Commands;

use App\Models\User;
use App\Services\MalApiService;
use Carbon\Carbon;
use Mockery\Exception;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    private $malService;

    public function __construct()
    {
        $this->malService = new MalApiService();
    }

    protected string $name = "start";
    protected array $aliases = ["login"];
    protected string $description = "Start Command to get you started";

    public function handle()
    {
        try {
            $firstName = $this->getUpdate()->getMessage()->from->first_name ?? "there";
            $chatId = intval($this->getUpdate()->getChat()->id);

            $user = User::where('chat_id', $chatId)->first();

            $this->replyWithMessage([
                "text" => "Hey, $firstName! Welcome to our bot!\n" . Carbon::now(),
            ]);

            if (!$user) {
                $this->replyWithMessage([
                    "text" => 'Looks like you\'re new here, but don\'t worry, I\'ll remember you later.',
                ]);

                $user = new User();

                $user->chat_id = $chatId;
                $user->first_name = $firstName;
                $user->last_name = $this->getUpdate()->getMessage()->from->last_name;
                $user->username = $this->getUpdate()->getMessage()->from->username;
                $user->save();
            }

            $this->replyWithMessage([
                "text" => "I'll check if you have loging informations, just a second.",
            ]);

            $url = $this->malService->getMalLoginUrl($chatId);

            $this->replyWithMessage([
                "text" => "It seems that you don't made a login yet\.\n" .
                    "Please, click at *[this link]($url)* to get to MAL oficial login page\.",
                "parse_mode" => 'MarkdownV2'
            ]);

        } catch (Exception $exception) {
            $this->replyWithMessage($exception->getMessage());
        }
    }
}
