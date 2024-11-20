<?php

namespace App\class;

class Request
{

    private int $request_id;
    private string $label;
    private ?string $description;
    private string $address;
    private string $created_at;
    private int $clients_client_id;
    private string $domain_name;
    private ?int $repairmen_repairman_id;
    private string $request_status;


    //Getter
    public function getRequestId()
    {
        return $this->request_id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getRequestStatus()
    {
        return $this->request_status;
    }

    public function getDomainName()
    {
        return $this->domain_name;
    }

    public function getRepairman()
    {
        if ($this->repairmen_repairman_id == null) return null;

        $pdo = new DataManagement();

        return $pdo->getRepairManById($this->repairmen_repairman_id);
    }

    public function getClient()
    {
        $pdo = new DataManagement();
        return $pdo->getCliensDetailById($this->clients_client_id);
    }

    public function getArray()
    {

        return [
            'request_id' => $this->request_id,
            'label' => $this->label,
            'description' => $this->description,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'client' => $this->getClient()?->getArray(),
            'domain_name' => $this->domain_name,
            'repairman' => $this->getRepairman(),
            'request_status' => $this->request_status
        ];
    }
}
