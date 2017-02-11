<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Form\PhysicianForm;
use Application\Model\Scheduler;
use Application\Model\SchedulerTable;

use Zend\Session\Container;
class SchedulerController extends AbstractActionController
{
    
    
    public $physicianTable;
    public $schedulerTable;
    public $daysTable;
    public $session;
    
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
    
    public function getSchedulerTable()
    {
        if (!$this->schedulerTable) {
            $sm = $this->getServiceLocator();
            $this->schedulerTable = $sm->get('Scheduler\Model\SchedulerTable');
        }
        return $this->schedulerTable;
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
        
        if ($this->session->role === 'physician')
        {
            $this->layout('layout/physician');
            $this->layout()->setVariable('scheduler_active', 'active');
            $scheduler = $this->getSchedulerTable()->getSchedulerPhysician($this->session->id,date('m'));
        } else  {
            $this->redirect()->toRoute('authorization');
        }
        return new ViewModel(array(
            'schedulers'    => $scheduler,
        ));
    }
    
    public function addAction() 
    {
        
        $request = $this->getRequest();
        
        if ($request->isPost()) 
        {
           
           $data =  explode(',', $request->getPost('date'));
           asort($data);
           
           for ($i=0 ; $i<count($data) ; $i++) 
           {               
               $day =  date("w",  strtotime($data[$i]));
               
               switch ($day)
               {
                   case 1:  
                       
                       $this->addSchedulerDb($data[$i], 'mon_start', 'mon_end');
                       break;
                   case 2:
                      $this->addSchedulerDb($data[$i], 'tue_start', 'tue_end');
                       break;
                   case 3:
                       $this->addSchedulerDb($data[$i], 'wed_start', 'wed_end');
                       break;
                   case 4:
                      $this->addSchedulerDb($data[$i], 'thu_start', 'thu_end');
                       break;
                   case 5:
                       $this->addSchedulerDb($data[$i], 'fri_start', 'fri_end');
                       break;
                   
               }
           }
          
        }
        $form = new physicianForm();
        return new ViewModel(array(
            'physicians' =>$form,
        ));
        /*
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $data_scheduler = array(
                'physician_id'  => $request->getPost('physician'),
                'date_start'    => $request->getPost('date_start'),
                'date_end'      => $request->getPost('date_end'),
            );
            
            $schedule = new Scheduler();
            $schedule->exchangeArray($data_scheduler);
            $this->getSchedulerTable()->saveScheduler($schedule);
            $scheduler_id = $this->getSchedulerTable()->lastInsertId();
            $data_days = array(
                'scheduler_id'  =>  $scheduler_id,
                'monday'        =>  $request->getPost('mon_start').'|'.$request->getPost('mon_end'),
                'tuesday'       =>  $request->getPost('tue_start').'|'.$request->getPost('tue_end'),
                'wednesday'     =>  $request->getPost('wed_start').'|'.$request->getPost('wed_end'),
                'thursday'      =>  $request->getPost('thu_start').'|'.$request->getPost('thu_end'),
                'friday'        =>  $request->getPost('fri_start').'|'.$request->getPost('fri_end'),
            );
            
            $day = new Days();
            $day->exchangeArray($data_days);
            $this->getDaysTable()->saveDays($day);
        }
        $form = new physicianForm();
        return new ViewModel(array(
            'physicians' =>$form,
        ));*/
    }
    public function deleteAction() 
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
             return $this->redirect()->toRoute('schedulerShow');
         } else {
               
               $this->getSchedulerTable()->deleteScheduler($id);
         }
         
         
         return $this->redirect()->toRoute('schedulerShow');
    }
    public function showAction() {
         $form = new physicianForm();
        
        $request = $this->getRequest();
        $schedulerList = null;
        
       
        if ($request->isPost()) {
            
            $physicianId =(int) $request->getPost('physicianId');
            $orderBy = $request->getPost('orderBy');
          
            if (!empty($orderBy)) {               
                $schedulerList =  $this->getSchedulerTable()->showScheduler($orderBy,$physicianId);
                $form->get('physicianScheduler')->setAttribute('value',$physicianId);                
            } else {
                $schedulerList =  $this->getSchedulerTable()->showScheduler(null,$physicianId);
                $form->get('physicianScheduler')->setAttribute('value',$physicianId);
            }
            
        } else {
          $schedulerList =  $this->getSchedulerTable()->showScheduler();
        }
        return new ViewModel(array(
            'schedulers' => $schedulerList,
            'physicians' => $form
        ));
    }
    
   private function addSchedulerDb($data,$start,$end) {
      
       $request = $this->getRequest();
        if ($request->getPost($start)!='00:00' && $request->getPost($end) !='00:00')
        {            
            
                $date_start = new \DateTime($data.$request->getPost($start));
                $date_end = new \DateTime($data.$request->getPost($end));
            
            $schedule = new Scheduler();
            $dataSchedule = array(
                'physician_id'   =>  $request->getPost('physician'),
                'date_start'     =>  $date_start->format('Y-m-d H:i'),
                'date_end'       =>  $date_end->format('Y-m-d H:i')
            );            
             if ($this->getSchedulerTable()->checkDate($request->getPost('physician'),$date_start->format('Y-m-d H:i')) === 0)
             {
                 $schedule->exchangeArray($dataSchedule);
            $this->getSchedulerTable()->saveScheduler($schedule);
            
        return $this->getSchedulerTable()->lastInsertId();
             }
            
                      
        } else {
            return false;
        }
   }
}


