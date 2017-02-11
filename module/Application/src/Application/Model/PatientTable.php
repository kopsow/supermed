<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class PatientTable {
    
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()    {
        
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getPatient($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getPatientScheduler($id)
    {
          $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('scheduler_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function loginPatient($email,$password)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where('email="'.$email.'"')->where('password="'.$password.'"');
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset->current();
    }
    public function savePatient(Patient $patient)
    {
        $data = array(            
            'name'          => $patient->name,
            'surname'       => $patient->surname,
            'pesel'         => $patient->pesel,
            'birthday'      => $patient->birthday,
            'tel'           => $patient->tel,
            'email'         => $patient->email,
            'verified'      => $patient->verified,              
        );

        $id = (int)$patient->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPatient($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addPatient(Patient $patient)
    {
        
        $data = array(            
            'name'          => $patient->name,
            'surname'       => $patient->surname,
            'pesel'         => $patient->pesel,
            'password'      => $patient->password,
            'birthday'      => $patient->birthday,
            'tel'           => $patient->tel,
            'email'         => $patient->email,
            'verified'      => $patient->verified,
            
        );
        $this->tableGateway->insert($data);
    }
    public function deletePatient($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}

