<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 */
class Kohana_Model_Validation extends Model {

    /**
     *
     * @var Validation 
     */
    private $validation;

    public function __construct() {

        $this->validation = Validation::factory((array) $this);

        foreach ($this->rules() as $key => $rules) {
            $this->validation->rules($key, $rules);
        }
    }

    public function validation() {
        return $this->validation;
    }

    public function check() {
        $this->validation = Validation::factory((array) $this);
        return $this->validation->check();
    }

    public function filters() {
        return array();
    }

    public function rules() {
        return array();
    }

}

?>
