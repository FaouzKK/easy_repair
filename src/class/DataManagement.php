<?php

namespace App\Class;

use Exception;
use PDO;
use App\Class\Config;
use App\Class\PrintableException;
use Throwable;

/**
 * Une classe de manager la database coter client-requetes
 */
class DataManagement
{

    private $DB_HOST;
    private $REQUEST_DB_NAME;
    private $DB_USER;
    private $DB_PASS;

    private $pdo;

    /**
     * Constructor
     * Connect to the database
     */
    public function __construct()
    {
        try {

            $this->DB_HOST = Config::getVar('DB_HOST');
            $this->REQUEST_DB_NAME = Config::getVar('REQUEST_DB_NAME');
            $this->DB_USER = Config::getVar('DB_USER');
            $this->DB_PASS = Config::getVar('DB_PASS') ?? null;

            $this->pdo = new PDO("mysql:host={$this->DB_HOST};dbname={$this->REQUEST_DB_NAME}", $this->DB_USER, $this->DB_PASS);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $th) {

            throw $th;
        }
    }



    /**
     * Permets de creer un nouveau client
     * @param string $username le nom d'utilisateur du client a creer
     * @param string $email l'email du client a creer
     * @param string $password le mot de passe du client a creer
     * @throws \Exception si une erreur survient c'est qu'il un soucis avec les requetes
     * @throws \App\Class\PrintableException Cette erreur survient si l'email est deja utiliser
     * @return bool|string
     */
    public function registerNewClient(string $username, string $email, string $password): bool|string
    {

        try {

            //on verifie si l'email est deja Utiliser
            $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE email = :email");
            if (!$stmt) throw new Exception("Error on preparing Request");
            $stmt->execute(['email' => $email]);

            $exist = $stmt->fetch();

            if ($exist) {
                //Cas ou un utilisateur avec le meme email est present dans la base de donnée
                throw new PrintableException("Cet email est deja associee a un compte");
            }

            //Cas ou aucun utilisateur avec le meme email n'est present dans la base de donnée
            $stmt = $this->pdo->prepare("INSERT INTO clients (username, email, password) VALUES (:username, :email, :password)");
            if (!$stmt) throw new Exception("Error on preparing Request");

            $result = $stmt->execute(
                [
                    "username" => $username,
                    "email" => $email,
                    "password" => password_hash($password, PASSWORD_DEFAULT)
                ]
            );


            if (!$result) {
                throw new Exception("Une erreur est survenue lors de l'enregistrement");
            }

            //Retourner l'id du nouvel lien
            return $this->pdo->lastInsertId();
        } catch (Exception $th) {
            throw $th;
        }
    }



    /**
     * Permets de creer un nouveau technicien
     * @param string $username le nom d'utilisateur du technicien a creer
     * @param string $email l'email du technicien a creer
     * @param string $password le mot de passe du technicien a creer
     * @throws \Exception si une erreur survient c'est qu'il un soucis avec les requetes
     * @throws \App\Class\PrintableException Cette erreur survient si l'email est deja utiliser
     * @return bool|string
     */
    public function registerNewRepairman(string $username, string $email, string $password, string $domain_label): bool|string
    {

        try {

            //On verifie le domain inserer
            $stmt = $this->pdo->prepare("SELECT * FROM domains WHERE label=:label");
            $stmt->execute(['label' => $domain_label]);

            $domain = $stmt->fetch();

            if (!$domain) {
                throw new PrintableException("Le domain specifier est inconnus");
            }

            //On verifie si l'email est deja Utiliser
            $stmt = $this->pdo->prepare("SELECT * FROM repairmen WHERE email = :email");
            if (!$stmt) throw new Exception("Error on preparing Request");
            $stmt->execute(['email' => $email]);

            $exist = $stmt->fetch();

            if ($exist) {
                //Cas ou un utilisateur avec le meme email est present dans la table repairmen
                throw new PrintableException("Cet email est deja associee a un compte");
            }

            //Cas ou aucun utilisateur avec le meme email n'est present dans la table repairmen
            $stmt = $this->pdo->prepare("INSERT INTO repairmen (username, email, password) VALUES (:username, :email, :password)");
            if (!$stmt) throw new Exception("Error on preparing Request");

            $result = $stmt->execute(
                [
                    "username" => $username,
                    "email" => $email,
                    "password" => password_hash($password, PASSWORD_DEFAULT)
                ]
            );

            if (!$result) {
                throw new PrintableException("Une erreur est survenue lors de l'enregistrement");
            }

            //Retourner l'id du nouvel lien
            $repairmain_id = $this->pdo->lastInsertId();

            //On ajoute le technicien au domain
            $stmt = $this->pdo->prepare("INSERT INTO repairmen_has_domains (repairmen_repairman_id, domains_domain_id) VALUES (:repairman_id, :domain_id)");
            if (!$stmt) throw new Exception("Error on preparing Request");

            $result = $stmt->execute(
                [
                    "repairman_id" => $repairmain_id,
                    "domain_id" => $domain['domain_id']
                ]
            );

            if (!$result) {
                throw new Exception("Une erreur est survenue lors de l'enregistrement");
            }

            return $repairmain_id;
        } catch (Throwable $th) {
            throw $th;
        }
    }



