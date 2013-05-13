<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail sender.
 * 
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Sender {

    /**
     * Default sender. 
     * 
     * @var string 
     */
    public static $default = 'Sendmail';

    /**
     * Default styling engine.
     * 
     * @var string 
     */
    public static $default_styler = 'HTML';

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
     * Generates basic headers.
     *  
     * @return array
     */
    public static function basic_headers() {
        return Arr::merge(Kohana::$config->load('mail.headers'), array(
                    'From' => static::encode(Kohana::$config->load('mail.from_name')) . ' <' . Kohana::$config->load('mail.from_email') . '>',
                    'Date' => Date::formatted_time("now"),
        ));
    }

    /**
     *
     * @var \Mail_Styler 
     */
    public $styler;

    public function __construct(Mail_Styler $styler = NULL) {

        if ($styler === NULL) {
            $styler = Mail_Styler::factory(static::$default_styler);
        }

        $this->styler = $styler;
    }

    /**
     * 
     * 
     * @param Mail_Styler $styler
     * @return Mail_Styler 
     */
    public function styler(Mail_Styler $styler = NULL) {

        if ($styler === NULL) {
            return $this->styler;
        }

        $this->styler = $styler;

        return $this;
    }

    public function style($style) {

        $this->styler->style($style);

        return $this;
    }

    /**
     * Envoie un courriel à tous les utilisateurs de la variable $receivers.
     * 
     * @param Mail_Receiver|Traversable|array $receivers set of Mail_Receiver or
     * a Mail_Receiver object.
     * @param string $view content to be sent.
     * @param array $parameters view's parameters.
     * @param string $subject is the subject of the mail. It is UTF-8 encoded, 
     * so you can use accents and other characters.
     * @param array $headers is an array of mail headers.
     * @param boolean $force verifies if the receiver is 
     * subscribed to the mail.
     * @return boolean false si au moins un envoie échoue.
     */
    public function send($receivers, $subject, $view, array $parameters = NULL, array $headers = array(), $force = FALSE) {

        if (!Arr::is_array($receivers)) {
            $receivers = array($receivers);
        }

        $result = true;

        foreach ($receivers as $key => $value) {

            $receiver = $value;

            // Key is an email, therefore value is a name
            if (is_string($key) && Valid::email($key)) {
                $receiver = Model::factory("Mail_Receiver");
                $receiver->email = $key;
                $receiver->name = $value;
            }

            // Value is an email, key is optionally a name
            if (is_string($value) && Valid::email($value)) {
                $receiver = Model::factory("Mail_Receiver");
                $receiver->email = $value;

                if (is_string($key)) {
                    $receiver->name = $key;
                }
            }

            // Up here, we assume that $receiver implements Mail_Receiver
            // On vérifie si l'utilisateur est abonné
            if ($receiver->receiver_subscribed($view) OR $force) {

                // Update receiver
                $parameters["receiver"] = $receiver;

                // Update content
                $parameters["content"] = View::factory($view, $parameters);

                // Generate content
                $_content = View::factory("template/mail", $parameters);

                // Update content in styler
                $this->styler->content($_content);                

                // Merge headers over basic ones
                $_headers = Arr::merge(static::basic_headers(), $headers);

                $mail = new Model_Mail($receiver, $subject, $this->styler, $_headers);

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
