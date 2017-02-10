<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class TestController extends AbstractActionController
{
    public $scheduleTable;
    public $registrationTable;
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
        
      
            $physicianId = 4;
            $visitDate = '2017-02-10';
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
        
    }
    
   
}


