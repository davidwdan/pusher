<?php

use React\EventLoop\Factory;
use Rx\Observable;
use Rx\Observer\CallbackObserver;
use ApiClients\Pusher\AsyncClient;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$loop = Factory::create();

/**
 * The App ID isn't a secret and comes from a Pusher blog post:
 * @link https://blog.pusher.com/pusher-realtime-reddit-api/
 */
$client = new AsyncClient($loop, require 'reddit.key.php');

$subReddits = $argv;

Observable::fromArray($subReddits)
    ->skip(1)
    ->flatMap(function ($subReddit) use ($client) {
        return $client->channel($subReddit);
    })
    ->subscribe(new CallbackObserver(function ($event) {
        echo 'Channel: ', $event->channel, PHP_EOL;
        echo 'Event: ', $event->event, PHP_EOL;
        echo 'Data: ', $event->data, PHP_EOL;
    }));

$loop->run();
