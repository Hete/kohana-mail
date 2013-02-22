<?php

class Kohana_Model_Mail_Receiver extends Model implements Mail_Receiver {

    public $email, $name;

    public function receiver_email() {
        return $this->email;
    }

    public function receiver_name() {
        return $this->name;
    }

}

?>
