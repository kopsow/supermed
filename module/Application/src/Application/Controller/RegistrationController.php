<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PhysicianForm;
use Application\Form\PatientForm;
use Zend\Db\Adapter\Adapter;

use Application\Model\Registration;
use Application\Model\RegistrationTable;

class RegistrationController extends AbstractActionController
{
    
    public $schedulerTable;
    public $registrationTable;
    
    public function getSchedulerTable()
    {
        if (!$this->schedulerTable) {
            $sm = $this->getServiceLocator();
            $this->schedulerTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->schedulerTable;
    }
    public function getRegistrationTable()
    {
        if (!$this->registrationTable) {
            $sm = $this->getServiceLocator();
            $this->registrationTable = $sm->get('Registration\Model\RegistrationTable');
        }
        return $this->registrationTable;
    }
    
    public function getDaysTable()
    {
        if (!$this->daysTable) {
            $sm = $this->getServiceLocator();
            $this->daysTable = $sm->get('Days\Model\DaysTable');
        }
        return $this->daysTable;
    }
    public function indexAction()
    {
       
        $form = new PhysicianForm();
        $formPatient = new PatientForm();
        return new ViewModel(array(
            'physicians' => $form,
            'patient'    => $formPatient
        ));
    }
    
    public function addAction()
    {
        $requset = $this->getRequest();
        
        if ($requset->isPost())
        {
            $registration = new Registration();
            $data = array(
                'patient_id'        =>  $requset->getPost('patientSelect'),
                'physician_id'      =>  $requset->getPost('physicianScheduler'),
                'visit_date'        =>  $requset->getPost('dzien')." ".$requset->getPost('godzina'),
                'registration_date' =>  date('Y-m-d H:i:s')
            );
            $registration->exchangeArray($data);
            $this->getRegistrationTable()->saveRegistration($registration);
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
            $this->redirect()->toRoute('registration');
        } else {
            $this->redirect()->toRoute('registration');
        }
    }
    
    public function showAction()
    {
        $result = NULL;
        $request = $this->getRequest();
        $patientForm = new PatientForm();
        $physicianForm = new PhysicianForm();
        
        if ($request->isPost())
        {
            $patientId = $request->getPost('patientId');
            $physicianId = $request->getPost('physicianId');
            if (is_numeric($patientId) || is_numeric($physicianId))
            {
                $result = $this->getRegistrationTable()->showSelect($patientId,$physicianId);
                $patientForm->get('patientSelect')->setAttribute('value',$patientId);  
                $physicianForm->get('physicianScheduler')->setAttribute('value',$physicianId); 
            } else {
                 $result = $this->getRegistrationTable()->menagmentFetchAll();
            }
            
        } else {
            $result = $this->getRegistrationTable()->menagmentFetchAll();
        }
        
        return new ViewModel(array(
            'result'=>$result,
            'physicians' => $physicianForm,
            'patient'   =>  $patientForm
        ));
    }
    
       
   
  
}


