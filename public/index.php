<?php

use App\Class\DataManagement;
use App\class\SessionManager;

require "../vendor/autoload.php";

$route = new AltoRouter();



$route->map("GET", "/", function () {

    $session = SessionManager::getIdBySession();

    $data = new DataManagement();

    if ($session) {

        if ($session["role"] == 'client') {
            $client = $data->getCliensDetailById($session["id"]);

            if (!$client) {
                echo "user not found";
                exit();
            } else {
                dd($client);
            }
        } else {
            $repairman = $data->getRepairManById($session["id"]);

            if (!$repairman) {
                echo "user not found";
                exit();
            } else {
                dd($repairman);
            }
        }
    } else {
        $user_id = $data->registerNewClient("Kevin", "Chakams9909@gmail.com", "12345678");

        SessionManager::setSession($user_id, "client");

        dd($user_id);
    }
}, "home");



// Fonction de chargement du tableau de bord
$dashboardLoader = function (string $sidebar, array $message = null) {

    // Vérifier la session
    $session = SessionManager::getIdBySession();
    if (!$session) {
        header("Location: /");
        exit();
    }

    $data = new DataManagement();

    // Vérifier si l'utilisateur est un client
    if ($session["role"] == 'client') {
        $client = $data->getCliensDetailById($session["id"]);

        if (!$client) {
            echo "User not found";
            exit();
        }

        // Définir les options de sidebar
        $sidebar_option = ["pending", "accepted", "all"];
        $sidebar = in_array($sidebar, $sidebar_option) ? $sidebar : "pending";

        //Recuperer les requettes
        if (!empty($_GET["search"])) {

            $requestList = $data->getRequestsListForClient($client->getClientId(), $sidebar, $_GET["search"]);
        } else {
            $requestList = $data->getRequestsListForClient($client->getClientId(), $sidebar);
        }

        // Charger la vue du tableau de bord
        require "../views/client/dashboard.php";

        exit();
    } else {
        echo "Access denied";
        exit();
    }
};


//-----------------------Définir la route dasnboard sans requetes POST-------------------------

$route->map("GET", "/dashboard/[a:sidebar]", $dashboardLoader, "dashboard");



$route->map("GET", "/dashboard", function () {

    header("Location: /dashboard/pending");
    exit();
});


//-------------------------------Route Dashboard avec requetes POST---------------------------------------

$route->map("POST", "/dashboard/[a:sidebar]", function ($sidebar) use ($dashboardLoader) {

    $params = ["sidebar" => $sidebar];

    if (!empty($_POST["label"]) && !empty($_POST["domain"]) && isset($_POST["description"]) && !empty($_POST["address"])) {
        $params["message"] = [
            "success" => true,
            "info" => "Requette ajoutée avec succès"
        ];
        //Logique ici
    } else {
        $params["message"] = [
            "success" => false,
            "info" => "Echec lors de l'ajout de la nouvelle requette\nCertaines valeures requises sont manquantes"
        ];
    }

    call_user_func_array($dashboardLoader, $params);
});


$route->map("GET", "/api/getrequest", function () {

    $pdo = new DataManagement();

    $request = $pdo->getRequestById(1);

    header("Content-Type: application/json");

    echo json_encode($request->getArray());
});



//Matcher les routes
$match = $route->match();

if (is_array($match) && is_callable($match['target'])) {

    call_user_func_array($match['target'], $match['params']);
} else {

    // no route was matched
    dd($match);
}
