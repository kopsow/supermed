<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class UsersForm extends Form {
     private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
   
    
    public function __construct() {
        parent::__construct('users');       
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'name',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj imię'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'surname',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj nazwisko'
                )
        ));
        
        $this->add(array(
                'type'      =>  'email',
                'name'      =>  'email',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj email'
                )
        ));
        
        $this->add(array(
                'type'      =>  'password',
                'name'      =>  'password',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj hasło'
                )
        ));
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'roleSelect',
               'options'    => array (   
                    'value_options' => $this->getRole(), 
                    'empty_option'  => '--- Wybierz role ---',
                                       
               ),
               'attributes' =>  array (
                   'id'     =>  'roleelect',
                   'class'  =>  'form-control',
                   
               )
        ));
    }
    
    public function getRole()
    {
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,name FROM role');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            
            $selectData[$res['id']] =   $res['name'];
        }
       
        return $selectData;
    }
}