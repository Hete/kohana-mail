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
    protected static $_instances = array();

    /**
     *
     * @var array 
     */
    private $_config;

    /**
     *
     * @return Kohana_Mail_Sender 
     */
    public static function instance($name = "default") {
        return isset(Kohana_Mail_Sender::$_instances[$name]) ? Kohana_Mail_Sender::$_instances[$name] : Kohana_Mail_Sender::$_instances[$name] = new Mail_Sender($name);
    }

    /**
     * 
     * @throws Kohana_Exception
     */
    private function __construct($name) {
        $this->_config = Kohana::$config->load("mail.$name");

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
     * @param array $model 
     * @return Boolean false si au moins un envoie échoue.
     */
    public function send(Model_User $receivers, $view, $parameters = NULL, $title = NULL) {

        $result = true;

        foreach ($receivers->find_all() as $receiver) {
            $result = $result && $this->send_to_one($receiver, $view, $parameters, $title);
        }

        return $result;
    }

    /**
     * 
     * @param Model_User $receivers
     * @param View $view vue.
     * @param array $parameters paramètres de la vue.
     * @param string $title
     * @param array $variables variables de
     * @return Boolean résultat de la fonction mail().
     */
    public function send_to_one($receiver, $view, $parameters = NULL, $title = NULL) {

        if ($title === NULL) {
            $title = $this->_config['default_subject'];
        }

        if ($parameters === NULL) {
            $parameters = array();
        }

        if (!Arr::is_array($parameters)) {
            $parameters = array("model" => $parameters);
        }

        // $receiver may be an email so we convert it into a user orm model.
        if (is_string($receiver) and Valid::email($receiver)) {
            $temp_email = $receiver;
            $receiver = ORM::factory('user');
            $receiver->email = $temp_email;
        }

        $parameters["title"] = $title;
        $parameters["receiver"] = $receiver;

        if (!Valid::email($receiver->email))
            throw new Kohana_Exception("Le email :email est invalide !", array(":email" => $receiver->email));

        // We use a template clone not to corrupt the original.
        $template = View::factory("mail/layout/template", $parameters);
        $template->header = View::factory("mail/layout/header", $parameters);
        $template->head = View::factory("mail/layout/head", $parameters);
        $template->footer = View::factory("mail/layout/footer", $parameters);

        // We define the template content.
        $template->set("content", View::factory($view, $parameters));

        $mail = new Model_Mail_Mail($receiver->email, $title, $template->render(), $this->generate_headers($receiver));

        if ($this->_config['async']) {
            return $this->push($mail);
        } else {
            $result = $mail->send();
            if (!$result) {
                // On push dans la file, le mail n'a pas pu être envoyé.
                Log::instance()->add(Log::CRITICAL, "L'envoi d'un mail à :email a échoué!", array(":email" => $receiver->email));
                $this->push($mail);
            }
            return $result;
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // Gestion asynchrome

    /**
     * Ajoute un objet Mail_Mail à la fin de la queue.   
     * @param Mail_Mail $mail
     * @return int
     */
    public function push(Mail_Mail $mail) {
        $serialized_mail = serialize($mail);
        $mail_sha1 = sha1($serialized_mail);
        $filename = $this->salt($mail_sha1, time());
        return file_put_contents($this->filename_to_path($filename), $serialized_mail);
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

    /**
     * 
     * @param type $unlink
     * @return boolean|\Mail_Mail FALSE if queue is empty, a Mail_Mail object otherwise.
     * @throws Kohana_Exception
     */
    public function pull($unlink = false) {
        $files = $this->peek_mail_queue();

        if (count($files) === 0) {
            return FALSE;
        }

        $file_path = $this->filename_to_path(array_shift($files));

        $file_content_serialized = file_get_contents($file_path);


        if ($file_content_serialized === FALSE) {

            throw new Kohana_Exception("Le contenu du fichier :fichier n'a pas pu être récupéré.",
                    array(":fichier", $file_path));
        }

        $file_content = unserialize($file_content_serialized);

        if ($file_content === FALSE) {
            throw new Kohana_Exception("La désérialization n'a pas fonctionné sur le fichier :file.",
                    array(":file", $file_path));
        }

        if (!($file_content instanceof Mail_Mail)) {
            throw new Kohana_Exception("Le contenu du fichier :fichier n'est pas de type Mail_Mail.",
                    array(":fichier", $file_path));
        }

        if ($unlink) {
            unlink($file_path);
        }


        return $file_content;
    }

    /**
     * Créé un sel à partir d'un timestamp et le sha1 unique d'un mail.
     * @param string $mail_sha1 mail's content sha1.
     * @param int $timestamp 
     * @return string
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
                ->rule(1, "alpha_numeric");


        if (count($parts) !== 2 | !$validation->check()) {
            return false;
        }

        $mail_sha1 = sha1_file($this->filename_to_path($name));




        return $this->salt($mail_sha1, $parts[0]) === $name;
    }

    /**
     * 
     * @return type
     */
    public function peek_mail_queue() {
        $files = scandir($this->_config['queue_path']);
        return array_filter($files, array($this, "validate_filename"));
    }

}

?>