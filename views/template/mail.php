<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html>
<html>
    <body>
        <header><?php echo View::factory("mail/header") ?></header>
        <section><?php echo $content ?></section>
        <footer><?php echo View::factory("mail/footer") ?></footer>
    </body>
</html>

