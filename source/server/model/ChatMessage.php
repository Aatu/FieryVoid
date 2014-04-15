<?php

class ChatMessage
{
    public $id, $userid, $username, $gameid, $message, $time;
    
    public function __construct($id, $userid, $username, $gameid, $message, $time) {
        $this->id = $id;
        $this->userid = $userid;
        $this->username = $username;
        $this->gameid = $gameid;
        $this->message = $message;
        $this->time = $time;
    }
}

