<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class UsersTable {
    protected $tableGateway;
     
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getUsers($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function addUsers(Users $users)
    {
        
        $data = array(            
            'name'              => $users->name,
            'surname'           => $users->surname,
            'email'             => $users->email,
            'password'          => $users->password,
            'role_id'           => $users->role_id,
        );
        $this->tableGateway->insert($data);
    }
    
    public function saveUsers(Users $user)
    {
        $data = array(            
            'name'          =>  $user->name,
            'surname'       =>  $user->surname,
            'email'         =>  $user->email,
            'password'      =>  $user->password,
            'role_id'       =>  $user->role_id
              
        );

        $id = (int)$user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsers($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    public function deleteUsers($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}