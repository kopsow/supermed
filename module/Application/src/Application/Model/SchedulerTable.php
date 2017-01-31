<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class SchedulerTable 
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

    public function getScheduler($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveScheduler(Scheduler $scheduler)
    {
        $data = array(            
            'physician_id'      => $scheduler->physician_id,
            'date_start'        => $scheduler->date_start,
            'date_end'          => $scheduler->date_end,
            'schedule'          => $scheduler->schedule,            
        );

        $id = (int)$scheduler->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getScheduler($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addScheduler(Scheduler $scheduler)
    {
        
        $data = array(            
            'physician_id'      => $scheduler->physician_id,
            'date_start'        => $scheduler->date_start,
            'date_end'          => $scheduler->date_end,
            'schedule'          => $scheduler->schedule,     
        );
        $this->tableGateway->insert($data);
    }
    public function deleteScheduler($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}