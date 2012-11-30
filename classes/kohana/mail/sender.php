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
     * Current configuration.
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
     * @return View
     */
    protected function generate_content(Model_User $receiver, $view, array $parameters = NULL, $title = NULL) {


        if ($title === NULL) {
            $title = $this->_config['default_subject'];
        }

        if ($parameters === NULL) {
            $parameters = array();
        }

        // $parameters is a model, not an array
        if (!Arr::is_array($parameters)) {
            $parameters = array("model" => $parameters);
        }

        $parameters["title"] = $title;
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

    /**
     * Envoie un courriel à tous les utilisateurs de la variable $receivers
     * basé sur la vue et le modèle spécifié.
     * @param Model_User $receivers fetchable ORM model of receivers.
     * @param View $view content to be sent.
     * @param array $parameters view's parameters.
     * @param string $subject 
     * @param array $headers
     * @return Boolean false si au moins un envoie échoue.
     */
    public function send(Model_User $receivers, $view, $parameters = NULL, $subject = NULL, $headers = NULL) {

        if (!Arr::is_array($parameters)) {
            $parameters = array(
                "model" => $parameters
            );
        }

        $result = true;

        foreach ($receivers->find_all() as $receiver) {

            $content = $this->generate_content($receiver, $view, $parameters, $subject);


            $mail = new Model_Mail($receiver, $subject, $content, $headers);

            if ($mail->check()) {

                $success = $mail->send(Arr::get($this->_config, "async", FALSE));

                if (!$success) {
                    Log::instance()->add(Log::CRITICAL, "Mail failed to send. Check server configuration.");
                    $this->push($mail);
                }

                $result = $result && $success;
            } else {
                throw new Validation_Exception("Mail failed to validate.");
            }
        }

        return $result;
    }

    /**
     * Alias de la fonction send.
     * @param Model_User|string $receivers may be a Model_User or a valid email.
     * @param string $view vue.
     * @param array $parameters paramètres de la vue.
     * @param string $subject
     * @param array $variables variables de     * 
     * @return Boolean résultat de la fonction mail().
     * 
     * @deprecated Simply use send.
     */
    public function send_to_one($receiver, $view, $parameters = NULL, $subject = NULL, $headers = NULL) {

        // $receiver may be an email so we convert it into a user orm model.
        if (is_string($receiver) and Valid::email($receiver)) {
            $temp_email = $receiver;
            $receiver = ORM::factory('user');
            $receiver->email = $temp_email;
        }

        return $this->send($receiver, $view, $parameters, $subject, $headers);
    }

    ////////////////////////////////////////////////////////////////////////////
    // Gestion asynchrome

    /**
     * Ajoute un objet Mail_Mail à la fin de la queue.   
     * @param Mail_Mail $mail
     * @return int
     */
    public function push(Model_Mail $mail) {
        $serialized_mail = serialize($mail);
        $mail_sha1 = sha1($serialized_mail);
        $filename = $this->salt($mail_sha1, time());
        return file_put_contents($this->filename_to_path($filename), $serialized_mail);
    }

    /**
     * Retourne l'objet Mail_Mail au début de la queue.
     * @param type $unlink
     * @return boolean|Model_Mail FALSE if queue is empty, a Mail_Mail object otherwise.
     * @throws Kohana_Exception
     */
    public function pull($unlink = FALSE) {
        $files = $this->peek_mail_queue();

        if (count($files) === 0) {
            return FALSE;
        }

        $file_path = $this->filename_to_path(array_shift($files));

        $file_content_serialized = file_get_contents($file_path);


        if ($file_content_serialized === FALSE) {

            throw new Kohana_Exception("Le contenu du fichier :fichier n'a pas pu être récupéré.", array(":fichier", $file_path));
        }

        $file_content = unserialize($file_content_serialized);

        if ($file_content === FALSE) {
            throw new Kohana_Exception("La désérialization n'a pas fonctionné sur le fichier :file.", array(":file", $file_path));
        }

        if (!($file_content instanceof Mail_Mail)) {
            throw new Kohana_Exception("Le contenu du fichier :fichier n'est pas de type Mail_Mail.", array(":fichier", $file_path));
        }

        if ($unlink) {
            unlink($file_path);
        }

        return $file_content;
    }

    /**
     * Pulls the element at the end of the queue and send it.
     */
    public function pull_and_send($unlink = TRUE) {
        $model = $this->pull($unlink);
        if ($model === FALSE) {
            return TRUE;
        } else {
            return $model->send();
        }
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
     * Créé un sel à partir d'un timestamp et le sha1 unique d'un mail.
     * @param string $mail_sha1 mail's content sha1.
     * @param int $timestamp 
     * @return string
     */
    public function salt($mail_sha1, $timestamp) {
        return $timestamp . "~" . sha1($this->_config['salt'] . $mail_sha1 . $timestamp);
    }

    /**
     * Validate a file name against its content.
     * @param string $name is the file name, not its path.
     * @return boolean true if the content represents its name, false otherwise.
     */
    public function validate_filename($name) {
        $parts = explode("~", $name);

        $validation = Validation::factory($parts)
                ->rule(0, "digit")
                ->rule(1, "alpha_numeric");


        if (count($parts) !== 2 | !$validation->check()) {
            // It's not that terrible, but still we warn the user.
            Log::instance()->add(Log::INFO, "Invalid file :file in mail queue :path.", array(":file" => $name, ":path" => $this->filename_to_path($name)));
            return false;
        }

        $mail_sha1 = sha1_file($this->filename_to_path($name));




        return $this->salt($mail_sha1, $parts[0]) === $name;
    }

    /**
     * Compare two filenames for usort function.
     * @param type $name1
     * @param type $name2
     * @return int
     */
    public function compare_filenames($name1, $name2) {

        $parts1 = explode("~", $name1);
        $parts2 = explode("~", $name2);

        $name1 = $parts1[0];
        $name2 = $parts2[0];

        if ($name1 == $name2) {
            return 0;
        }
        return ($name1 < $name2) ? -1 : 1;
    }

    /**
     * Obtain the mail queue sorted by time and filtered by validity.
     * @return type
     */
    public function mail_queue() {
        $files = scandir($this->_config['queue_path']);

        $valid_files = array_filter($files, array($this, "validate_filename"));

        return usort($valid_files, array($this, "compare_filenames"));
    }

}

?>