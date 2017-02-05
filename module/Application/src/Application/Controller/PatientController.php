<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PatientForm;

class PatientController extends AbstractActionController
{
    
    private $patientTable;
    
    public function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Patient\Model\PatientTable');
        }
        return $this->patientTable;
    }
    
    public function indexAction()
    {  
        $result = $this->getPatientTable()->fetchAll();
        
        
        
        return new ViewModel(array(
            'patients'      =>  $result
            
        ));
    }
    
    public function addAction()
    {
        $formPatient = new PatientForm();
        
        return new ViewModel(array(
           'formPatient'    =>  $formPatient 
        ));
    }
    
   
}


