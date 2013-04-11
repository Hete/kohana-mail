<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail queue.
 * 
 * @package Mail
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Queue extends Mail_Sender {

    public static $default = "File";

    /**
     * 
     * @param type $name
     * @return \Mail_Queue
     */
    public static function factory($name = NULL) {

        if ($name === NULL) {
            $name = static::$default;
        }

        $class = "Mail_Queue_$name";
        return new $class(strtolower($name));
    }

    protected function __construct() {
        
    }

    public function _send(Model_Mail $mail) {
        $this->push($mail);
    }

    /**
     * Return the lastest mail in the queue, but do not remove it.
     * 
     * @return Model_Mail
     */
    abstract function peek();

    /**
     * Remove the latest mail in the queue and return it.
     * 
     * @return Model_Mail
     */
    abstract function pull();

    /**
     * Push an item at the end of the mail que.
     * 
     * @param Model_Mail $mail
     * @return Mail_Queue for builder syntax
     */
    abstract function push(Model_Mail $mail);
}

?>
