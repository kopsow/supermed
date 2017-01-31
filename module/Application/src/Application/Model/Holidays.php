<?php

namespace Application\Model;


class Holidays {
    public $id;
    public $date_start;
    public $date_end;
    public $physician_id;
    
     public function exchangeArray($data)
    {
        $this->id           = (isset($data['id']))              ? $data['id']           : null;
        $this->date_start   = (isset($data['date_start']))      ? $data['date_start']   : null;
        $this->date_end     = (isset($data['date_end']))        ? $data['date_end']     : null;
        $this->physician_id = (isset($data['physician_id']))    ? $data['physician_id'] : null;
        
    }
}
