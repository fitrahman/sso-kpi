<?php

namespace App\Models;

use Laravel\Passport\Client as BaseClient;

class PassportClient extends BaseClient
{
    /**
     * Determine if the client should skip the authorization prompt.
     *
     * @return bool
     */
    public function skipsAuthorization()
    {
        return true;
    }
}
