<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail queue.
 * 
 * @package Mail
 * @category Queues
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Queue extends Mail_Sender implements Iterator {

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
        
        return new $class();
    }    

    private $index = 0;

    /**
     * Override _send to push.
     * 
     * @param Model_Mail $mail
     */
    protected function _send(Model_Mail $mail) {
        $this->push($mail);
    }

    public function current() {
        return $this->peek();
    }

    public function key() {
        return $this->index;
    }

    public function next() {
        // Remove current
        $this->pull();
        $this->index++;
        return $this->peek();
    }

    public function rewind() {
        $this->index = 0;
    }

    public function valid() {
        return $this->peek() instanceof Model_Mail;
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
