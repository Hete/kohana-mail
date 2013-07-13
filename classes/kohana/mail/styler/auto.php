<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Auto styling engine.
 * 
 * Applies a Text::auto_p and a Text::auto_link to its content.
 * 
 * @package   Mail
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Styler_Auto extends Mail_Styler {

    private $content;

    public function content($content) {
        $this->content = $content;
    }

    public function style($style) {
        throw new Kohana_Exception('Style is automatically generated.');
    }

    public function render() {
        $output = Text::auto_p($this->content);
        $output = Text::auto_link($output);
        return $output;
    }

}

?>
