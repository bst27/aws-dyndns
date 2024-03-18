<?php

namespace AwsDynDns;

use Aws\Route53\Route53Client;
use RuntimeException;

class Updater
{
    /**
     * @throws RuntimeException
     */
    public function update(UpdateRequest $request): void
    {
        // Initialize Route53 client
        $route53 = new Route53Client([
            'version' => 'latest',
            'region' => $request->getRegion(),
            'credentials' => [
                'key' => $request->getAccessKey(),
                'secret' => $request->getSecretKey(),
            ],
        ]);

        // Prepare the change batch for the DNS update
        $changeBatch = [
            'Changes' => [
                [
                    'Action' => 'UPSERT',
                    'ResourceRecordSet' => [
                        'Name' => $request->getDomainName(),
                        'Type' => 'A',
                        'TTL' => 300,
                        'ResourceRecords' => [['Value' => $request->getNewIpAddress()]],
                    ],
                ],
            ],
        ];

        // Attempt to update the DNS record
        $result = $route53->changeResourceRecordSets([
            'HostedZoneId' => $request->getHostedZoneId(),
            'ChangeBatch' => $changeBatch,
        ]);
    }
}
