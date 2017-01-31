<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class HolidaysTable 
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

    public function getHolidays($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveHolidays(Holidays $holidays)
    {
        $data = array(            
            'date_start'        => $holidays->date_start,
            'date_end'          => $holidays->date_end,
            'physician_id'      => $holidays->physician_id,
        );

        $id = (int)$holidays->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getHolidays($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addHolidays(Holidays $holidays)
    {
        
        $data = array(            
            'date_start'        => $holidays->date_start,
            'date_end'          => $holidays->date_end,
            'physician_id'      => $holidays->physician_id,
        );
        $this->tableGateway->insert($data);
    }
    public function deleteHolidays($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}