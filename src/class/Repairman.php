<?php

namespace App\Class;

class Repairman extends User
{

    private $repairman_id;
    private $domain_label;


    public function getRepairmanId()
    {
        return $this->repairman_id;
    }
    public function getDomain()
    {
        return $this->domain_label;
    }

    public function getArray() 
    {
        return [
            "repairman_id" => $this->repairman_id,
            "email" => $this->email,
            "password" => $this->password,
            "domain_label" => $this->domain_label,
            "created_at" => $this->created_at
        ] ;
    }
}
