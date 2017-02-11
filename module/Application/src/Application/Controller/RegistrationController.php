<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PhysicianForm;
use Application\Form\PatientForm;
use Zend\Db\Adapter\Adapter;

use Application\Model\Registration;
use Application\Model\RegistrationTable;

use Zend\Mail\Message;
use Zend\Mime;
use Zend\Session\Container;

class RegistrationController extends AbstractActionController
{
    
    public $schedulerTable;
    public $registrationTable;
    public $physicianTable;
    public $patientTable;
    public function __construct() {
        $this->session = new Container('loginData');
        
    }
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
    
    public function getPhysicianTable()
    {
        if (!$this->physicianTable) {
            $sm = $this->getServiceLocator();
            $this->physicianTable = $sm->get('Physician\Model\PhysicianTable');
        }
        return $this->physicianTable;
    }
    
    public function getDaysTable()
    {
        if (!$this->daysTable) {
            $sm = $this->getServiceLocator();
            $this->daysTable = $sm->get('Days\Model\DaysTable');
        }
        return $this->daysTable;
    }
    
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
       if ($this->session->role === 'patient')
        {
            $this->layout('layout/patient');
            $this->layout()->setVariable('registration_active', 'active');
        }
        $form = new PhysicianForm();
        $formPatient = new PatientForm();
        return new ViewModel(array(
            'physicians' => $form,
            'patient'    => $formPatient,
            'lekarze'    => $this->getPhysicianTable()->fetchAll(),
            'dni'  => $this->getSchedulerTable()->fetchAll(),
            'godziny'  => $this->getSchedulerTable()->fetchAll(),
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
    
    public function cancelAction()
    {
        
        
       $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
             return $this->redirect()->toRoute('registration');
         } else {
               $wynik = $this->getRegistrationTable()->getRegistration($id);
               $day = date('Y-m-d',  strtotime($wynik->visit_date));
               $hour = date('H:i',  strtotime($wynik->visit_date));
               $physician = $this->getPhysicianTable()->getPhysician($wynik->physician_id);
         }
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');
       
       
            // Email content
            $viewContent = new \Zend\View\Model\ViewModel(
                array(
                    'physician'    => $physician->name." ".$physician->surname,
                    'day'          => $day,
                    'hour'         => $hour,
            ));
            $viewContent->setTemplate('email/cancel'); // set in module.config.php
            $content = $renderer->render($viewContent);

            // Email layout
            $viewLayout = new \Zend\View\Model\ViewModel(array('content' => $content));
            $viewLayout->setTemplate('email/layout'); // set in module.config.php

            // Email
            
            $html =  new \Zend\Mime\Part($renderer->render($viewLayout));
            $html->type = 'text/html';
            $body = new \Zend\Mime\Message();
            $body->setParts(array($html));
        
        $transport = $this->getServiceLocator()->get('mail.transport');
        
        $objEmail = new \Zend\Mail\Message();
        
        
        $objEmail->setBody($body);
        $objEmail->setFrom('rejestracja@super-med.pl', 'Super-Med');
        $patient = $this->getPatientTable()->getPatient($wynik->patient_id);
        $objEmail->addTo($patient->email, $patient->name." ".$patient->surname);
        $objEmail->setSubject('Informacja o anluowaniu wizyty');
        $transport->send($objEmail); 
        return '';
    }
    
    
    public function stepOneAction()
    {
        $request = $this->request;
        
        if ($request->isPost())
        {
            
        } else {
            $this->redirect()->toRoute('registration');
        }
    }
       
   
  
}


