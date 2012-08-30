<?php

class Model_User extends Model_Auth_User {

    public function nom_complet() {

        return $this->email;
    }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
