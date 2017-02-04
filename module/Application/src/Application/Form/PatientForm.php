<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class PatientForm extends Form {
    
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
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj pesel'
                )
        ));
        
        $this->add(array(
                'type'      =>  'password',
                'name'      =>  'password',
                'options'   =>  array(
                    'label' =>  'Imię',
                ),
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj hasło'
                )
        ));
        
        $this->add(array(
                'type'      =>  'date',
                'name'      =>  'birthday',
                'options'   =>  array(
                    'label' =>  'Data urodzenia',
                ),
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj datę urodzenia'
                )
        ));
        
              
        $this->add(array(
                'type'      =>  'checkbox',
                'name'      =>  'verified',
                'options'   =>  array(
                    'label' =>  'Zweryfikowany',
                ),
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    
                )
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'patientSelect',
               'options'    => array (   
                    'value_options' => $this->getPatient(), 
                    'empty_option'  => '--- Wybierz pacienta ---',
                                       
               ),
               'attributes' =>  array (
                   'id'     =>  'patientSelect',
                   'class'  =>  'form-control',
                   
               )
        ));
    }
    
    public function getPatient()
    {
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,CONCAT(name," ",surname) AS name FROM patient');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
}