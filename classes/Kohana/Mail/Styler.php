<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Definition of a mail styler.
 * 
 * A styler takes a content and a style and applies the style on the content on
 * rendering.
 * 
 * @package Mail
 * @category Stylers
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Mail_Styler {

    public static $default = 'Plain';

    public static function factory($name) {

        $class = "Mail_Styler_$name";

        return new $class;
    }
    
    /**
     *
     * @return string styled body
     */
    public abstract function style($body);

}
