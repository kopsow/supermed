<?php
namespace Application\Model;

class Physician
{
    public $id;
    public $name;
    public $surname;
    public $pesel;
    public $password;
    public $holiday;

    public function exchangeArray($data)
    {
        $this->id           = (isset($data['id']))              ? $data['id']       : null;
        $this->name         = (isset($data['name']))            ? $data['name']     : null;
        $this->surname      = (isset($data['surname']))         ? $data['surname']  : null;
        $this->pesel        = (isset($data['pesel']))           ? $data['pesel']    : null;
        $this->password     = (isset($data['password']))        ? $data['password'] : null;
        $this->holiday      = (isset($data['holiday']))         ? $data['holiday']  : null;
    }
}