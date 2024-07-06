<?php

namespace AwsDynDns;

use InvalidArgumentException;

class UpdateRequest
{
    /**
     * @throws InvalidArgumentException
     */
    public static function initOrFail(): self
    {
        $accessKey = self::getValueOrFail('awsAccessKey');
        $secretKey = self::getValueOrFail('awsSecretKey');
        $region = self::getValueOrFail('awsRegion');
        $hostedZoneId = self::getValueOrFail('awsHostedZoneId');
        $domainName = self::getValueOrFail('awsDomainName');

        $ipAddresses = [];
        try {
            $ipAddresses[] = IpAddress::ipv4(self::getValueOrFail('ip', false));
        } catch (InvalidArgumentException $ex) {
        }

        try {
            $ipAddresses[] = IpAddress::ipv6(self::getValueOrFail('ipv6', false));
        } catch (InvalidArgumentException $ex) {
        }

        if (empty($ipAddresses)) {
            throw new InvalidArgumentException('Missing required parameter: ip or ipv6');
        }

        $domainName = self::ensureEndsWithPeriod($domainName);

        return new self(
            $accessKey,
            $secretKey,
            $region,
            $hostedZoneId,
            $domainName,
            $ipAddresses,
        );
    }

    private static function getValueOrFail(string $key, bool $useEnvFallback = true): string
    {
        $value = $_GET[$key] ?? null;

        if (! $value && $useEnvFallback) {
            $value = getenv(self::camelCaseToSnakeCase($key));
        }

        if (! $value) {
            throw new InvalidArgumentException('Missing required parameter: '.$key);
        }

        return $value;
    }

    private static function camelCaseToSnakeCase(string $value): string
    {
        // Insert an underscore before each uppercase letter, then convert the entire string to uppercase
        $result = preg_replace('/(?<!^)[A-Z]/', '_$0', $value);

        return strtoupper($result);
    }

    private static function ensureEndsWithPeriod(string $value): string
    {
        if (! str_ends_with($value, '.')) {
            $value .= '.';
        }

        return $value;
    }

    private function __construct(
        private readonly string $accessKey,
        private readonly string $secretKey,
        private readonly string $region,
        private readonly string $hostedZoneId,
        private readonly string $domainName,
        private readonly array $newIpAddresses,
    ) {}

    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getHostedZoneId(): string
    {
        return $this->hostedZoneId;
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    /**
     * @return IpAddress[]
     */
    public function getNewIpAddresses(): array
    {
        return $this->newIpAddresses;
    }
}
