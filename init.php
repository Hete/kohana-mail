<?php

defined('SYSPATH') or die('No direct script access.');

// Tests for default configuration
if (!is_writable(Mail_Sender::instance()->config("async.path")))
    throw new Kohana_Exception("Folder :folder is not writeable.", array(":folder" => Mail_Sender::instance()->config("async.path")));

if (Mail_Sender::instance()->config("async.salt") === NULL)
    throw new Kohana_Exception("Salt is not defined.");
?>
