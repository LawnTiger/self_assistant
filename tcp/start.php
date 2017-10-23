<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;

$channel = new Channel\Server('127.0.0.1', 2206);

$worker = new Worker("websocket://0.0.0.0:4001");
$worker->count = 2;
$tcp = new Worker("tcp://0.0.0.0:4000");
$tcp->count = 2;


// websocket
$worker->onWorkerStart = function ($worker) {
    Channel\Client::connect('127.0.0.1', 2206);
    Channel\Client::on('broadcast', function($event_data) use($worker) {
        foreach ($worker->connections as $con) {
            $con->send($event_data);
        }
    });
};

// tcp
$tcp->onWorkerStart = function ($tcp) {
    Channel\Client::connect('127.0.0.1', 2206);
    Channel\Client::on('broadcast', function($event_data) use($tcp) {
        foreach ($tcp->connections as $con) {
            $con->send($event_data);
        }
    });
};

$worker->onConnect = 'handle_connect';
$worker->onMessage = 'handle_message';
$worker->onClose = 'handle_close';

$tcp->onConnect = 'handle_connect';
$tcp->onMessage = 'handle_message';
$tcp->onClose = 'handle_close';

function handle_connect($connection)
{
    echo "connectionID: $connection->id\n";
    $connection->send("welcome to fu*king test room\n");
}

function handle_message($connection, $data)
{
    print("connectionID: $connection->id Receive: $data \n");
    Channel\Client::publish('broadcast', $data);
}

function handle_close($connection)
{
    echo "Connection : {$connection->id}  closed\n";
}


// Run worker
Worker::runAll();
