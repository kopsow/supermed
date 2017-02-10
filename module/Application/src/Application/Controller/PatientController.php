<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PatientForm;
use Application\Model\Patient;
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
       
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $patient = new Patient();
            $data = array(
                'name'      =>  $request->getPost('name'),
                'surname'   =>  $request->getPost('surname'),
                'pesel'     =>  $request->getPost('pesel'),
                'password'  =>  $request->getPost('password'),
                'birthday'  =>  $request->getPost('birthday'),
                'tel'       =>  $request->getPost('tel'),
                'email'     =>  $request->getPost('email')
            );
            
            $patient->exchangeArray($data);
            $this->getPatientTable()->savePatient($patient);
            $this->redirect()->toRoute('patient');
            
        } else {
            
        }
        $formPatient = new PatientForm();
             
            return new ViewModel(array(
           'formPatient'    =>  $formPatient 
        ));
    }
    
    public function editAction()
    {
        
       $id   =   (int) $this->params()->fromRoute('id',0);
      
       if (!$id)
       {
           $this->redirect()->toRoute('home');
           
       } else {
           $patient = $this->getPatientTable()->getPatient($id);
           $data = array(
            'name'      =>  $patient->name,
            'surname'   =>  $patient->surname,
            'pesel'     =>  $patient->pesel,
            'birthday'  =>  $patient->birthday,
            'tel'       =>  $patient->tel,
            'email'     =>  $patient->email
        );
       }
        $form = new PatientForm();
        
        $form->setData($data);
        return new ViewModel(array(
            'form'  =>  $form,
            'id'    =>$id
        ));
    }
    
    public function saveAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            
            $patient = new Patient();
            
            $data = array(
                'id'        =>  $this->params()->fromRoute('id'),
                'name'      =>  $request->getPost('name'),
                'surname'   =>  $request->getPost('surname'),
                'pesel'     =>  $request->getPost('pesel'),
                'birthday'  =>  $request->getPost('birthday'),
                'tel'       =>  $request->getPost('tel'),
                'email'     =>  $request->getPost('email')
            );
            
            $patient->exchangeArray($data);
            $this->getPatientTable()->savePatient($patient);
            $this->redirect()->toRoute('patient');
        }else {
            $this->redirect()->toRoute('patient');
        }
               
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        
        if (!$id)
        {
            $this->redirect()->toRoute('home');
            
        } else {
            $this->getPatientTable()->deletePAtient($id);
            $this->redirect()->toRoute('patient');
        }
    }
    
   
}


