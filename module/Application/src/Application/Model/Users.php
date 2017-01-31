<?php

namespace Application\Model;


class Users {
    public $id;
    public $name;
    public $surname;
    public $email;
    public $role;
    
     public function exchangeArray($data) {
        $this->id           = (isset($data['id']))              ? $data['id']       : null;
        $this->name         = (isset($data['name']))            ? $data['name']     : null;
        $this->surname      = (isset($data['surname']))         ? $data['surname']  : null;
        $this->email        = (isset($data['email']))           ? $data['email']    : null;
        $this->role         = (isset($data['role']))            ? $data['role']    : null;
     }
}