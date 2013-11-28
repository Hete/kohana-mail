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
        Mail_Sender::factory()
                ->style("html{background:blue}")
                ->send('foo@bar.com', 'Foo subject', 'mail/test');
    }

}
