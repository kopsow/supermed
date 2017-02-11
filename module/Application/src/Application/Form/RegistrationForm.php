<?php

namespace Application\Form;

use Zend\Form\Form;


class RegistrationForm extends Form {
    
    
    
    public function __construct() {
        parent::__construct('physician');      
        
        $this->setAttribute('action', '/pacjent/new');
        $this->setAttribute('method', 'post');
        
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
                'type'      =>  'text',
                'name'      =>  'name',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj imię'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'pesel',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj swój PESEL'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'email',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj adres email'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'tel',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj telefon'
                )
        ));
        
        $this->add(array(
                'type'      =>  'text',
                'name'      =>  'password',
                
                'attributes'=>  array(
                    'class'         =>  'form-control',
                    'placeholder'   =>  'Podaj hasło'
                )
        ));
    }
}