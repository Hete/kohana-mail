<?php

defined('SYSPATH') or die('No direct script access.');


if ($this->_config['async']) {
    if (!is_writable($this->_config['queue_path']))
        throw new Kohana_Exception("Folder :folder is not writeable.", array(":folder" => $this->_config['queue_path']));

    if ($this->_config['salt'] === NULL)
        throw new Kohana_Exception("Salt is not defined.");
}
?>
