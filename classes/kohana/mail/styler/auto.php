<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @package Mail
 * @category Stylers
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Styler_Auto extends Mail_Styler {

    private $content;

    public function content($content) {
        $this->content = $content;
        
        return $this;
    }

    public function style($style) {
        throw new Kohana_Exception('Style is automatically generated.');
    }

    /**
     * Applies paragraphs and links to its content.
     * 
     * @return string
     */
    public function render() {
        return Text::auto_link(Text::auto_p($this->content));
    }

}
