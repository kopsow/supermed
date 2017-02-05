<?php
namespace Application\Model;


class Patient {
    public $id;
    public $name;
    public $surname;
    public $pesel;
    public $password;
    public $birthday;
    public $tel;
    public $email;
    public $verified;
    
    
    public function exchangeArray($data) {
        $this->id           = (isset($data['id']))          ? $data['id']           : null;
        $this->name         = (isset($data['name']))        ? $data['name']         : null;
        $this->surname      = (isset($data['surname']))     ? $data['surname']      : null;
        $this->pesel        = (isset($data['pesel']))       ? $data['pesel']        : null;
        $this->password     = (isset($data['password']))    ? $data['password']     : null;
        $this->birthday     = (isset($data['birthday']))    ? $data['birthday']     : null;
        $this->tel          = (isset($data['tel']))         ? $data['tel']          : null;
        $this->email        = (isset($data['email']))       ? $data['email']        : null;
        $this->verified     = (isset($data['verified']))    ? $data['verified']     : null;
    }
}