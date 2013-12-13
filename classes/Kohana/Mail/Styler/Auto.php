<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Basic styler with Text::auto_link and Text::auto_p.
 *
 * @package Mail
 * @category Stylers
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Styler_Auto extends Mail_Styler {

    /**
     * Applies paragraphs and links to its content.
     * 
     * @return string
     */
    public function style($body) {
        return Text::auto_link(Text::auto_p($body));
    }
}
