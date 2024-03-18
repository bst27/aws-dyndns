<?php

require_once __DIR__.'/../vendor/autoload.php';

use AwsDynDns\AuthGuard;
use AwsDynDns\Updater;
use AwsDynDns\UpdateRequest;

try {
    (new AuthGuard())->check();
} catch (RuntimeException $err) {
    http_response_code(403);
    echo 'Forbidden';
    exit();
}

try {
    $request = UpdateRequest::initOrFail();
} catch (InvalidArgumentException $err) {
    http_response_code(400);
    echo 'Bad Request: '.$err->getMessage();
    exit();
}

$updater = new Updater();
try {
    $updater->update($request);
} catch (RuntimeException $err) {
    http_response_code(502);
    echo 'Bad Gateway';
    exit();
}

http_response_code(200);
echo 'OK';
