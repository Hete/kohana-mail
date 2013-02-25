<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail queue.
 * 
 * @package Mail
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Queue {

    public static $default = "File";

    /**
     * 
     * @param type $name
     * @return \Mail_Queue
     */
    public function factory($name = NULL) {

        if ($name === NULL) {
            $name = static::$default;
        }

        $class = "Mail_Queue_$name";
        return new $class(strtolowe($name));
    }

    /**
     * Specific configuration.
     * @var array 
     */
    private $_config;

    public function __construct($name) {
        $this->_config = Kohana::$config->load("mail.queue.$name");
    }

    protected function config($path, $default = NULL, $delimiter = NULL) {
        return Arr::path($this->_config, $path, $default, $delimiter);
    }

    /**
     * Alias for push. Allow the use of send on a queue.
     * 
     * @param Model_Mail $mail
     */
    public function send($limit = 1) {
        while ($limit > 0 && $mail = $this->pull()) {
            Mail_Sender::factory()->_send($mail);
        }
    }

    public function send_all() {
        while ($mail = $this->pull()) {
            Mail_Sender::factory()->_send($mail);
        }
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
