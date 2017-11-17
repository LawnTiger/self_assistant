<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/WebSocket.php';
require_once __DIR__ . '/Tool/Content.php';
require_once __DIR__ . '/Tool/Model.php';

use Workerman\Worker;

$channel = new Channel\Server('127.0.0.1', 2206);

$ws = new Worker("websocket://0.0.0.0:4001");
$ws->count = 2;

$websocket = new \Youdu\WebSocket();
$ws->onWorkerStart = array($websocket, 'onWorkerStart');
$ws->onConnect     = array($websocket, 'onConnect');
$ws->onMessage     = array($websocket, 'onMessage');
$ws->onClose       = array($websocket, 'onClose');
$ws->onWorkerStop  = array($websocket, 'onWorkerStop');

Worker::runAll();
