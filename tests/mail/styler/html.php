<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 * 
 * @package Mail
 * @category Tests
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Mail_Styler_HTML_Test extends Unittest_TestCase {

    public function test_css() {

        $styler = Mail_Styler::factory('HTML')
                ->style('html{background:blue}');

        Mail_Sender::factory()
                ->styler($styler)
                ->send('foo@bar.com', 'Foo subject', 'mail/test');
    }

}

?>
