<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers for mail.
 * 
 * @package Mail
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail extends Model_Mail {
    /**
     * Basic styles
     */

    const STYLE = "font-family: Arial;";

    /**
     * Append an attributes without overriding existing attributes. class is
     * used by default.
     * 
     * @param array $attributes array of key-value pairs of attributes
     * @param type $value value to append
     * @param type $name name of the attribute   
     */
    public static function add_attribute(&$attributes, $value, $name = "class") {
        $attributes[$name] = Arr::get($attributes, $name, "") . " " . $value;
    }

    public static function h1($text, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . "font-size: 20px;", "style");

        return "<h1 " . HTML::attributes($attributes) . ">" . $text . "</h1>";
    }

    public static function h2($text, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . "font-size: 18px;", "style");

        return "<h2 " . HTML::attributes($attributes) . ">" . $text . "</h2>";
    }

    public static function h3($text, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . "font-size: 16px;", "style");


        return "<h3 " . HTML::attributes($attributes) . ">" . $text . "</h3>";
    }

    public static function p($text, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . "font-size: 16px;", "style");

        return "<p " . HTML::attributes($attributes) . ">" . $text . "</p>";
    }

    public static function pre($text, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . "font-size: 16px;", "style");

        return "<pre " . HTML::attributes($attributes) . ">" . $text . "</pre>";
    }

    public static function td_open($colspan = 12, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . " border:none; padding-left: 20px; text-align: left", "style");
        $attributes["colspan"] = $colspan;

        return "<th " . HTML::attributes($attributes) . ">";
    }

    public static function th_open($colspan = 12, array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE . " border:none; padding-left: 20px; text-align: left", "style");
        $attributes["colspan"] = $colspan;

        return "<th " . HTML::attributes($attributes) . ">";
    }

    public static function tr_open(array $attributes = NULL) {

        static::add_attribute($attributes, static::STYLE, "style");

        return "<tr " . HTML::attributes($attributes) . ">";
    }

    public static function td_close() {
        return "</td>";
    }

    public static function th_close() {
        return "</th>";
    }

    public static function tr_close() {
        return "</tr>";
    }

}

?>
