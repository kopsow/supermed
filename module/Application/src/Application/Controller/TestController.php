<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class TestController extends AbstractActionController
{
    public function indexAction()
    {
        
        $time_start =  '8:00';
        $time_end   =  '16:00';
        
        $result = strtotime($time_start);
        
        return new ViewModel(array(
            'result'=>$result,
        ));
    }
    
   
}


