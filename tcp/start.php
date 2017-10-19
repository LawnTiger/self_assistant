<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;

// Create a Websocket server
$worker = new Worker("tcp://0.0.0.0:4000");
$worker->count = 2;

$worker->onWorkerStart = function ($worker) {

};

$worker->onConnect = function ($connection) use ($worker) {
    echo "New connection\n";
    $connection->send("welcome to fu*king test room\n");
};

$worker->onMessage = function ($connection, $data) use ($worker) {
    print("workerID: $worker->id  connectionID: $connection->id Receive: $data \n");
    foreach ($worker->connections as $key => $conn) {
        $conn->send('somebody: ' . $data);
    }
};

$worker->onClose = function ($connection) {
    echo "Connection : {$connection->id}  closed\n";
};

// Run worker
Worker::runAll();
