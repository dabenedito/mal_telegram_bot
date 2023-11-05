<?php

namespace App\Http\Controllers;


use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use STS\JWT\Facades\JWT;
use Telegram\Bot\Objects\Message;
use Illuminate\Http\JsonResponse;
use App\Services\MalApiService;
use Telegram\Bot\Laravel\Facades\Telegram;

class SchedulerController extends Controller
{
    private MalApiService $malService;

    public function __construct()
    {
        $this->malService = new MalApiService();
    }

    /**
     * Handles the reponse bot messages.
     */
    public function botResponses(): void
    {
        Telegram::commandsHandler(true);
    }

    /**
     * @throws Exception
     */
    public function getOAuth2Token(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => ['required'],
                'state' => ['required'],
            ]);

            if (!$validated) {
                throw new Exception('Missing code/state values.', 400);
            }

            $parsedState = JWT::parse($request['state'])->getClaims();

            $user = User::where('chat_id', $parsedState['chat_id'])->first();

            $response = Http::asForm()
                ->post('https://myanimelist.net/v1/oauth2/token', [
                    'client_id' => env('MAL_CLIENT_ID'),
                    'client_secret' => env('MAL_CLIENT_SECRET'),
                    'grant_type'=>'authorization_code',
                    'code'=> $request['code'],
                    'code_verifier'=> $parsedState['code_challenge']
                ]);

            $user->access_token = $response->json()['access_token'];
            $user->refresh_token = $response->json()['refresh_token'];
            $user->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'request_raw' => $request->all(),
                    'user' => $user
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage(),
                'status_code' => $exception->getCode()
            ]);
        }
    }

    /**
     * Set the env url for webhook responses
     *
     * @return void
     */
    public function setWebhook(): void
    {
        Telegram::setWebhook(['url' => env('TELEGRAM_WEBHOOK_URL')]);
    }

    /**
     * @param string $chatId
     * @param string $message
     * @return Message
     */
    private function sendMessage(string $chatId, string $message): Message
    {
        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $message
        ]);
    }
}
