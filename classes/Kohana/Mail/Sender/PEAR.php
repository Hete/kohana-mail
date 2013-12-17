<?php

defined('SYSPATH') or die('No direct script access.');

require_once 'Mail.php';

/**
 * PEAR wrapper for the Mail module.
 *
 * PEAR must be included in your PHP path.
 *
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
abstract class Kohana_Mail_Sender_PEAR extends Mail_Sender {

}
