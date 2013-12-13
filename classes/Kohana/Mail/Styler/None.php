<?php

class Kohana_Mail_Styler_None extends Mail_Styler {

    public function style($body) {
        return $body;
    }

}
