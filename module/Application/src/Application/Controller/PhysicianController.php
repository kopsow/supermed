<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class PhysicianController extends AbstractActionController
{
    

    
    public $physicianTable;
    public $patientTable;
    public $holidaysTable;
    public $registrationTable;
    public $schedulerTable;
    
    
    public function __construct() {
        
        $this->session = new Container('loginData');
    }
    public function getPhysicianTable()
    {
        if (!$this->physicianTable) {
            $sm = $this->getServiceLocator();
            $this->physicianTable = $sm->get('Physician\Model\PhysicianTable');
        }
        return $this->physicianTable;
    }
    
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
    
    public function getSchedulerTable()
    {
        if (!$this->schedulerTable) {
            $sm = $this->getServiceLocator();
            $this->schedulerTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->schedulerTable;
    }
    
    public function getHolidaysTable()
    {
        if (!$this->holidaysTable) {
            $sm = $this->getServiceLocator();
            $this->holidaysTable = $sm->get('HolidaysModel\HolidaysTable');
        }
        return $this->holidaysTable;
    }
    
    public function indexAction()
    {
        $listPatient = null; 
        
       if (!$this->session->login)
       {
          $this->redirect()->toRoute('autoryzacja',array('action'=>'physician'));
       } elseif ($this->session->role === 'physician') {
            $listPatient = $this->getRegistrationTable()->showRegistration(null,null,$this->session->id);
       }
       if (!$this->session || $this->session->role ==='patient')
       {
           $this->redirect()->toRoute('autoryzacja',array('action'=>'physician'));
       }
       if ($this->session->role === 'physician')
       {
           $this->layout('layout/physician');
           $this->layout()->setVariable('list_active', 'active');
       }
        return new ViewModel(array(
            'patients' =>  $listPatient,
        ));
    }
    
    public function addAction() 
    {
        $form = new \Application\Form\PhysicianForm();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $physician = new \Application\Model\Physician();
            
            $data = array(
                'name'      =>  $request->getPost('name'),
                'surname'   =>  $request->getPost('surname'),
                'pesel'     =>  $request->getPost('pesel'),
                'password'  =>  $request->getPost('password')
            );
            
            $physician->exchangeArray($data);
            $this->getPhysicianTable()->savePhysician($physician);
            $this->redirect()->toRoute('physician');
        } 
        return new ViewModel(array(
            'form'  => $form
        ));
    }
    
    public function deleteAction()
    {
         $id   =   (int) $this->params()->fromRoute('id');
         if (!$id){
             $this->redirect()->toRoute('home');
         } else {
             $this->getPhysicianTable()->deletePhysician($id);
             $this->redirect()->toRoute('physician');
         }
         echo $id;
    }
   
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        
        if (!$id)
        {
            $this->redirect()->toRoute('physician');
        } else {
            $physician = $this->getPhysicianTable()->getPhysician($id);
            $data = array(
                'name'      =>  $physician->name,
                'surname'   =>  $physician->surname,
                'pesel'     =>  $physician->pesel,
                'password'  =>  $physician->password
                
            );
            $form = new \Application\Form\PhysicianForm;
            $form->setData($data);
            
            return new ViewModel(array(
                'id'    =>  $id,
                'form'  =>  $form
            ));
          
        }
    }
    
    public function saveAction()
    {
        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        if (!$id)
        {
            $this->redirect()->toRoute('home');
        } else {
           
            if ($request->isPost())
            {
                $physician = new \Application\Model\Physician();

                $data = array(
                    'name'      =>  $request->getPost('name'),
                    'surname'   =>  $request->getPost('surname'),
                    'pesel'     =>  $request->getPost('pesel'),
                    'password'  =>  $request->getPost('password')
                );

                $physician->exchangeArray($data);
                $this->getPhysicianTable()->savePhysician($physician);
                $this->redirect()->toRoute('physician');
            } 
        }
    }
    
    public function cancelAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        
        if ($this->session->role === 'physician')
        {
            $physicianId    =   $this->session->id;
            
            $registrationInfo   =   $this->getRegistrationTable()->showRegistration(null,$id,null)->current();
            $patientInfo        =   $this->getPatientTable()->getPatient($registrationInfo['patient_id']);
            $this->getRegistrationTable()->deleteRegistraionPhysician($physicianId,$id);
            $transport = $this->getServiceLocator()->get('mail.transport');
            $message = new \Zend\Mail\Message();       
            $message->addFrom("rejestracja@super-med.pl", "Super-Med")
            ->addTo($patientInfo->email)
            ->setSubject("Odwołanie wizyty");
            $message->setEncoding("UTF-8");
            $bodyHtml = ("Informujemy, że twoja wizyta w dniu:"
                    . "".$registrationInfo['visit_date']."<br />"
                    . " Do lekarza: ".$registrationInfo['physician'].""
                    . "<br/> Została odwołana przez lekarza");
            $htmlPart = new MimePart($bodyHtml);
            $htmlPart->type = "text/html";
            $body = new MimeMessage();
            $body->setParts(array($htmlPart));
            $message->setBody($body);
            $transport->send($message);
            $this->redirect()->toRoute('physician');
        }
    }
}
