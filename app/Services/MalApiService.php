<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Exception;
use STS\JWT\Facades\JWT;
use Illuminate\Support\Facades\Http;

/**
 * Class MalAuthenticationService.
 */
class MalApiService
{
    /**
     * @param string $chatId
     * @return string
     */
    public function getMalLoginUrl(string $chatId): string
    {
        $codeChallenge = hash('sha3-256', $chatId);
        $stateToken = JWT::get($chatId, [
            'chat_id'        => $chatId,
            'code_challenge' => $codeChallenge,
            'date'           => Carbon::now(),
        ]);
        $url = 'https://myanimelist.net/v1/oauth2/authorize';
        $url .= '?response_type=code';
        $url .= '&client_id=' . env('MAL_CLIENT_ID');
        $url .= '&state=' . $stateToken;
        // TODO: $url .= '&redirect_uri=MyRedirectUri';
        $url .= '&code_challenge=' . $codeChallenge;

        return $url;
    }


    /**
     * @param string $season
     * @param int $year
     * @return array
     */
    public function getSeasonalAnime(string $season, int $year): array
    {
        $response = Http::withHeader('X-MAL-CLIENT-ID', env('MAL_CLIENT_ID'))
            ->withQueryParameters([
                'fields' => 'mean,rank,popularity,num_list_users,num_scoring_users'
            ])
            ->get('https://api.myanimelist.net/v2/anime/season/' . $year . '/' . $season);

        return $response->json();
    }

    /**
     * @param User $user
     * @param string $status
     * @return array|mixed
     */
    public function getMyAnimeList(User $user, string $status = 'watching'): mixed
    {
        try {
            $reponse = Http::withHeader('Authorization', "Bearer $user->access_token")
                ->withQueryParameters([
                    'status' => $status,
                    'fields' => 'list_status',
                    'limit'  => 100
                ])
                ->get('https://api.myanimelist.net/v2/users/@me/animelist');

            if ($reponse->status() !== 200) {
                throw new Exception($reponse->json()['error'], $reponse->status());
            }

            return $reponse->json();
        } catch (Exception $exception) {
            dump($exception);
            return false;
        }
    }

    public function getAnime(int $id, User $user)
    {
        $resonse = Http::withHeader("Authorization", "Bearer $user->access_token")
            ->withQueryParameters([
                'fields' => "id,title,main_picture,alternative_titles,start_date,end_date,synopsis,mean,rank,popularity,
                    num_list_users,num_scoring_users,nsfw,created_at,updated_at,media_type,status,genres,my_list_status,
                    num_episodes,start_season,broadcast,source,average_episode_duration,rating,pictures,background,
                    related_anime,related_manga,recommendations,studios,statistics"
            ])
            ->get("https://api.myanimelist.net/v2/anime/$id"
            );

        return $resonse->json();
    }
}
