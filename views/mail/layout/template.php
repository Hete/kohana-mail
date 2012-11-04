<?php defined('SYSPATH') or die('No direct script access.'); ?><!DOCTYPE html>
<html>
    <head><?php echo View::factory("mail/layout/template/head", array('mail' => $mail)) ?></head>
    <body style="margin-left: 30px; font-family: Arial;">
        <header><?php echo View::factory("mail/layout/template/header", array('mail' => $mail)) ?></header>
        <?php echo View::factory($mail->content, array('mail' => $mail)) ?>
        <footer><?php echo View::factory("mail/layout/template/footer", array('mail' => $mail)) ?></footer>
    </body>
</html>

