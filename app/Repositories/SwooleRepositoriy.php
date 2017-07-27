<?php

namespace App\Repositories;

use Cache;

class SwooleRepositoriy
{
    public function onOpen($ws, $request)
    {
        $fd[] = $request->fd;
        $GLOBALS = Cache::pull('fds') ?: [];
        $GLOBALS['fd'][] = $fd;
        Cache::forever('fds', 1);
        print_r(Cache::get('fds'));
        Cache::forever('fds', $GLOBALS);
        echo "client  >> {$request->fd} <<  is connected\n";
    }

    public function onMessage($ws, $frame)
    {
        $GLOBALS = Cache::get('fds');
        $receive = json_decode($frame->data, true);
        if ($receive['type'] == 'init') {
            Cache::forever('mapping:' . $frame->fd, $receive['from']);
        } else {
            foreach($GLOBALS['fd'] as $fd) {
                foreach($fd as $i){
                    if ($frame->fd == $i) {
                        continue;
                    }
                    if (Cache::get('mapping:' . $frame->fd) == Cache::get('mapping:' . $i)) {
                        $ws->push($i,$receive['msg']);
                    }
                }
            }
        }
    }

    public function onClose($ws, $fd)
    {
        echo "client-{$fd} is closed\n";
        $GLOBALS = Cache::pull('fds');
        foreach($GLOBALS['fd'] as $key => $fds) {
            if ($fds[0] == $fd) {
                unset($GLOBALS['fd'][$key]);
            }
        }
        Cache::forever('fds', $GLOBALS);
    }
}
