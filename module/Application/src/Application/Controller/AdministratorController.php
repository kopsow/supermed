<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Users;
use Application\Form\UsersForm;

class AdministratorController extends AbstractActionController
{
    public $usersTable;
    public $roleTable;
  
    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
    }
    
    public function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Role\Model\RoleTable');
        }
        return $this->roleTable;
    }
   
    public function indexAction()
    {
        
        return new ViewModel(array(
            'users'     =>  $this->getUsersTable()->fetchAll(),
            'form'      => new \Application\Form\UsersForm()
        ));
    }
    public function addAction()
    {
        $form = new UsersForm();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $user = new Users();
            $data = array(
                'name'      =>  $request->getPost('name'),
                'surname'   =>  $request->getPost('surname'),
                'email'     =>  $request->getPost('email'),
                'password'  =>  $request->getPost('password'),
                'role_id'   =>  $request->getPost('roleSelect')
            );
            $user->exchangeArray($data);
            $this->getUsersTable()->saveUsers($user);
            $this->redirect()->toRoute('administrator');
        }
        return new ViewModel(array(
           'form'   =>  $form 
        ));
    }
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        
        if (!$id)
        {
            $this->redirect()->toRoute('administrator');
        } else {
            $form = new UsersForm;
            $user = $this->getUsersTable()->getUsers($id);
            
           
                $data = array(
                    'name'      =>  $user->name,
                    'surname'   =>  $user->surname,
                    'email'     =>  $user->email,
                    'password'  =>  $user->password,
                    'role_id'   =>  $user->role_id
                );
                
                $form->setData($data);
                $form->get('roleSelect')->setAttribute('value',$user->role_id);      
            return new ViewModel(array(
                'form'      =>  $form,
                'id'        =>  $id
            ));
        }
    }
    
    public function saveAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            
            $user = new Users();
            
            $data = array(
                'id'        =>  $this->params()->fromRoute('id'),
                'name'      =>  $request->getPost('name'),
                'surname'   =>  $request->getPost('surname'),                
                'email'     =>  $request->getPost('email'),
                'password'  =>  $request->getPost('password'),
                'role_id'   =>  $request->getPost('roleSelect')
            );
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
            $user->exchangeArray($data);
            $this->getUsersTable()->saveUsers($user);
            $this->redirect()->toRoute('administrator');
        }else {
            $this->redirect()->toRoute('administrator');
        }
    }
    
   
}


