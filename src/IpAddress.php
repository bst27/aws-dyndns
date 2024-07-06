<?php

namespace AwsDynDns;

class IpAddress
{
    private const TYPE_4 = 'ipv4';

    private const TYPE_6 = 'ipv6';

    public static function ipv4(string $ipAddr): self
    {
        return new self(self::TYPE_4, $ipAddr);
    }

    public static function ipv6(string $ipAddr): self
    {
        return new self(self::TYPE_6, $ipAddr);
    }

    private function __construct(
        private readonly string $type,
        private readonly string $ipAddress,
    ) {}

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function isIpv4(): bool
    {
        return $this->type === self::TYPE_4;
    }

    public function isIpv6(): bool
    {
        return $this->type === self::TYPE_6;
    }
}
