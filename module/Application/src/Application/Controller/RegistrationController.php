<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\PhysicianForm;
use Zend\Db\Adapter\Adapter;

class RegistrationController extends AbstractActionController
{
    
    public $daysTable;
    
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
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
        return new ViewModel(array(
            'physicians' =>$form,
        ));
    }
    
   public function checkAction() 
   {
       $request = $this->getRequest();
       
       if($request->isPost()) 
       {
           $physicianId = $request->getPost('physicianId');
           $data = $request->getPost('data');
           $day = ('Wybrany dzień to: '.  strtolower(date("l",  strtotime($data))));
           //$result =  $this->getDaysTable()->getDaysScheduler(15);
           $this->czyPrzyjmuje($physicianId, $data, $day);
           return '';
       } else {
           return $this->redirect()->toRoute('registration');
       }
   }
   
   private function zakres($physician_id,$date) {
       $id=$physician_id;

       $dbAdapter = new Adapter($this->configArray);
       $statement = $dbAdapter->query('SELECT id FROM scheduler WHERE physician_id='.$id.' AND '
               . '(date_start>="'.$date.'"<=date_end)');
       $result = $statement->execute();

       return $result;
   }
   private function czyPrzyjmuje($physician_id,$date,$day)
   {
       $schedler_id = (int) $this->zakres($physician_id, $date)->key();
       if ($schedler_id >0) {
           echo $schedler_id;
       } else {
           echo 'Nie przyjume w tym dniu';
       }
   }
}


