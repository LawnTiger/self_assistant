<?php

class Content
{
    protected static $get_msg = array(
        'code' => 'msg',
        'data' => array(
            'chatType' => '',
            'groupId' => '',
            'userId' => '',
            'userName' => '',
            'groupName' => '',
            'time' => '',
            'content' => array(
                'contentType' => 'txt',
                'body' => ''
            )
        )
    );

    protected static $notice = array(
        'code' => 'notice',
        'data' => array(
            'type' => '',
            'id' => '',
            'name' => '',
            'time' => '',
            'content' => ''
        )
    );

    public static function msg($type, $user_id, $user_name, $time, $body, $group_id = null, $group_name = null)
    {
        self::$get_msg['data']['chatType'] = $type;
        self::$get_msg['data']['userId'] = $user_id;
        self::$get_msg['data']['userName'] = $user_name;
        self::$get_msg['data']['time'] = $time;
        self::$get_msg['data']['content']['body'] = $body;
        self::$get_msg['data']['groupId'] = $group_id;
        self::$get_msg['data']['groupName'] = $group_name;

        return self::$get_msg;
    }

    public static function notice($type, $id, $name, $time, $content, $is_accept = null)
    {
        self::$notice['data']['type'] = $type;
        self::$notice['data']['id'] = $id;
        self::$notice['data']['name'] = $name;
        self::$notice['data']['time'] = $time;
        self::$notice['data']['content'] = $content;

        if ($is_accept) {
            self::$notice['data']['content'] = $is_accept;
        }

        return self::$notice;
    }
}
