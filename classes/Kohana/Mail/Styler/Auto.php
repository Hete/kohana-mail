<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Basic styler with Text::auto_link and Text::auto_p.
 *
 * The given mail body must be a non-HTML content.
 * 
 * @package   Mail
 * @category  Stylers
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Styler_Auto extends Mail_Styler {

    public $content_type = 'text/html';

    /**
     * Applies paragraphs and links to its content.
     * 
     * @return string 
     */
    public function style($body) {

        if (Kohana::$config->load('mail.styler.Auto.paragraph')) {
            $body = Text::auto_p($body);
        }

        if (Kohana::$config->load('mail.styler.Auto.link')) {
            $body = Text::auto_link($body);
        }

        return '<html><body>' . $body . '</body></html>';
    }
}
