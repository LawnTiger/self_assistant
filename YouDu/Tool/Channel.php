<?php

namespace YouDu\Tool;

use \Channel as WorkerChannel;

class Channel
{
    public static function connect()
    {
        WorkerChannel\Client::connect('127.0.0.1', 2206);
    }

    public static function listen_p2p($listen, $worker)
    {
        WorkerChannel\Client::on($listen, function ($event_data) use ($worker) {
            $to_connection_id = $event_data['connection'];
            if (!isset($worker->connections[$to_connection_id])) {
                echo "ERROR CONNECT: $worker->name -- $worker->id -- $to_connection_id";
                return;
            }
            $worker->connections[$to_connection_id]->send($event_data['content'] . "\n");
        });
    }

    public static function listen_brocast($listen, $worker)
    {
        WorkerChannel\Client::on($listen, function ($event_data) use ($worker) {
            foreach ($worker->connections as $con) {
                $con->send($event_data . "\n");
            }
        });
    }

    public static function publish_p2p($listen, $connection, $content)
    {
        WorkerChannel\Client::publish($listen, array(
            'connection' => $connection,
            'content' => json_encode($content)
        ));
    }
}
