<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Application\Model\Scheduler;

class AjaxController extends AbstractActionController
{
    
    protected $roleTable;
    protected $scheduleTable;
    
    public function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Role\Model\RoleTable');
        }
        return $this->roleTable;
    }
    public function getScheduleTable()
    {
        if (!$this->scheduleTable) {
            $sm = $this->getServiceLocator();
            $this->scheduleTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->scheduleTable;
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
        }
        $json = new JsonModel(array(
                '28-02-2017',
                '20-02-2017'
            ));
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
}


