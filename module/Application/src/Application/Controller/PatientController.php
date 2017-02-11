<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PatientForm;
use Application\Model\Patient;
use Zend\Session\Container;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class PatientController extends AbstractActionController
{
   public function __construct() {
        $this->session = new Container('loginData');
    }
    private $patientTable;
    private $registrationTable;
    
    public function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Patient\Model\PatientTable');
        }
        return $this->patientTable;
    }
    public function getRegistrationTable()
    {
        if (!$this->registrationTable) {
            $sm = $this->getServiceLocator();
            $this->registrationTable = $sm->get('Registration\Model\RegistrationTable');
        }
        return $this->registrationTable;
    }
    public function indexAction()
    {  
        
        if ($this->session->role === 'patient')
        {
            $this->layout('layout/patient');
            $this->layout()->setVariable('patient_active', 'active');
        }
        if (!$this->session->id)
        {
            
            $this->redirect()->toRoute('autoryzacja',array('action'=>'patient'));
        }
            

        $result = $this->getRegistrationTable()->showRegistration($this->session->id);
        return new ViewModel(array(
            'patients'      =>  $result,
            'patient_id'    =>  $this->session->id
            
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
        $this->layout()->setVariable('edit_active', 'active');
       
        if ($this->session->role === 'patient')
        {
            $this->layout('layout/patient');
            $id = $this->session->id;
        } else {
            $id   =   (int) $this->params()->fromRoute('id',0);
        }
       
      
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
            'email'     =>  $patient->email,

                   
        );
       }
        $form = new PatientForm();
        if ($this->session->role === 'patient')
        {
         $form->get('email')->setAttribute('readonly','true');
         $form->get('pesel')->setAttribute('readonly','true');
        }
        $form->setData($data);
        return new ViewModel(array(
            'form'  =>  $form,
            'id'    =>  $id
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
            $this->getPatientTable()->deletePatient($id);
            $this->redirect()->toRoute('patient');
        }
    }
    
    public function cancelAction()
    {
        
        if ($this->session->role === 'patient')
        {
            $patientId         =    $this->session->id;
            $registrationId    =    $this->params()->fromRoute('id');
        }
       
        $registrationInfo = $this->getRegistrationTable()->showRegistration($patientId,$registrationId)->current();
        
        $this->getRegistrationTable()->deleteRegistrationPatient($patientId,$registrationId);
        $transport = $this->getServiceLocator()->get('mail.transport');
        $message = new \Zend\Mail\Message();       
        $message->addFrom("rejestracja@super-med.pl", "Super-Med")
        ->addTo($this->session->email)
        ->setSubject("Odwołanie wizyty");
        $message->setEncoding("UTF-8");
        $bodyHtml = ("Informujemy, że twoja wizyta w dniu:".$registrationInfo['visit_date']."<br /> Do lekarza: ".$registrationInfo['physician']."<br/> Została przez ciebie odwołana");
        $htmlPart = new MimePart($bodyHtml);
        $htmlPart->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlPart));
        $message->setBody($body);
        $transport->send($message);
        $this->redirect()->toRoute('patient');
    }
    
   
}


