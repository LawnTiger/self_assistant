<?php
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function(swoole_client $cli) {
    sleep(2);
//    $cli->send("test send\n");
    $cli->send('{"code":"init","data":{"id":1}}');
});
$client->on("receive", function(swoole_client $cli, $data){
    echo "Receive: $data"."\n";
});
$client->on("error", function(swoole_client $cli){
    echo "error\n";
});
$client->on("close", function(swoole_client $cli){
    echo "Connection close\n";
});
$client->connect('127.0.0.1', 4000);
