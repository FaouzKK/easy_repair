<?php

namespace App\Class;

class Client extends User
{

    protected $client_id;

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getArray()
    {
        $params = [
            "client" => $this->client_id,
            "username" => $this->username,
            "password" => $this->password,
            "email" => $this->email,
            "created_at" => $this->created_at
        ];
        return $params;
    }
}
