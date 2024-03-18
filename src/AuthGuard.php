<?php

namespace AwsDynDns;

use RuntimeException;

class AuthGuard
{
    /**
     * @throws RuntimeException
     */
    public function check(): void
    {
        $authToken = getenv('AUTH_TOKEN');

        if (! $authToken) {
            return;
        }

        if (($_GET['authToken'] ?? '') === $authToken) {
            return;
        }

        throw new RuntimeException('Access denied');
    }
}
