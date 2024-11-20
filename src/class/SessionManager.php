<?php

namespace App\class;

use PDO;
use App\Class\Config;
use DateTime;
use Exception;
use Throwable;

class SessionManager
{

    /**
     * Initialise la connection a PDO
     * @return \PDO
     */
    private static function initPdo(): PDO
    {
        $DB_NAME = Config::getVar('SESSION_DB_NAME');
        $DB_USER = Config::getVar('DB_USER');
        $DB_PASSWORD = Config::getVar('DB_PASS') ?? null;
        $DB_HOST = Config::getVar('DB_HOST');

        return new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASSWORD);
    }


    /**
     * Create a session in database
     * @param int $userId id de l'utilisateur
     * @param string $userRole Role de l'utilisateur
     * @throws \Exception
     * @return string Le md5 de la session
     */
    private static function createNewSession(int $userId, string $userRole): string
    {

        try {

            $pdo = SessionManager::initPdo();

            $stmt = $pdo->prepare("
                INSERT INTO sessions (session_id,user_id,user_role,expired_at)
                VALUES (:session_id,:user_id,:user_role,DATE_ADD(NOW(), INTERVAL 1 WEEK));");

            if (!$stmt) {
                throw new Exception("Error preparing statement");
            }

            // $date = new DateTime();
            // $nextWeek = $date->modify('+1 week')->getTimestamp();

            $session = 'ERSESS_' . md5(uniqid());

            $result = $stmt->execute([
                'session_id' => $session,
                'user_id' => $userId,
                'user_role' => $userRole,
            ]);

            if ($result) {
                return $session;
            } else {
                throw new Exception("Error creating session");
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }


    /**
     * placer la session dans les cookies
     * @param int $userId
     * @param string $userRole Role de l'utilisateur
     * @throws \Throwable
     * @return bool True si la session a été créée
     */
    static function setSession($userId, $userRole): bool
    {

        try {

            $session = SessionManager::createNewSession($userId, $userRole);

            return setcookie('session', $session, time() +  60 * 60 * 24 * 7);
        } catch (Throwable $th) {
            throw $th;
        }
    }


    static function getIdBySession(): array | bool
    {

        try {
            $session = $_COOKIE['session'] ?? null;

            if (!$session)  return false;

            $pdo = SessionManager::initPdo();

            $stmt = $pdo->prepare("SELECT user_id,user_role FROM sessions WHERE session_id = :session_id AND expired_at > NOW()");

            $stmt->execute(['session_id' => $session]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    "id" => $result['user_id'],
                    "role" => $result['user_role']
                ];
            } else {
                return false;
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }
}
