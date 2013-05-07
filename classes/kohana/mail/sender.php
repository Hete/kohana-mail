<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail sender.
 * 
 * @package Mail
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Sender {

    /**
     * Default sender. 
     * 
     * @var string 
     */
    public static $default = "Sendmail";

    /**
     * Return an instance of the specified sender.
     * 
     * @return Mail_Sender 
     */
    public static function factory($name = NULL) {

        if ($name === NULL) {
            $name = static::$default;
        }

        $class = "Mail_Sender_$name";

        return new $class();
    }

    /**
     * Encode a value for headers.
     * 
     * @param string $value
     * @return string
     */
    public static function encode($value) {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    /**
     * Current configuration.
     * @var array 
     */
    private $_config;

    protected function __construct() {
        // Load the corresponding configuration
        $this->_config = Kohana::$config->load("mail.sender");
    }

    /**
     * Generate headers for the specified receiver. $receiver is not yet used,
     * but will be used to u
     * 
     * @param Mail_Receiver $receiver
     * @return array
     */
    public function basic_headers() {
        return array(
            "From" => static::encode(Kohana::$config->load("mail.sender.from.name")) . " <" . Kohana::$config->load("mail.sender.from.email") . ">",
            "Date" => Date::formatted_time("now"),
            "Content-type" => "text/html; charset=UTF-8",
            "MIME-Version" => "1.0"
        );
    }

    /**
     * Envoie un courriel à tous les utilisateurs de la variable $receivers.
     * 
     * @param Mail_Receiver|Traversable|array $receivers set of Mail_Receiver or
     * a Mail_Receiver object.
     * @param View $view content to be sent.
     * @param array $parameters view's parameters.
     * @param string $subject is the subject of the mail. It is UTF-8 encoded, 
     * so you can use accents and other characters.
     * @param array $headers is an array of mail headers.
     * @param boolean $force verifies if the receiver is 
     * subscribed to the mail.
     * @return boolean false si au moins un envoie échoue.
     */
    public function send($receivers, $subject, $view, array $parameters = NULL, array $headers = NULL, $force = FALSE) {

        if ($headers === NULL) {
            $headers = array();
        }

        if (!Arr::is_array($receivers)) {
            $receivers = array($receivers);
        }

        $result = true;

        foreach ($receivers as $key => $value) {

            $receiver = $value;

            if (is_string($key) && Valid::email($key)) {
                $receiver->email = $key;
                $receiver->name = $value;
            }

            if (is_string($key) && Valid::email($value)) {
                $receiver->email = $value;
            }

            if (!$value instanceof Mail_Receiver) {
                throw new Kohana_Exception("Invalid receiver :receiver", array(":receiver" => $receiver));
            }

            // On vérifie si l'utilisateur est abonné
            if ($value->receiver_subscribed($view) OR $force) {

                // Update receiver
                $parameters["receiver"] = $value;

                // Update content
                $parameters["content"] = View::factory($view, $parameters);

                // Generate content
                $_content = View::factory("template/mail", $parameters);

                // Merge headers over basic ones
                $_headers = Arr::merge($this->basic_headers(), $headers);

                $mail = new Model_Mail($value, $subject, $_content, $_headers);

                $result = $result AND $this->_send($mail);
            }
        }

        // Cumulated result
        return $result;
    }

    /**
     * Implemented by the sender.
     * 
     * @param Model_Mail $mail  
     * @return boolean true if sending is successful, false otherwise.
     */
    public abstract function _send(Model_Mail $mail);
}

?>