    /**
     * Permets de D'enregistrer de nouvelles requetes
     * @param string $label Le titre associer a la requete
     * @param string|null $description Une description detaille sur le probleme
     * @param string $address l'addresse du client
     * @param int $client_id l'id du client
     * @param string $domain_label le nom du domaine
     * @throws \App\Class\PrintableException
     * @throws \Exception
     * @return bool
     */
    public function createNewRequest(string $label, string $address, int $client_id, string $domain_label, string|null $description = null): bool
    {

        try {

            //On recupere d'abord l'id du domain
            $stmt = $this->pdo->prepare("SELECT * FROM domains WHERE label=:label");
            $stmt->execute(['label' => $domain_label]);

            $domain = $stmt->fetch();

            if (!$domain) {
                throw new PrintableException("Le domain specifier est inconnus");
            }

            $stmt = $this->pdo->prepare("INSERT INTO requests (label, description, address, clients_client_id, domains_domain_id) VALUES (:label, :description, :address, :client_id, :domain_id)");
            if (!$stmt) throw new Exception("Error on preparing Request");

            $result = $stmt->execute(
                [
                    "label" => $label,
                    "description" => $description,
                    "address" => $address,
                    "client_id" => $client_id,
                    "domain_id" => $domain['domain_id']
                ]
            );

            if (!$result) {
                throw new Exception("Une erreur est survenue lors de l'enregistrement");
            }

            return true;
        } catch (Throwable $th) {
            throw $th;
        }
    }


    /**
     * Recupere les details d'un client
     * @param int $client_id l'id du client
     * @throws \Exception
     * @return \App\Class\Client
     */
    public function getCliensDetailById(int $client_id): Client
    {

        try {

            $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE client_id=:client_id");
            $stmt->execute(['client_id' => $client_id]);

            if (!$stmt) throw new Exception("Erreur lors de la preparation de la requete");

            return $stmt->fetchObject(Client::class);
        } catch (Throwable $th) {
            throw $th;
        }
    }


    /**
     * Recupere les details d'un technicien
     * @param int $repairman_id l'id du technicien
     * @throws \Exception
     * @return \App\Class\Repairman
     */
    public function getRepairManById(int $repairman_id)
    {

        try {

            $stmt = $this->pdo->prepare(
                "SELECT repairman_id,username,email,password,created_at,label as domain_label FROM repairmen r
                LEFT JOIN repairmen_has_domains rd ON r.repairman_id = rd.repairmen_repairman_id
                LEFT JOIN domains d ON d.domain_id = rd.domains_domain_id
                WHERE repairman_id = :repairman_id"
            );

            $stmt->execute(['repairman_id' => $repairman_id]);

            if (!$stmt) throw new Exception("Erreur lors de la preparation de la requete");

            return $stmt->fetchObject(Repairman::class);
        } catch (Throwable $th) {
            throw $th;
        }
    }


    /**
     * Recuprer la liste des requetes et leurs informations d'un client
     * @param int $id indentifiant du client
     * @param string $type  status de la requette ou "all" pour toutes les requetes
     * @param string $search mot cle en cas de recherche pour la recherche
     * @throws \Exception
     * @return array[Request]
     */
    public function getRequestsListForClient(int $id, string $type = "all", string $search = null): array
    {

        try {

            $filtrer = match ($type) {
                "all" => "",
                "pending" => "AND rs.status_type = 'pending'",
                "accepted" => "AND rs.status_type = 'accepted'",
                default => ""
            };

            $searchFilter = $search ? "AND r.label LIKE :search" : "";

            $stmt = $this->pdo->prepare("
                SELECT
                    r.request_id,
                    r.label,
                    r.description,
                    r.address,
                    r.created_at,
                    r.clients_client_id,
                    r.repairmen_repairman_id,
                    rs.status_type as request_status,
                    d.label as domain_name   
                FROM requests r
                LEFT JOIN requests_status rs ON r.request_id = rs.requests_request_id
                LEFT JOIN domains d ON r.domains_domain_id = d.domain_id
                WHERE r.clients_client_id = :client_id
                {$filtrer}
                {$searchFilter}
                ORDER BY r.created_at DESC;");

            if (!$stmt) throw new Exception("Erreur lors de la preparation de la requete");

            $params = ['client_id' => $id];

            if ($search) $params['search'] = "%{$search}%";

            $result = $stmt->execute($params);

            if (!$result) throw new Exception("Erreur lors de l'execution de la requete");

            return $stmt->fetchAll(PDO::FETCH_CLASS, Request::class);
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function getRequestById(int $id)
    {

        try {

            $stmt = $this->pdo->prepare("
                SELECT
                    r.request_id,
                    r.label,
                    r.description,
                    r.address,
                    r.created_at,
                    r.clients_client_id,
                    r.repairmen_repairman_id,
                    rs.status_type as request_status,
                    d.label as domain_name   
                FROM requests r
                LEFT JOIN requests_status rs ON r.request_id = rs.requests_request_id
                LEFT JOIN domains d ON r.domains_domain_id = d.domain_id
                WHERE r.request_id = :request_id;");

            $stmt->execute(['request_id' => $id]);

            if (!$stmt) throw new Exception("Erreur lors de la preparation de la requete");

            return $stmt->fetchObject(Request::class);

        } catch (Throwable $th) {
            throw $th;
        }
    }
}
