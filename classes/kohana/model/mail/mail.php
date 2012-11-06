<?php

/**
 * Model for mail.
 * 
 * @package Mail
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2012, Hète.ca
 */
class Kohana_Model_Mail_Mail extends Model {

    public $content,
            $model,
            $headers = array(
                'MIME-Version' => 1.0,
                'Content-type' => 'text/html; charset=UTF-8',
                    ),
            $email,
            $subject,
            $receiver;

    /**
     * 
     * @param type $email
     * @param type $subject
     * @param View $content
     * @param array $headers
     */
    public function __construct(Model_Auth_User $receiver, $subject, $content, ORM $model, array $headers = array()) {
        $this->subject = $subject;
        $this->headers += $headers;
        $this->content = $content;
        $this->model = $model;
        $this->receiver = $receiver;
    }

    /**
     * Headers access method. Same as param.
     * @param string $key
     * @param string $value
     * @return type
     */
    public function headers($key = NULL, $value = NULL) {
        if ($key === NULL) {
            return $this->_headers;
        } else if ($value === NULL) {
            return $this->_headers[$key];
        } else {
            $this->_headers[$key] = $value;
            return $this;
        }
    }

    /**
     * Return basic headers.  
     * @return array
     */
    protected function base_headers() {
        return array(
            'To' => $this->receiver->username . " <" . $this->receiver->email . ">",
            'From' => Mail_Sender::instance()->config("from_name") . " <" . Mail_Sender::instance()->config("from") . ">",
        );
    }

    /**
     * Print headers in a 
     * @return string
     */
    public function render_headers() {
        $output = array();
        foreach ($this->headers + $this->base_headers() as $key => $value) {
            $output += array("$key: $value");
        }
        return implode("\r\n", $output);
    }

    /**
     * Render the content of the mail.
     * @return string
     */
    public function render() {
        return View::factory("mail/layout/template", array('mail' => $this))->render();
    }

    /**
     * Alias for render().
     * @return type3
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Send the mail to its subject.
     * @return type
     */
    public function send() {
        return mail($this->email, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $this->render(), $this->render_headers());
    }

}

?>
