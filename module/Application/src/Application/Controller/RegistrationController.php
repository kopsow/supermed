<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PhysicianForm;
use Application\Form\PatientForm;
use Zend\Db\Adapter\Adapter;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Application\Model\Registration;
use Application\Model\RegistrationTable;


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
        $request = $this->getRequest();
        if ($this->session->role === 'patient')
        {
            $this->layout('layout/patient');
            $this->layout()->setVariable('registration_active', 'active');
        }elseif ($this->session->role === 'physician') {
            $this->layout('layout/physician');
            $this->layout()->setVariable('registration_active', 'active');
        }elseif ($this->session->role === 'register')
        {
            $this->layout('layout/register');
            $this->layout()->setVariable('registration_active', 'active');
        }        
        
        if($request->isPost())
        {
           
           $id = (int) $request->getPost('id');
           
           
           if ($id)
           {
               $result = $this->getSchedulerTable()->getSchedulerPhysician($id,date('m'));
               
               $view = new ViewModel(array(
                   'dni'    =>  $result,
                   'id'     =>  $id
               ));
           }
            
            
        } else {            
                
        $form = new PhysicianForm();
        $formPatient = new PatientForm();
        
         $view = new ViewModel(array(
            'physicians' => $form,
            'patient'    => $formPatient,
            'lekarze'    => $this->getPhysicianTable()->fetchAll(),
            'dni'  => $this->getSchedulerTable()->fetchAll(),
            'godziny'  => $this->getSchedulerTable()->fetchAll(),
        ));
        }        
        /*return new ViewModel(array(
            'physicians' => $form,
            'patient'    => $formPatient,
            'lekarze'    => $this->getPhysicianTable()->fetchAll(),
            'dni'  => $this->getSchedulerTable()->fetchAll(),
            'godziny'  => $this->getSchedulerTable()->fetchAll(),
        ));*/
        
         
         return $view;
        
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
        if ($this->session->role != 'register' && $this->session->role != 'admin')
        {
            $this->redirect()->toRoute('home');
        } elseif ($this->session->role === 'register') {
           $this->layout('layout/register');
           $this->layout()->setVariable('registrationList_active', 'active');
        }
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
    
    public function oneAction()
    {
        $this->layout('layout/patient');
        $this->layout()->setVariable('registration_active', 'active');
        
        $id = (int) $this->params()->fromRoute('param');
        $this->session->idPhysician = $id;
        $resultDay = $this->getSchedulerTable()->getSchedulerPhysician($id,date('m'));
        
        return new ViewModel(array(
            'days'  =>  $resultDay
        ));
    }
    
    public function twoAction()
    {
        $this->layout('layout/patient');
        $this->layout()->setVariable('registration_active', 'active');
            
        $day = $this->params()->fromRoute('param');
        $this->session->visit_date = $day;
        
            $physicianId = $this->session->idPhysician;
            $visitDate = trim($this->session->visit_date);
            $result = $this->getSchedulerTable()->getSchedulerPhysicianHours($physicianId,$visitDate);
           
            $time_start = date('H:i',  strtotime($result->date_start));
            $time_end = date('H:i',  strtotime($result->date_end));

            $godzinyPrzyjec = array();
            $godzinyPrzyjec[]=$time_start;

            while ($time_start != $time_end)
            {
               $time_start = date('H:i',  strtotime($time_start.'+15 minutes'));
               $godzinyPrzyjec[]=$time_start;
            }
            $busyHours = $this->getRegistrationTable()->busyHours($physicianId,$visitDate);
            $busy = array();
            foreach ($busyHours as $hour)
            {
                $busy[]=date('H:i',  strtotime($hour->visit_date));
            }
            return new ViewModel(array(
               'physician'     =>  $this->getPhysicianTable()->getPhysician($this->session->idPhysician),
               'day'           =>  $this->session->visit_date,
               'hours'         =>  $godzinyPrzyjec ,
               'busy'          => $busy
            ));
    }
       
    public function threeAction()
    {
        $this->layout('layout/patient');
        $this->layout()->setVariable('registration_active', 'active');
        
        $patientId      =   $this->session->id;
        $physicianId    =   $this->session->idPhysician;
        $visit_date     =   $this->session->visit_date;
        $visit_time     =   $this->params()->fromRoute('param');
        
        $data = array (
            'patient_id'        =>  $patientId,
            'physician_id'      =>  $physicianId,
            'visit_date'        =>  date('Y-m-d H:i:s',strtotime($visit_date." ".$visit_time)),
            'registration_date' =>  date('Y-m-d H:s'),
        );
  
        $physicianInfo = $this->getPhysicianTable()->getPhysician($physicianId);
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        $registration = new Registration();
        $registration->exchangeArray($data);
        $this->getRegistrationTable()->saveRegistration($registration);
        $body = 'Witaj! '.$this->session->name.'<br/>'
                . 'Potwierdzamy dokonanie rezerwacji do <br/>'
                . 'Lekarza: '.$physicianInfo->name." ".$physicianInfo->surname.'<br />'
                . 'W dniu: '.$visit_date.'<br />'
                . 'Na godzinę: '.$visit_time;
        $this->sendMail($this->session->email, 'Rejestracja wizyty', $body);
        $this->redirect()->toRoute('patient');
        
    }
  
    private function sendMail($to,$subject,$body)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');
        $message = new \Zend\Mail\Message();       
        $message->addFrom("rejestracja@super-med.pl", "Super-Med")
        ->addTo($to)
        ->setSubject($subject);
        $message->setEncoding("UTF-8");
        $bodyHtml = ($body);
        $htmlPart = new MimePart($bodyHtml);
        $htmlPart->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlPart));
        $message->setBody($body);
        $transport->send($message);
        $this->redirect()->toRoute('patient');
    }
}


