<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail sender.
 * 
 * @package Mail
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender {

    /**
     *
     * @var Kohana_Mail_Sender 
     */
    protected static $_instances = array();

    /**
     * Current configuration.
     * @var array 
     */
    private $_config;

    /**
     *
     * @return Mail_Sender 
     */
    public static function instance($name = "default") {
        return isset(Mail_Sender::$_instances[$name]) ? Mail_Sender::$_instances[$name] : Mail_Sender::$_instances[$name] = new Mail_Sender($name);
    }

    /**
     * 
     * @throws Kohana_Exception
     */
    private function __construct($name = "default") {
        $this->_config = Kohana::$config->load("mail.$name");
    }

    /**
     * 
     * @return View
     */
    public function generate_content(Mail_Receiver $receiver, $view, array $parameters = NULL, $subject = NULL) {

        if ($parameters === NULL) {
            $parameters = array();
        }

        if ($subject === NULL) {
            $subject = $this->_config["subject"];
        }

        // $parameters is a model, not an array
        if (!Arr::is_array($parameters)) {
            $parameters = array("model" => $parameters);
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

    public function config($path, $default = NULL, $delimiter = NULL) {
        return Arr::path($this->_config, $path, $default, $delimiter);
    }

    /**
     * Envoie un courriel à tous les utilisateurs de la variable $receivers.
     * Si on fait l'envoi 
     * basé sur la vue et le modèle spécifié.
     * @param Model_User|Database_Result|string $receivers fetchable and loaded ORM model of 
     * receivers, one loaded Model_User, or a valid string email.
     * @param View $view content to be sent.
     * @param array $parameters view's parameters.
     * @param string $subject 
     * @param array $headers
     * @return Boolean false si au moins un envoie échoue.
     */
    public function send(Mail_Receiver $receivers, $view, array $parameters = NULL, $subject = NULL, array $headers = NULL) {

        if (!Arr::is_array($parameters)) {
            $parameters = array(
                "model" => $parameters
            );
        }

        if (is_string($view)) {
            $view = View::factory($view, $parameters);
        }

        foreach ($receivers as $receiver) {
            $result = true;

            foreach ($receivers as $receiver) {

                // Updating receiver..
                $view->set("receiver", $receiver);

                $content = $this->generate_content($receivers, $view, $parameters, $subject);

                $result = $result && $this->_send($receiver, $content, $subject, $headers);
            }

            // Résultat cumulé
            return $result;
        }
    }

    /**
     * Envoie unitaire.
     * @param Model_User $receiver
     * @param type $view
     * @param type $parameters
     * @param type $subject
     * @param type $headers
     * @param type $async 
     * @return boolean true if sending is successful, false otherwise. If sending
     * fails, the mail will be pushed on the queue for later sending.
     * @throws Validation_Exception
     */
    protected function _send(Mail_Receiver $receiver, View $content, $subject = NULL, array $headers = NULL) {

        $mail = new Model_Mail($receiver, $subject, $content, $headers);

        if ($mail->check()) {

            $success = $mail->send();

            if (!$success) {
                Log::instance()->add(Log::CRITICAL, "Mail failed to send. Check server configuration.");
            }

            return $success;
        } else {
            throw new Validation_Exception($mail, "Mail failed to validate.");
        }
    }

}

?>