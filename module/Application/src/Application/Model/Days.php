<?php
namespace Application\Model;


class Days {
    public $id;
    public $scheduler_id;
    public $monday;
    public $tuesday;
    public $wednesday;
    public $thursday;
    public $friday;
    
    public function exchangeArray($data) {
        $this->id           = (isset($data['id']))              ? $data['id']           : null;
        $this->scheduler_id = (isset($data['scheduler_id']))    ? $data['scheduler_id'] : null;
        $this->monday       = (isset($data['monday']))          ? $data['monday']       : null;
        $this->tuesday      = (isset($data['tuesday']))         ? $data['tuesday']      : null;
        $this->wednesday    = (isset($data['wednesday']))       ? $data['wednesday']    : null;
        $this->thursday     = (isset($data['thursday']))        ? $data['thursday']    : null;
        $this->friday       = (isset($data['friday']))          ? $data['friday']       : null;
    }
}