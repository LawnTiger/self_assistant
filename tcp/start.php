<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;

// Create a Websocket server
$worker = new Worker("websocket://0.0.0.0:4000");
$worker->count = 2;

$channel = new Channel\Server('127.0.0.1', 2206);

$worker->onWorkerStart = function ($worker) {
    Channel\Client::connect('127.0.0.1', 2206);
    Channel\Client::on('broadcast', function($event_data) use($worker) {
        foreach ($worker->connections as $con) {
            $con->send($event_data);
        }
    });
};

$worker->onConnect = function ($connection) use ($worker) {
    echo "New connection\n";
    $connection->send("welcome to fu*king test room\n");
};

$worker->onMessage = function ($connection, $data) use ($worker) {
    print("workerID: $worker->id  connectionID: $connection->id Receive: $data \n");
    Channel\Client::publish('broadcast', $data);
};

$worker->onClose = function ($connection) {
    echo "Connection : {$connection->id}  closed\n";
};

// Run worker
Worker::runAll();
