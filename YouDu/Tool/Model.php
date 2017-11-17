<?php

namespace Youdu\Tool;

class Model
{
    protected $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function socket_in($type, $id, $worker, $connection)
    {
        $this->db->delete('socket_mapping')->where("type=2 and user_id={$id}")->query();
        $this->db->insert('socket_mapping')->cols(array(
            'type' => $type,
            'user_id' => $id,
            'worker' => $worker,
            'connection' => $connection))
            ->query();
    }

    public function socket_item($type, $worker, $connection)
    {
        return $this->db->select('*')->from('socket_mapping')
            ->where("type={$type} and worker={$worker} and connection={$connection}")->row();
    }

}
