<?php

class Kohana_Mail_Queue {

    private $queue_path;

    public function __construct($queue_path) {

        if (!is_writable($queue_path)) {
            throw new Kohana_Exception("The mail queue path :path is not writeable !", array(":path" => $queue_path));
        }

        $this->queue_path = $queue_path;
    }

    /**
     * 
     * @param string $email
     * @param string $subject
     * @param string $content
     * @param string $headers
     */
    public function push(string $email, string $subject, string $content, string $headers) {
        return file_put_contents(time(), serialize(new Mail($email, $subject, $content, $headers)));
    }

    public function list_and_sort_files_in_queue() {

        return scandir($this->queue_path);
        ;
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
