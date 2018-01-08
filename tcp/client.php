<?php
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function(swoole_client $cli) {
    sleep(2);
    $cli->send('{"code":"init","data":{"id":4}}');
//    sleep(2);
//    $cli->send('{"code": "msg", "data": {"chatType": "p2p", "id": 1, "time":8848, "content": {"contentType": "txt", "body": "hello"} } }');
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
//$client->connect('192.168.10.10', 4000);
$client->connect('59.110.136.203', 4000);
