<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class DaysTable {
    
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
    
    public function getDays($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveDays(Days $days)
    {
        $data = array(            
            'scheduler_id'      => $days->scheduler_id,
            'monday'            => $days->monday,
            'tuesday'           => $days->tuesday,
            'wednesday'         => $days->wednesday,
            'thursday'          => $days->thursday,
            'friday'            => $days->friday,
              
        );

        $id = (int)$days->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getDays($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addDays(Days $days)
    {
        
        $data = array(            
            'scheduler_id'      => $days->scheduler_id,
            'monday'            => $days->monday,
            'tuesday'           => $days->tuesday,
            'wednesday'         => $days->wednesday,
            'thursday'          => $days->thursday,
            'friday'            => $days->friday,
            
        );
        $this->tableGateway->insert($data);
    }
    public function deleteDays($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}

