<?php
namespace Application\Model;


class Role {
    public $id;
    public $name;
    public $description;
    
    public function exchangeArray($data) {
        $this->id           = (isset($data['id']))              ? $data['id']           : null;
        $this->name         = (isset($data['name']))            ? $data['name']         : null;
        $this->description  = (isset($data['description']))     ? $data['description']  : null;
    }
}