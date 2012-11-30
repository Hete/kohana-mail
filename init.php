<?php

defined('SYSPATH') or die('No direct script access.');

// Tests for default configuration

if (!is_writable(Kohana::$config->load("mail.default.async.path")))
    throw new Kohana_Exception("Folder :folder is not writeable.", array(":folder" => Kohana::$config->load("mail.default.async.path")));

if (Kohana::$config->load("mail.default.async.salt") === NULL)
    throw new Kohana_Exception("Salt is not defined.");
?>
