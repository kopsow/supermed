<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class RegistrationTable {
    
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
    
    public function getRegistration($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getRegistrationPatient($id_patient)
    {
       $id  = (int) $id_patient;
        $rowset = $this->tableGateway->select(array('patient_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
     public function getRegistrationPhysician($id_physician)
    {
       $id  = (int) $id_physician;
        $rowset = $this->tableGateway->select(array('physician_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getRegistrationScheduler($id)
    {
          $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('scheduler_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveRegistration(Registration $registration)
    {
        $data = array(            
            'patient_id'            => $registration->patient_id,
            'physician_id'          => $registration->physician_id,
            'visit_date'            => $registration->visit_date,
            'registration_date'     => $registration->registration_date,
            'scheduler_id'          => $registration->scheduler_id,           
              
        );

        $id = (int)$registration->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRegistration($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function addRegistration(Registration $registration)
    {
        
        $data = array(            
            'patient_id'            => $registration->patient_id,
            'physician_id'          => $registration->physician_id,
            'visit_date'            => $registration->visit_date,
            'registration_date'     => $registration->registration_date,
            'scheduler_id'          => $registration->scheduler_id,      
            
        );
        $this->tableGateway->insert($data);
    }
    /**
     * Funkcja usuwa rejestrację o podanym numrze id
     * 
     * @param type $id
     */
    public function deleteRegistration($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    /**
     * Funkcja usuwa rejestracje o podanym numerze id oraz o passujacym id lekarza
     * @param type $physicianId
     * @param type $registrationId
     */
    public function deleteRegistrationPhysician($physicianId,$registrationId)
    {
          $this->tableGateway->delete(array('id' => $registrationId,'physician_id'=>$physicianId));
    }
    
    //usuwanie rejestracji przez pacjenta
    public function deleteRegistrationPatient($patientId,$registrationId)
    {
        $this->tableGateway->delete(array('id' => $registrationId,'patient_id'=>$patientId));
    }
    
    public function busyHours($physician_id,$visit_date)
    {
         $select = $this->tableGateway->getSql()->select();
         $select->columns(array('visit_date'=>new \Zend\Db\Sql\Expression('TIME(visit_date)')));
         $select->where('physician_id='.$physician_id)->where('DATE(visit_date)="'.$visit_date.'"');
         $rowset = $this->tableGateway->selectWith($select);
        
        return $rowset;
    }   
    
    public function menagmentFetchAll()
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));
        $statmentSql->join('physician', 'registration.physician_id = physician.id',array('physician'=>new \Zend\Db\Sql\Expression('CONCAT(physician.name," ",physician.surname)')),'left');
        $statmentSql->join('patient', 'registration.patient_id = patient.id',array('patient'=>new \Zend\Db\Sql\Expression('CONCAT(patient.name," ",patient.surname)')),'left');
        
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
    }
    
    public function showSelect($patient_id = NULL, $physician_id = NULL)
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));
        $statmentSql->join('physician', 'registration.physician_id = physician.id',array('physician'=>new \Zend\Db\Sql\Expression('CONCAT(physician.name," ",physician.surname)')),'left');
        $statmentSql->join('patient', 'registration.patient_id = patient.id',array('patient'=>new \Zend\Db\Sql\Expression('CONCAT(patient.name," ",patient.surname)')),'left');
       
        if ($patient_id != NULL && $physician_id === NULL)
        {
            $statmentSql->where('registration.patient_id='.$patient_id);
        }
        if ($physician_id != NULL && $patient_id === NULL)
        {
            $statmentSql->where('physician.id='.$physician_id);
        }
        if ($patient_id != NULL && $physician_id != NULL )
        {
            $statmentSql->where(array(
                'patient.id'    =>  $patient_id,
                'physician.id'  =>  $physician_id
            ));
        }
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
    }
    
    public function showRegistration($patient_id = null,$registration_id = null,$physician_id = null)
    {
        $statmentSql = $this->tableGateway->getSql()->select();
        $statmentSql->columns(array('*'));        
        $statmentSql->join('physician', 'registration.physician_id = physician.id',array('physician'=>new \Zend\Db\Sql\Expression('CONCAT(physician.name," ",physician.surname)')),'left');
        $statmentSql->join('patient', 'registration.patient_id = patient.id',array('patient'=>new \Zend\Db\Sql\Expression('CONCAT(patient.name," ",patient.surname)')),'left');
        
            if ($patient_id)
            {
                $statmentSql->where(array('patient.id'    =>  $patient_id,
                ));
            } elseif ($registration_id) {
                $statmentSql->where(array(
                     'registration.id'  =>  $registration_id
                ));
               
            }
            
            if ($physician_id)
            {
                $statmentSql->where(array('physician.id'    =>  $physician_id,
                ));
               
            }
            
            if ($registration_id)
            {
                
                $statmentSql->where(array('registration.id'  =>  $registration_id
                ));
            }
            
        $statementResult = $this->tableGateway->getSql()->prepareStatementForSqlObject($statmentSql);
        $resultSet = $statementResult->execute();
        return $resultSet;
    }
    
}

