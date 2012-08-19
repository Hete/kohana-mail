<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender de mail.
 */
class Kohana_Mail_Sender {

    protected static $_instance;
    private $_config;
    private $template;

    /**
     *
     * @return Kohana_Mail_Sender 
     */
    public static function instance() {

        return Kohana_Mail_Sender::$_instance ? Kohana_Mail_Sender::$_instance : new Mail_Sender();
    }

    private function __construct() {
        $this->_config = Kohana::$config->load('mail.default');
        $this->template = View::factory("mail/layout/template");
        $this->template->header = View::factory("mail/layout/header");
        $this->template->head = View::factory("mail/layout/head");
        $this->template->footer = View::factory("mail/layout/footer");
    }

    private function generate_headers($receiver) {

        // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        // En-têtes additionnels
        $headers .= 'To: ' . $receiver->nom_complet() . ' <' . $receiver->email . '>' . "\r\n";
        $headers .= 'From: SaveInTeam <' . $this->_config['from'] . '>' . "\r\n";

        return $headers;
    }

    /**
     * Envoie un courriel à tous les utilisateurs de la variable $receivers
     * basé sur la vue et le modèle spécifié.
     * @param Model_User $receivers
     * @param View $view
     * @param ORM $model 
     * @return Boolean false si au moins un envoie échoue.
     */
    public function send($receivers, $view, $model) {
        
        $result = true;

        foreach ($receivers->find_all() as $receiver) {
            $result = $result && $this->send_to_one($receiver, $view, $model);
        }

        return $result;
    }

    /**
     * 
     * @param Model_User $receivers
     * @param View $view
     * @param ORM $model 
     * @return Boolean résultat de la fonction mail().
     */
    public function send_to_one($receiver, $view, $model) {

        // Message avec une structure de données à afficher
        $content = new View($view);

        $content->model = $model;

        // $receiver may be an email so we convert it into a user orm model.
        if (is_string($receiver) and Valid::email($receiver)) {

            $receiver = ORM::factory('user');
            $receiver->email = $receiver;
        }

        $content->receiver = $receiver;

        $this->template->content = $content->render();



        return mail($receiver->email, '=?UTF-8?B?' . base64_encode("Un message de l'équipe de SaveInTeam") . '?=', $this->template->render(), $this->generate_headers($receiver));
    }

}

?>