<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail sender.
 * 
 * @package   Mail
 * @category  Senders
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
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
     * Internal styler.
     * 
     * @var \Mail_Styler 
     */
    private $styler;

    /**
     * Internal headers.
     * 
     * @var array 
     */
    private $headers;

    public function __construct() {
        $this->headers = Kohana::$config->load('mail.headers');
    }

    /**
     * Getter-setter for mail headers.
     * 
     * @param string $key
     * @param variant $value
     * @return variant
     */
    public function headers($key = NULL, $value = NULL) {

        if ($key === NULL) {
            return $this->headers;
        }

        if ($value === NULL) {
            return Arr::get($this->headers, $key);
        }

        $this->headers[$key] = (string) $value;

        return $this;
    }

    /**
     * 
     * @param string $cc
     * @return \Mail_Sender
     */
    public function cc($cc = NULL) {
        return $this->headers('Cc', $cc);
    }

    /**
     * 
     * @param string $bcc
     * @return \Mail_Sender
     */
    public function bcc($bcc = NULL) {
        return $this->headers('Bcc', $bcc);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function from($from = NULL) {
        return $this->headers('From', $from);
    }

    /**
     * 
     * @param string $reply_to
     * @return \Mail_Sender
     */
    public function reply_to($reply_to = NULL) {
        return $this->headers('Reply-To', $reply_to);
    }

    /**
     * Getter and setter for styling engine.
     * 
     * @param Mail_Styler $styler
     * @return \Mail_Styler for builder syntax.
     */
    public function styler(Mail_Styler $styler = NULL) {

        if ($styler === NULL) {
            return $this->styler;
        }

        $this->styler = $styler;

        return $this;
    }

    /**
     * Sends an custom email to all receivers.
     * 
     * @param variant $receiver can be  Model_Receiver, an email string or an 
     * array of mixed Mail_Receiver, email => name, email and name => email 
     * elements. It can also be a Model_Mail.
     * @param string $subject is the subject of the mail. It is UTF-8 encoded.
     * @param string $view is a view file.
     * @param array $parameters view's parameters.
     * so you can use accents and other characters.
     * @param array $headers are additionnal headers to override pre-configured
     * ones in mail.headers.
     * @param boolean $force verifies if the receiver is subscribed to the mail.
     * @return boolean cumulated result of all sendings.
     */
    public function send($receiver, $subject, $view, array $parameters = NULL, array $headers = NULL, $force = FALSE) {

        // Receiver is a mail
        if ($receiver instanceof Model_Mail) {
            return $this->_send($receiver);
        }

        if (!Arr::is_array($receiver)) {
            $receiver = array($receiver);
        }

        $headers['Date'] = Date::formatted_time();

        // Merge headers over config headers
        $headers = Arr::merge($this->headers, $headers);

        $result = TRUE;

        foreach ($receiver as $key => $value) {

            $receiver = $value;

            if (is_string($key) && Valid::email($key)) {
                $receiver = Model::factory('Mail_Receiver');
                $receiver->email = $key;
                $receiver->name = $value;
            }

            if (is_string($value) && Valid::email($value)) {
                $receiver = Model::factory('Mail_Receiver');
                $receiver->email = $value;

                // Key can also be an index
                if (is_string($key)) {
                    $receiver->name = $key;
                }
            }

            // Up here, we assume that $receiver implements Mail_Receiver
            // On vérifie si l'utilisateur est abonné
            if ($receiver->receiver_subscribed($view) OR $force) {

                // Update receiver
                $parameters['receiver'] = $receiver;

                // Update content
                $parameters['content'] = View::factory($view, $parameters);

                // Generate content
                $content = View::factory('template/mail', $parameters);

                if ($this->styler !== NULL) {
                    // Update content in styler
                    $content = $this->styler
                            ->content($content)
                            ->render();
                }

                // Prepare the model
                $mail = new Model_Mail($receiver, $subject, $content, $headers);

                // Send and cumulate the result
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
     * @return boolean TRUE if sending is successful, FALSE otherwise.
     */
    protected abstract function _send(Model_Mail $mail);
}

?>
