<?php

function autoload($className)
{
    $fileName = str_replace('\\', DIRECTORY_SEPARATOR,  __DIR__ . '\\..\\'. $className) . '.php';
    if (is_file($fileName)) {
        require $fileName;
    } else {
        echo $fileName . " is not exist \n";
    }
}
spl_autoload_register('autoload');

require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use YouDu\WebSocket;

$channel = new Channel\Server('127.0.0.1', 2206);

$ws = new Worker("websocket://0.0.0.0:4001");
$ws->count = 2;
$ws->name  = 'ws';

$websocket = new WebSocket();
$ws->onWorkerStart = array($websocket, 'onWorkerStart');
$ws->onConnect     = array($websocket, 'onConnect');
$ws->onMessage     = array($websocket, 'onMessage');
$ws->onClose       = array($websocket, 'onClose');
$ws->onWorkerStop  = array($websocket, 'onWorkerStop');

Worker::runAll();
