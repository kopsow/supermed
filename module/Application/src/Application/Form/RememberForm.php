<?php

namespace Application\Form;

use Zend\Form\Form;


class RememberForm extends Form {
    
   
    
    public function __construct() {
        parent::__construct('users');       
         $this->setAttribute('action', '/pacjent/rememeber');
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'email',                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj login (adres email)'
                )
        ));
        
        
        
      
    }
    
    
}