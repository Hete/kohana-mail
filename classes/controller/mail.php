<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Controller_Mail extends Controller_Template {

    public $template = "mail/layout/template";
    public $head = 'mail/layout/head';

    public function action_index() {
        if($this->request->param('folder')) {
            $model = ORM::factory($this->request->param('folder'));
        } else {
            $model = ORM::factory('user');
        }
        $this->template->header = new View("mail/layout/header");
        $this->template->head = new View("mail/layout/head");
        $this->template->content = new View("mail/" . $this->request->param('folder') ."/". $this->request->param('view'));
        $this->template->content->model = $model;
        $this->template->footer = new View("mail/layout/footer");
    }
}
?>