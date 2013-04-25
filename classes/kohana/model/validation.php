<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Système pour valider des modèles qui ne sont pas ORM.
 * 
 * @package Mail
 * @category Model
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Model_Validation extends Model {

    /**
     * Internal validation object.
     * @var Validation 
     */
    private $_validation = NULL;

    public function __construct() {
        $this->_validation = Validation::factory((array) $this);

        foreach ($this->rules() as $field => $rules) {
            $this->_validation->rules($field, $rules);
        }
    }

    /**
     * 
     * @return Validation
     */
    public function validation() {
        return $this->_validation;
    }

    /**
     * Reload validation.  
     */
    private function reload() {
        $this->_validation = $this->_validation->copy((array) $this);
    }

    public function labels() {
        return array();
    }

    public function filters() {
        return array();
    }

    public function rules() {
        return array();
    }

    // Bindings
    public function check() {
        $this->reload();
        return $this->_validation->check();
    }

    public function errors($file = NULL, $translate = TRUE) {
        return $this->_validation->errors($file, $translate);
    }

    public function values(array $values, array $expected = NULL) {

        if ($expected === NULL) {
            $expected = array_keys($values);
        }

        foreach ($expected as $key => $column) {
            $this->$column = $values[$column];
        }

        return $this;
    }

}

?>
