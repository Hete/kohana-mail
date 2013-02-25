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

    public static $default = "Native";

    /**
     *
     * @return Mail_Sender 
     */
    public static function factory($name = NULL) {

        if ($name === NULL) {
            $name = static::$default;
        }
    

        $class = "Mail_Sender_$name";
        $name = strtolower($name);

        return new $class($name);
    }

    /**
     * Current configuration.
     * @var array 
     */
    private $_config;

    private function __construct($name) {
        // Load the corresponding configuration
        $this->_config = Kohana::$config->load("mail.sender." . str_replace("_", ".", $name));
    }

    /**
     * Content generation function.
     * 
     * @todo améliorer l'implémentation pour la génération du contenu.
     * @return View
     */
    public function generate_content(Mail_Receiver $receiver, $view, array $parameters = NULL, $subject = NULL) {

        if ($parameters === NULL) {
            $parameters = array();
        }

        if ($subject === NULL) {
            $subject = $this->_config["subject"];
        }

        $parameters["title"] = $subject;
        $parameters["receiver"] = $receiver;

        // We use a template clone not to corrupt the original.
        $template = View::factory("mail/layout/template", $parameters);
        $template->header = View::factory("mail/layout/header", $parameters);
        $template->head = View::factory("mail/layout/head", $parameters);
        $template->footer = View::factory("mail/layout/footer", $parameters);

        // We define the template content.
        $template->set("content", View::factory($view, $parameters));

        return $template;
    }

    public function config($path = NULL, $default = NULL, $delimiter = NULL) {

        if ($path === NULL) {
            return $this->_config;
        }
    

        return Arr::path($this->_config, $path, $default, $delimiter);
    }

    /**
     * Envoie un courriel à tous les utilisateurs de la variable $receivers.
     * 
     * @param Mail_Receiver $receivers fetchable and loaded ORM model of 
     * receivers, one loaded Model_User, or a valid string email.
     * @param View $view content to be sent.
     * @param array $parameters view's parameters.
     * @param string $subject 
     * @param array $headers
     * @return boolean false si au moins un envoie échoue.
     */
    public function send(Mail_Receiver $receivers, $view, array $parameters = NULL, $subject = NULL, array $headers = NULL) {

        if ($subject === NULL) {
            $subject = $this->config("subject");
        }

        $headers["From"] = $this->config("from.name");
        $headers["Date"] = Date::formatted_time("now");
        $headers["Content-type"] = "text/html; charset=UTF-8";
        $headers["MIME-Version"] = "1.0";

        $result = true;

        if (!($receivers instanceof Traversable)) {
            $receivers = array($receivers);
        }

        foreach ($receivers as $receiver) {

            // Update receiver
            $parameters["receiver"] = $receiver;

            // Update headers
            $headers["To"] = $receiver->receiver_name() . " <" . $receiver->receiver_email() . ">";

            // Regenerate content
            $content = $this->generate_content($receiver, $view, $parameters, $subject);

            $mail = new Model_Mail($receiver, $subject, $content, $headers);

            $result = $result && $this->_send($mail);
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