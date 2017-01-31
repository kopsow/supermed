<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

class PhysicianForm extends Form {
    
    private $configArray = array(
          'driver'      =>   'Mysqli',
          'database'    =>   'supermed',
          'username'    =>   'root',
          'password'    =>   'kopsow82',
          'hostname'    =>   'localhost',
          'charset'     =>   'utf8'
        );
    
    public function __construct() {
        parent::__construct('physician');
        
        $this->add(array(
               'type'       =>  'Zend\Form\Element\Select',
               'name'       =>  'physician',
               'options'    => array (
                    'label'     => 'Wybór lekarza',
                    'value_options' => $this->getPhysician(),
               ),
               'attributes' =>  array (
                   'id'     =>  'sel1',
                   'class'  =>  'form-control',
               )
        ));
    }
    
    private function getPhysician() {
       
        $dbAdapter = new Adapter($this->configArray);
        $statement = $dbAdapter->query('SELECT id,CONCAT(name," ",surname) AS name FROM physician');
        $result = $statement->execute();
        
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] =   $res['name'];
        }
        
        return $selectData;
    }
}
