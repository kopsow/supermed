<?php

namespace Application\Form;

use Zend\Form\Form;


class LoginForm extends Form {
    
   
    
    public function __construct() {
        parent::__construct('users');       
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'login',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj login (adres email)'
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
        
      
    }
    
    
}