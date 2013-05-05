<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Mail
 * @category Models
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Model_Mail_Receiver extends Model_Validation implements Mail_Receiver {

    public $name, $email;

    public function receiver_email() {
        return $this->email;
    }

    public function receiver_name() {
        return $this->name;
    }

    public function receiver_subscribed($view) {
        return TRUE;
    }

    public function rules() {
        return array(
            "name" => array(
                array("not_empty")
            ),
            "email" => array(
                array("not_empty"),
                array("email")
            ),
        );
    }

}

?>
