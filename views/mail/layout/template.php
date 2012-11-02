<?php defined('SYSPATH') or die('No direct script access.'); ?><!DOCTYPE html>
<html>
    <head><?php echo View::factory("mail/layout/template/head") ?></head>
    <body style="margin-left: 30px; font-family: Arial;">
        <?php echo View::factory("mail/layout/template/header") ?>
        <?php echo $content ?>
        <?php echo View::factory("mail/layout/template/footer") ?>
    </body>
</html>

