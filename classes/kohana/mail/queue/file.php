<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * File-based queue.
 * 
 * @package Mail
 * @category Queues
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Queue_File extends Mail_Queue {

    public function push(Model_Mail $mail) {
        $serialized_mail = serialize($mail);
        $mail_sha1 = sha1($serialized_mail);
        $filename = $this->salt($mail_sha1, time());
        return file_put_contents($this->filename_to_path($filename), $serialized_mail);
    }

    public function peek() {
        $queue = $this->queue();

        if (count($queue) < 1) {
            return NULL;
        }

        $mail = unserialize(file_get_contents($queue[0]));

        return $mail;
    }

    public function pull() {

        $queue = $this->queue();

        if (count($queue) < 1) {
            return NULL;
        }

        $mail = unserialize(file_get_contents($queue[0]));

        unlink($queue[0]);

        return $mail;
    }

    /**
     * Obtain the mail queue sorted by time and filtered by validity.
     * @return type
     */
    public function queue() {
        $files = scandir(Kohana::$config->load('mail.queue.file.path'));

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
        return Kohana::$config->load("mail.queue.file.path") . $filename;
    }

    /**
     * Créé un sel à partir d'un timestamp et le sha1 unique d'un mail.
     * @param string $mail_sha1 mail's content sha1.
     * @param int $timestamp 
     * @return string
     */
    public function salt($mail_sha1) {
        return time() . "~" . $mail_sha1;
    }

    /**
     * Validate a file name against its content.
     * @param string $name is the file name, not its path.
     * @return boolean true if the content represents its name, false otherwise.
     */
    public function validate_filename($name) {
        $parts = explode("~", $name);

        $validation = Validation::factory($parts)
                ->rule(0, "not_empty")
                ->rule(0, "digit")
                ->rule(1, "not_empty")
                ->rule(1, "alpha_numeric")
                ->rule(1, "equals", array(":value", sha1_file($this->filename_to_path($name))));

        return $validation->check();
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
