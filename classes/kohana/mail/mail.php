<?php

/**
 * Model for mail.
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2012, Hète.ca
 */
class Kohana_Mail_Mail extends Model {

    public $content,
            $headers = array(
                'MIME-Version' => 1.0,
                'Content-type' => 'text/html; charset=UTF-8',
                    ),
            $email,
            $subject;

    public function __construct($email, $subject, View $content, array $headers = array()) {
        $this->email = $email;
        $this->subject = $subject;
        $this->headers += $headers;
        $this->content = $content;
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
     * Print headers in a 
     * @return string
     */
    public function render_headers() {
        $output = array();
        foreach ($this->headers as $key => $value) {
            $output += array("$key: $value");
        }
        return implode("\r\n", $output);
    }

    /**
     * Render the content of the mail.
     * @return string
     */
    public function render() {
        return $this->content->render();
    }

    /**
     * Alias for render().
     * @return type3
     */
    public function __toString() {
        return $this->render();
    }
    
    public function render() {
        
        return $this->content;
        
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
