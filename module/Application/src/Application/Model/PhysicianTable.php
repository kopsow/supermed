<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class PhysicianTable 
{
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

    public function getPhysician($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePhysician(Physician $physician)
    {
        $data = array(            
            'name'              => $physician->name,
            'surname'           => $physician->surname,
            'pesel'             => $physician->pesel,
            'email'          =>  $physician->email,
            'password'          => $physician->password,
            'holiday'           => $physician->holiday,
        );

        $id = (int)$physician->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPhysician($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    public function addPhysician(Physician $physician)
    {
        
        $data = array(            
            'name'              => $physician->name,
            'surname'           => $physician->surname,
            'pesel'             => $physician->pesel,
            'password'          => $physician->password,
            'email'             => $physician->email,
            'holiday'           => $physician->holiday,
        );
        $this->tableGateway->insert($data);
    }
    public function deletePhysician($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    public function lastInsertId() {
        return $this->tableGateway->lastInsertValue;
    }
    
    public function verifiedPatient($id)
    {
        $data = array(
            'verified'  =>  '1'
        );
        $this->tableGateway->update($data, array('id' => $id));
    }
    
    public function loginPhysician($email,$password)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where('email="'.$email.'"')->where('password="'.$password.'"');
        $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset->current();
    }
}