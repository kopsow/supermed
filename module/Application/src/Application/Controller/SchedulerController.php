<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Application\Form\PhysicianForm;

class SchedulerController extends AbstractActionController
{
    public $physicianTable;
    
    public function getPhysicianTable()
    {
        if (!$this->physicianTable) {
            $sm = $this->getServiceLocator();
            $this->physicianTable = $sm->get('Physician\Model\PhysicianTable');
        }
        return $this->physicianTable;
    }
    
    
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function addAction() 
    {
        
       
        $form = new physicianForm();
        return new ViewModel(array(
            'physicians' =>$form,
        ));
    }
    
   
}


