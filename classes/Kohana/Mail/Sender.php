<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Mail sender.
 * 
 * @package   Mail
 * @category  Senders
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Sender {

    /**
     * Default sender. 
     * 
     * @var string 
     */
    public static $default = 'Mail';

    /**
     * Return an instance of the specified sender.
     * 
     * @return Mail_Sender 
     */
    public static function factory($name = NULL) {

        if ($name === NULL) {
            $name = Mail_Sender::$default;
        }

        $class = "Mail_Sender_$name";

        return new $class();
    }

    public function __construct() {
        $this->headers = Kohana::$config->load('mail.headers');
        $this->styler = Mail_Styler::factory();
    }

    /**
     * Getter-setter for mail headers.
     * 
     * @param string $key
     * @param variant $value
     * @return variant
     */
    public function headers($key = NULL, $value = NULL) {

        if ($key === NULL) {
            return $this->headers;
        }

        if ($value === NULL) {
            return Arr::get($this->headers, $key);
        }

        $this->headers[$key] = (string) $value;

        return $this;
    }

    public function styler(Mail_Styler $styler = NULL) {
    
        if($styler === NULL) {
            return $this->styler;
        }

        $this->styler = $styler;

        return $this;
    }

    public function sender($sender = NULL) {
        return $this->headers('Sender', $sender);
    }

    /**
     * 
     * @param string $cc
     * @return \Mail_Sender
     */
    public function cc($cc = NULL) {
        return $this->headers('Cc', $cc);
    }

    /**
     * 
     * @param string $bcc
     * @return \Mail_Sender
     */
    public function bcc($bcc = NULL) {
        return $this->headers('Bcc', $bcc);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function from($from = NULL) {
        return $this->headers('From', $from);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function resent_from($resent_from = NULL) {
        return $this->headers('Resent-From', $resent_from);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function to($to = NULL) {
        return $this->headers('To', $to);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function resent_to($resent_to = NULL) {
        return $this->headers('Resent-To', $resent_to);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function subject($subject = NULL) {
        return $this->headers('Subject', $subject);
    }
    
    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function resent_subject($resent_subject = NULL) {
        return $this->headers('Resent-Subject', $resent_subject);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function return_path($return_path = NULL) {
        return $this->headers('Return-Path', $return_path);
    }

    /**
     * 
     * @param string $reply_to
     * @return \Mail_Sender
     */
    public function reply_to($reply_to = NULL) {
        return $this->headers('Reply-To', $reply_to);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function mail_reply_to($mail_reply_to = NULL) {
        return $this->headers('Mail-Reply-To', $mail_reply_to);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function mail_followup_to($mail_followup_to = NULL) {
        return $this->headers('Mail-Followup-To', $mail_followup_to);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function message_id($message_id = NULL) {
        return $this->headers('Message-ID', $message_id);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function in_reply_to($in_reply_to = NULL) {
        return $this->headers('In-Reply-To', $in_reply_to);
    }

    /**
     * 
     * @param string $from
     * @return \Mail_Sender
     */
    public function references($references = NULL) {
        return $this->headers('References', $in_reply_to);
    }
 
    /**
     * Process a receiver from the $receivers parameters.
     */   
    protected function process_receiver($index, $email) {

        // $key is an email, so $email is a name
        if (is_string($index) && Valid::email($index)) {
            return array($index, $email);
        }

        return array($email, NULL);
    }

    /**
     * Process an email and name into a valid RFC address list item.
     */
    protected function process_email($email, $name = NULL) {

        if ($name === NULL) {
            return $email;
        }

        return mb_encode_mimeheader($name) . ' ' . "<$email>";
    }
   
    /**
     * Process the subject of the maik
     *
     * Override this method if you want to have customized subjects.
     *
     * By default, this method translates the subject substituting
     * :name and :email.
     *
     * @param  string  $subject
     * @param  string  $email
     * @param  variant $name
     * @return string          a processed subject.
     */
    protected function process_subject($subject, $email = NULL, $name = NULL) {
        return __($subject, array(':email' => $email, ':name' => $name));
    }

    /**
     * Process the body of the mail.
     *
     * It applies the styler to the body of the mail. The default styler
     * does absolutely nothing unless it's changed.
     * 
     * Override this class in your own application if you want to load
     * your view or process stuff in your body.
     *
     * @param  variant $body
     * @param  string  $email
     * @param  variant $name
     * @return string         the processed body.
     */
    protected function process_body($body, $email = NULL, $name = NULL) {
        return $this->styler->style($body);
    }

    /**
     * Process mail headers.
     *
     * This will add the To header.
     * 
     * @param  array   $headers 
     * @param  string  $email
     * @param  variant $name     
     * @return array             the processed headers.
     */
    protected function process_headers(array $headers, $email = NULL, $name = NULL) {
       
        if (!array_key_exists('Content-Type', $headers)) {
            $headers['Content-Type'] = $this->styler->content_type;  
        }

        if (!array_key_exists('To', $headers) AND $email !== NULL) {
            $headers['To'] = $this->process_email($email, $name);
        }
         
        if (!array_key_exists('Message-ID', $headers)) {
            $headers['Message-ID'] = sha1(uniqid(NULL, TRUE));
        }

        return $headers;
    }

    /**
     * Append an attachment to this mail.
     */
    public function attachment($attachment, $type) {

        $this->attachments[] = array(
            'attachment' => $attachment,
            'type' => $type
        );

        return $this;
    }

    /**
     * Send an email to its receivers.
     * 
     * When fetching an ORM, it is somewhat useful to do $model->as_array('email', 'name').
     *
     * @param  variant $receiver a list of emails or an associative array of email to name.
     * @param  string  $subject  is the subject of the mail.
     * @param  variant $body     a string containing the body or preferably a View.
     * @param  array   $headers  are additionnal headers to override pre-configured
     * @param  boolean $once     send all mails at once or process each mail one by one.
     * ones in mail.headers and internal sender headers.
     * @return array   an array of states when sending; keys match $receivers keys.
     */
    public function send($receivers, $subject, $body, array $headers = array(), $once = FALSE) {
       
        // Check if the receiver is a traversable structure
        if (!Arr::is_array($receivers)) {
            $receivers = array($receivers);
        }

        if ($once === TRUE) {

            $subject = $this->process_subject($subject);
             
            $body = $this->process_body($body);

            $headers = Arr::merge($this->headers(), $headers);

            $headers = $this->process_headers($headers);

            $emails = array();

            foreach ($receivers as $index => $email) {
                list($email, $name) = $this->process_receiver($index, $email);
                $emails[] = $this->process_email($email, $name);
            }

            return $this->_send($emails, $subject, $body, $headers, $attachments);
        }
        
        foreach ($receivers as $index => $email) {
            
            list($email, $name) = $this->process_receiver($index, $email);

            $subject = $this->process_subject($subject, $email, $name);
             
            $body = $this->process_body($body, $email, $name);

            $headers = Arr::merge($this->headers(), $headers);

            $headers = $this->process_headers($headers, $email, $name);

            $email = $this->process_email($email, $name);

            // replace receiver by the result of the sent
            $receivers[$index] = $this->_send($email, $subject, $body, $headers, $attachments;
        }

        return $receivers;
    }

    /**
     * Implemented by the sender.
     *
     * @param  string  email       email
     * @param  string  subject     subject
     * @param  string  body        mail's body
     * @param  array   headers     headers
     * @param  array   attachments an array of mail attachments
     * @return boolean TRUE if sending is successful, FALSE otherwise.
     */
    protected abstract function _send($email, $subject, $body, array $headers, array $attachments);
}
