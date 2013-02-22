<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Mail
 */
class Kohana_Mail_Queue_File extends Mail_Queue {

    public function peek() {
        $files = $this->mail_queue();

        if (count($files) === 0) {
            return FALSE;
        }

        $file_path = $this->filename_to_path(array_shift($files));

        $file_content_serialized = file_get_contents($file_path);


        if ($file_content_serialized === FALSE) {
            Log::instance()->add(Log::CRITICAL, "Le contenu du fichier :fichier n'a pas pu être récupéré.", array(":fichier", $file_path));
            unlink($file_path);
            return $this->pull();
        }

        $file_content = unserialize($file_content_serialized);

        if ($file_content === FALSE) {
            Log::instance()->add(Log::CRITICAL, "La désérialization n'a pas fonctionné sur le fichier :file.", array(":file", $file_path));
            unlink($file_path);
            return $this->pull();
        }

        if (!($file_content instanceof Mail_Mail)) {
            Log::instance()->add(Log::CRITICAL, "Le contenu du fichier :fichier n'est pas de type Mail_Mail.", array(":fichier", $file_path));
            unlink($file_path);
            return $this->pull();
        }

        return $file_content;
    }

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
    public function pull() {

        $this->peek();

        if ($unlink) {
            unlink($file_path);
        }
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
     * Obtain the mail queue sorted by time and filtered by validity.
     * @return type
     */
    public function mail_queue() {
        $files = scandir($this->_config['async']['path']);

        $valid_files = array_filter($files, array($this, "validate_filename"));

        usort($valid_files, array($this, "compare_filenames"));

        return $valid_files;
    }

    /**
     * Converts filename from a mail in the queue to a path.
     * @param string $filename
     * @return string
     */
    private function filename_to_path($filename) {
        return $this->_config['async']['path'] . "/" . $filename;
    }

    /**
     * Créé un sel à partir d'un timestamp et le sha1 unique d'un mail.
     * @param string $mail_sha1 mail's content sha1.
     * @param int $timestamp 
     * @return string
     */
    public function salt($mail_sha1, $timestamp) {
        return $timestamp . "~" . sha1($this->_config['async']['salt'] . $mail_sha1 . $timestamp);
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

}

?>
