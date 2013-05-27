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
     * Internal styler.
     * 
     * @var \Mail_Styler 
     */
    private $styler;

    public function __construct(Mail_Styler $styler = NULL) {

        if ($styler === NULL) {
            $styler = Mail_Styler::factory(static::$default_styler);
        }

        $this->styler = $styler;
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
     * Alias for styler->style().
     * 
     * @see Mail_Styler::style 
     * 
     * @param variant $style is style content to update.
     * @return \Mail_Sender for builder syntax.
     */
    public function style($style) {

        $this->styler->style($style);

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

        $headers['Date'] = Date::formatted_time(); // Now        

        $result = TRUE;

        foreach ($receiver as $key => $value) {

            $receiver = $value;

            // Key is an email, therefore value is a name
            if (is_string($key) && Valid::email($key)) {
                $receiver = Model::factory('Mail_Receiver');
                $receiver->email = $key;
                $receiver->name = $value;
            }

            // Value is an email, key is optionally a name
            if (is_string($value) && Valid::email($value)) {
                $receiver = Model::factory('Mail_Receiver');
                $receiver->email = $value;

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
                $_content = View::factory('template/mail', $parameters);

                // Update content in styler
                $this->styler->content($_content);

                // Merge headers over config headers
                $_headers = Arr::merge(Kohana::$config->load('mail.headers'), $headers);

                // Prepare the model
                $mail = new Model_Mail($receiver, $subject, $this->styler, $_headers);

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
