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


class PhysicianController extends AbstractActionController
{
    
    public $physicianTable;
    public $holidaysTable;
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
        echo $this->session->role;
       if (!$this->session || $this->session->role ==='patient')
       {
           $this->redirect()->toRoute('autoryzacja',array('action'=>'physician'));
       }
        return new ViewModel(array(
            'physicians' =>  $this->getPhysicianTable()->fetchAll(),
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
}
