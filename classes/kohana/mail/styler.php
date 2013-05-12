<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Mail
 * @category Stylers
 * @author Guillaume Poirier-Morency <>
 */
abstract class Kohana_Mail_Styler {

    public static function factory($name) {

        $class = "Mail_Styler_$name";

        return new $class;
    }

    public abstract function content($content);

    public abstract function style($style);

    public abstract function render();

    public function __toString() {
        return $this->render();
    }

}

?>
