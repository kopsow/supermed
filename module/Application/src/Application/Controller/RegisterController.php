<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class RegisterController extends AbstractActionController
{
    
    public function __construct() {
        $this->session = new \Zend\Session\Container('loginData');
    }
    public $scheduleTable;
    public $registrationTable;
    public $patientTable;
    
    public function getSchedulerTable()
    {
        if (!$this->scheduleTable) {
            $sm = $this->getServiceLocator();
            $this->scheduleTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->scheduleTable;
    }
    
    public function getRegistrationTable()
    {
        if (!$this->registrationTable) {
            $sm = $this->getServiceLocator();
            $this->registrationTable = $sm->get('Registration\Model\RegistrationTable');
        }
        return $this->registrationTable;
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
        if (!$this->session->login)
        {
            $this->redirect()->toRoute('autoryzacja',array('action'=>'register'));
        } else {
            $this->layout('layout/register');
            $this->layout()->setVariable('list_active', 'active');
        }
                
    }
    
    public function cancelAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        
        if ($this->session->role === 'register')
        {
            
            
            $registrationInfo   =   $this->getRegistrationTable()->showRegistration(null,$id,null)->current();
            $patientInfo        =   $this->getPatientTable()->getPatient($registrationInfo['patient_id']);
            //$this->getRegistrationTable()->deleteRegistraion($id);
            $transport = $this->getServiceLocator()->get('mail.transport');
            $message = new \Zend\Mail\Message();       
            $message->addFrom("rejestracja@super-med.pl", "Super-Med")
            ->addTo($patientInfo->email)
            ->setSubject("Odwołanie wizyty");
            $message->setEncoding("UTF-8");
            $bodyHtml = ("Informujemy, że twoja wizyta w dniu:"
                    . "".$registrationInfo['visit_date']."<br />"
                    . " Do lekarza: ".$registrationInfo['physician'].""
                    . "<br/> Została odwołana przez rejestratorkę");
            $htmlPart = new MimePart($bodyHtml);
            $htmlPart->type = "text/html";
            $body = new MimeMessage();
            $body->setParts(array($htmlPart));
            $message->setBody($body);
            $transport->send($message);
            $this->redirect()->toRoute('register');
        }
    }
    
   
}


