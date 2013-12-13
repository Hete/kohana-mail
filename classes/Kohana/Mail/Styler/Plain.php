<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Styler_Plain extends Mail_Styler {

    public function style($body) {
        return $body;
    }

}
