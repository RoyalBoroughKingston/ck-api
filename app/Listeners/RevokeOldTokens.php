<?php

namespace App\Listeners;

use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

class RevokeOldTokens
{
    /**
     * Handle the event.
     *
     * @param \Laravel\Passport\Events\AccessTokenCreated $event
     */
    public function handle(AccessTokenCreated $event)
    {
        Token::query()
            ->where('user_id', $event->userId)
            ->where('id', '!=', $event->tokenId)
            ->where('revoked', false)
            ->update(['revoked' => true]);
    }
}
