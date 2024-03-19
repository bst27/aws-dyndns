<?php

namespace AwsDynDns;

class HealthCheck
{
    public function done(): bool
    {
        return $_SERVER['REQUEST_URI'] === '/health';
    }
}
