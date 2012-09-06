<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender de mail.
 */
class Kohana_Mail_Sender {

    protected static $_instance;
    private $_config;
    private $template;
    private $queue;

    /**
     *
     * @return Kohana_Mail_Sender 
     */
    public static function instance() {

        return Kohana_Mail_Sender::$_instance ? Kohana_Mail_Sender::$_instance : new Mail_Sender();
    }

    private function __construct() {
        $this->_config = Kohana::$config->load('mail.default');

        $this->queue = new Mail_Queue($this->_config['queue_path']);
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
    public function send($receivers, $view, $model, $title = "Un message de l'équipe de SaveInTeam") {

        $result = true;

        foreach ($receivers->find_all() as $receiver) {
            $result = $result && $this->send_to_one($receiver, $view, $model, $title);
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
    public function send_to_one($receiver, View $view, ORM $model, string $title = "Un message de l'équipe de SaveInTeam") {
        // Message avec une structure de données à afficher
        $content = new View($view);

        $content->model = $model;

        // $receiver may be an email so we convert it into a user orm model.
        if (is_string($receiver) and Valid::email($receiver)) {
            $temp_email = $receiver;
            $receiver = ORM::factory('user');
            $receiver->email = $temp_email;
        }

        if (!$receiver instanceof Model_User)
            throw new Kohana_Exception("Le receveur n'est pas une instance de Model_User !");

        if (!Valid::email($receiver->email))
            throw new Kohana_Exception("Le email :email est invalide !", array(":email" => $receiver->email));

        $content->receiver = $receiver;

        $this->template->content = $content->render();

        return $this->_send($receiver->email, $title, $this->template->render(), $this->generate_headers($receiver));
    }

    private function _send(string $email, string $subject, string $content, string $headers) {
        if ($this->_config['async']) {
            $this->queue->push();
        } else {

            return mail($email, '=?UTF-8?B?' . base64_encode($subject) . '?=', $content, $headers);
        }
    }

    //////////////////////////////////

    /**
     * 
     * @param string $email
     * @param string $subject
     * @param string $content
     * @param string $headers
     */
    public function push(string $email, string $subject, string $content, string $headers) {
        $filename = Cookie::salt("timestamp", time());

        return file_put_contents($filename, serialize(new Mail_Mail($email, $subject, $content, $headers)));
    }

    public static function salt($timestamp) {
        return sha1($this->_config['salt'] . $timestamp);
    }

    public static function validate_timestamp($timestamp, $digest) {
        return salt($timestamp) === $digest;
    }
    
    public static function validate_file_by_name($name) {
        
        
    }

    public function list_and_sort_files_in_queue() {
        
        $files = scandir($this->queue_path, SCANDIR_SORT_ASCENDING);
        
        
        array_filter($files, Mail_Sender::validate_file_by_name($name));
        
        
        
        return ;
    }

    /**
     * 
     * @param type $iterations
     */
    public function pull() {

        $files = $this->list_and_sort_files_in_queue();

        return unserialize(file_get_contents($files[0]));
    }

}

?>