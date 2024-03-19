<?php

namespace AwsDynDns;

use RuntimeException;

class RateLimiter
{
    /**
     * @throws RuntimeException
     */
    public function check(): void
    {
        $rateLimit = getenv('RATE_LIMIT');

        if (! $rateLimit) {
            return;
        }

        $this->checkRateLimit($rateLimit, $this->getClientIp());
    }

    private function getClientIp(): string
    {
        $headers = [
            // This is only safe if we can expect to run behind a trusted reverse proxy, otherwise it can be simply spoofed.
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                return $_SERVER[$header];
            }
        }

        throw new RuntimeException('Cannot determine client IP');
    }

    private function checkRateLimit(int $rateLimit, string $clientIp): void
    {
        $rateCount = $this->increaseRateCount($clientIp);

        if ($rateCount > $rateLimit) {
            throw new RuntimeException('Rate limit reached');
        }
    }

    private function increaseRateCount(string $clientIp): int
    {
        // Avoid storing the client IP in clear text.
        $clientIp = hash('sha256', $clientIp);

        // Every time window lasts an hour so that rate counts are reset every hour.
        $currentTimeWindow = date('YmdH');

        $storageDir = realpath('/tmp').'/';
        $prefix = 'aws-dyndns-';
        $storageFile = $storageDir.$prefix.$currentTimeWindow.'-'.$clientIp.'.txt';

        $this->cleanupOldFiles($storageDir, $prefix, $currentTimeWindow);

        $currentCount = 0;
        if (file_exists($storageFile)) {
            $result = file_get_contents($storageFile);

            if ($result === false) {
                throw new RuntimeException('Failed to get rate counter');
            }

            $currentCount = (int) $result;
        }
        $currentCount++;

        if (file_put_contents($storageFile, $currentCount) === false) {
            throw new RuntimeException('Failed to increase rate counter');
        }

        return $currentCount;
    }

    private function cleanupOldFiles(string $storageDir, string $prefix, string $currentTimeWindow): void
    {
        if ($handle = opendir($storageDir)) {
            while (false !== ($entry = readdir($handle))) {
                // Check if the entry starts with the prefix and is not for the current time window
                if (str_starts_with($entry, $prefix) && ! str_contains($entry, $currentTimeWindow)) {
                    unlink($storageDir.$entry);
                }
            }

            closedir($handle);
        }
    }
}
