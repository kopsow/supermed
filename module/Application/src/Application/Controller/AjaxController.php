<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Application\Model\Scheduler;
use Application\Model\Registration;

class AjaxController extends AbstractActionController
{
    
    protected $roleTable;
    protected $scheduleTable;
    protected $registrationTable;


    public function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Role\Model\RoleTable');
        }
        return $this->roleTable;
    }
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
    
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function kopsowAction() {
        
        $wynik = $this->getRoleTable()->fetchAll();
        $wypluj = array();
        foreach ($wynik as $wiersz) {
           
           $wypluj[$wiersz->id][]=$wiersz->name;
        }
        $result = new JsonModel($wypluj);
 
        return $result;
    }
    
    public function addSchedulerAction() {
        $response = $this->getResponse();
        $requset = $this->getRequest();

       if ($requset->isXmlHttpRequest()) {
           
            $response->setStatusCode(200);
            $result = $response->setContent($requset->getPost('koniec'));      
            
            $json = new JsonModel(array(
                $requset->getPost('dni')[1]
            ));
          return $json;
        }
    
    }
    public function rejestracjaAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $physicianId = $request->getPost('physicianId');
            $date_now   = date('m');
            
           $schedulers = $this->getSchedulerTable()->getSchedulerPhysician($physicianId,$date_now);
           $result = array();
            foreach ($schedulers as $val)
            {
                $result[]= date('Y-m-d',  strtotime($val->date_start));
            }
        }
        $json = new JsonModel($result);
          return $json;
    }
    
    public function testAction(){
        $scheduler = new Scheduler();
            $data = array(
                'date_start' => '2017-01-01',
                'date_end'  =>  '2017-02-05',
                'physician_id'=>4,
                'schedule' =>'null'
            );
            $scheduler->exchangeArray($data);
            $this->getScheduleTable()->saveScheduler($scheduler);
    }
    
    public function freeHoursAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest())
        {
            $physicianId = $request->getPost('physicianId');
            $visitDate = $request->getPost('visitDate');
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
            
            foreach ($busyHours as $time)
            {
                $result = array_search(date('H:i',  strtotime($time->visit_date)), $godzinyPrzyjec);
                if (is_numeric($result) && $result>0)
                {
                    //unset($godzinyPrzyjec[$result]);
                    array_splice($godzinyPrzyjec, $result,1);
                }
            }

           $json = new JsonModel($godzinyPrzyjec);
              return $json;
        } else {
            
              return new JsonModel(array());
        }
        
    }
}


