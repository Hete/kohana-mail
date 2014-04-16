<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Fake sender for testing application without sending real mails.
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD 3 clauses
 */
class Kohana_Mail_Sender_Mock extends Mail_Sender {

    protected function _send(array $to) {

        return (bool) $to;
    }

}
