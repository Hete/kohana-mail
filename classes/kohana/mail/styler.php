<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Styling engine.
 * 
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Styler {

    /**
     * Default styler.
     * 
     * @var string 
     */
    public static $default = 'HTML';

    public static function factory($name = NULL) {

        if ($name === NULL) {
            $name = Mail_Styler::$default;
        }

        $class = "Mail_Styler_$name";

        return new $class;
    }

    /**
     * Update or get content of this styler.
     * 
     * @return \Mail_Styler
     */
    public abstract function content($content);

    /**
     * Update or get style for this styler.
     * 
     * @return \Mail_Styler
     */
    public abstract function style($style);

    /**
     * Renders the style over the content.
     */
    public abstract function render();

    public function __toString() {
        return $this->render();
    }

}

?>
