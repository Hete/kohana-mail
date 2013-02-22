<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Mail
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Queue {

    public static $default = "File";

    /**
     * 
     * @param type $type
     * @return \Mail_Queue
     */
    public function factory($type = NULL) {

        if ($type === NULL) {
            $type = static::$default;
        }

        $class = "Mail_Queue_$type";
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
     * 
     * @return Model_Mail
     */
    abstract function peek();

    /**
     * 
     * @return Model_Mail
     */
    abstract function pull();

    /**
     * 
     * @param Model_Mail $mail
     * @return Mail_Queue for builder syntax
     */
    abstract function push(Model_Mail $mail);
}

?>
