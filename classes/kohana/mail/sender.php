<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender de mail.
 */
class Kohana_Mail_Sender {

    /**
     *
     * @var Kohana_Mail_Sender 
     */
    protected static $_instances;

    /**
     *
     * @var array 
     */
    private $_config;

    /**
     *
     * @var type 
     */
    private $template;

    /**
     *
     * @return Kohana_Mail_Sender 
     */
    public static function instance($name = "default") {
        return Kohana_Mail_Sender::$_instances[$name] ? Kohana_Mail_Sender::$_instances[$name] : Kohana_Mail_Sender::$_instances[$name] = new Mail_Sender($name);
    }

    /**
     * 
     * @throws Kohana_Exception
     */
    private function __construct($name) {
        $this->_config = Kohana::$config->load("mail.$name");
        $this->template = View::factory("mail/layout/template");
        $this->template->header = View::factory("mail/layout/header");
        $this->template->head = View::factory("mail/layout/head");
        $this->template->footer = View::factory("mail/layout/footer");

        if ($this->_config['async']) {
            if (!is_writable($this->_config['queue_path']))
                throw new Kohana_Exception("Folder :folder is not writeable.", array(":folder" => Kohana::$config->load('mail.default.queue_path')));

            if ($this->_config['salt'] === NULL)
                throw new Kohana_Exception("Salt is not defined.");
        }
    }

    /**
     * 
     * @param Model_User $receiver
     * @return string
     */
    private function generate_headers(Model_User $receiver) {

        // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        // En-têtes additionnels
        $headers .= 'To: ' . $receiver->nom_complet() . ' <' . $receiver->email . '>' . "\r\n";
        $headers .= 'From: ' . $this->_config['from_name'] . ' <' . $this->_config['from'] . '>' . "\r\n";

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
    public function send(Model_User $receivers, $view, ORM $model, $title = NULL) {

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
    public function send_to_one($receiver, $view, ORM $model, $title = NULL) {

        if ($title === NULL) {

            $title = $this->_config['default_subject'];
        }

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

        return $this->_send(new Mail_Mail($receiver->email, $title, $this->template->render(), $this->generate_headers($receiver)));
    }

    /**
     * Fonction d'envoie.
     * @param string $email
     * @param string $subject
     * @param string $content
     * @param string $headers
     * @return type
     */
    private function _send(Mail_Mail $mail) {
        if ($this->_config['async']) {
            return $this->push($mail);
        } else {
            return $mail->send();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // Gestion asynchrome

    /**
     * Ajoute un objet Mail_Mail à la fin de la queue.     
     */
    public function push(Mail_Mail $mail) {
        $filename = $this->salt($mail = serialize($mail), time());
        return file_put_contents($this->filename_to_path($filename), serialize($mail));
    }

    /**
     * Converts filename from a mail in the queue to a path.
     * @param string $filename
     * @return string
     */
    private function filename_to_path($filename) {
        return $this->_config['queue_path'] . "/" . $filename;
    }

    /**
     * Retourne l'objet Mail_Mail au début de la queue.
     * Si l'objet est retournable, 
     * @param Mail_Mail $iterations
     */
    public function pull() {

        $files = $this->peek_mail_queue();

        return unserialize(file_get_contents($this->filename_to_path(array_shift($files))));
    }

    /**
     * Créé un sel à partir d'un timestamp et le sha1 unique d'un mail.
     * @param integer $timestamp
     * @return type
     */
    public function salt($mail_sha1, $timestamp) {
        return $timestamp . "~" . sha1($this->_config['salt'] . $mail_sha1 . $timestamp);
    }

    /**
     * Valide un nom de fichier.
     * @param string $name
     * @return type
     */
    public function validate_filename($name) {
        $parts = explode("~", $name);

        $validation = Validation::factory($parts)
                ->rule(0, "digit")
                ->rule(1, "aplha_numeric");

        if (count($parts) !== 2 | !$validation->check()) {
            return false;
        }

        $mail_sha1 = sha1_file($this->filename_to_path($name));
        return $this->salt($mail_sha1, $parts[0]) === $parts[1];
    }

    /**
     * 
     * @return type
     */
    public function peek_mail_queue() {
        $files = scandir($this->queue_path, SCANDIR_SORT_ASCENDING);
        return array_filter($files, $this->validate_filename($name));
    }

}

?>