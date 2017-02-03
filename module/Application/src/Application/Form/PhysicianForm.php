<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class PhysicianForm extends Form {
    
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
    
    public function __construct() {
        parent::__construct('physician');
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'name',
                'options'   =>  array(
                    'label' =>  'Imię',
                ),
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj imię'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'surname',
                'options'   =>  array(
                    'label' =>  'Nazwisko',
                ),
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj nazwisko'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'pesel',
                'options'   =>  array(
                    'label' =>  'PESEL',
                ),
                'attributes'=>  array(
                    'class' =>  'form-control',
                    'placeholder'   =>  'Podaj numer PESEL'
                )
        ));
        $this->add(array(
                'type'      =>  'password',
                'name'      =>  'password',
                'options'   =>  array(
                    'label' =>  'Hasło',
                ),
                'attributes'=>  array(
                    'class' =>  'form-control'
                    
                )
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'physician',
               'options'    => array (
                    'label'     => 'Wybór lekarza',
                    'value_options' => $this->getPhysician(),
               ),
               'attributes' =>  array (
                   'id'     =>  'sel1',
                   'class'  =>  'form-control',
               )
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'physicianScheduler',
               'options'    => array (   
                    'value_options' => $this->getPhysician(), 
                    'empty_option'  => '--- Wybierz lekarza ---',
                                       
               ),
               'attributes' =>  array (
                   'id'     =>  'sel1',
                   'class'  =>  'form-control',
                   
               )
        ));
        $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Zapisz',
                 'id' => 'submitbutton',
             ),
         ));
    }
    
    private function getPhysician() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,CONCAT(name," ",surname) AS name FROM physician');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
}
