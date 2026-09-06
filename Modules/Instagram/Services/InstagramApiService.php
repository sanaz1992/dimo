<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\Http;
use Modules\Instagram\Entities\InstagramAccount;

class InstagramApiService
{
    protected string $baseUrl = 'https://graph.instagram.com/v26.0';

    public function getPosts(
        InstagramAccount $instagramAccount,
        int $limit = 50,
        ?string $after = null
    ): array {
        $query = [
            'fields' => implode(',', [
                'id',
                'media_type',
                'media_product_type',
                'caption',
                'permalink',
                'timestamp',
            ]),
            'limit' => $limit,
        ];

        if ($after) {
            $query['after'] = $after;
        }

        $response = Http::withToken($instagramAccount->access_token)
            ->get($this->baseUrl.'/'.$instagramAccount->instagram_user_id.'/media', $query);

        $response->throw();

        return $response->json();
    }
}
