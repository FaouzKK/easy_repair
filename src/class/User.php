<?php

namespace App\Class ;


class User {

    protected $username ;
    protected $password ;
    protected $email ;
    protected $created_at ;

    //Getter

    public function getUsername() {
        return $this->username;
    }

    // public function getPassword() {
    //     return $this->password;
    // }

    public function getEmail() {
        return $this->email;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}