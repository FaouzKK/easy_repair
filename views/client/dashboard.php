<?php

$headerTitle = match ($sidebar) {
    "all" => "Liste de vos requetes",
    "accepted" => "Vos rendez-vous",
    "pending" => "Requetes en attente"
};

$tableHeader = [
    "pending" => ["label", "address", "date", "domain"],
    "accepted" => ["label", "address", "date", "domain", "approbateur"],
    "all" => ["label", "address", "date", "domain", "status", "Approbateur"]
];
$pageInt = isset($_GET['page']) && $_GET["page"] > 1 ? $_GET['page']  : 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/client_dashboard.css">
    <script type="module" src="/js/client_dashboard.js" defer></script>
    <title>Dashboard | <?= $client->getUsername() ?></title>

</head>

<body>
    <main class="d-flex p-2 justify-content-between" style="height: 100vh; width: 100vw; background-color: #f8f9fa">

        <!-- SIDE-BAR-->

        <section id="side-bar">
            <div id="client-profil" class="d-flex align-items-center">
                <div id="client-icon" class="fw-9 px-3 py-2 bg-primary text-white fw-bold h4 text-uppercase"><?= $client->getUsername()[0] ?></div>
                <div id="client-info" class="ms-2">
                    <div id="client-usernmae" class="text-capitalize fw-bold"><?php echo $client->getUsername(); ?></div>
                    <div id="client-email" style="font-size: 12px;"><?= $client->getEmail() ?></div>
                </div>
            </div>
            <div id="side-option" class="mt-5">
                <ul>
                    <li class="<?php if ($sidebar == 'pending') echo 'bg-primary text-white' ?>" page="pending"><i class="bi bi-list-ul fw-bold"></i> Requetes en attente</li>
                    <li class="<?php if ($sidebar == 'accepted') echo 'bg-primary text-white' ?>" page="accepted"><i class="bi bi-list-check fw-bold"></i> Vos rendez-vous</li>
                    <li class="<?php if ($sidebar == 'all') echo 'bg-primary text-white' ?>" page="all"><i class="bi bi-list fw-bold"></i> Requetes Global</li>
                </ul>
            </div>

            <div id="side-down" class="justify-self-end">
                <ul>
                    <li id="logout"><i class="bi bi-box-arrow-left"></i> Deconnexion</li>
                </ul>
            </div>
        </section>

        <!-- MAIN CONTENT -->

        <section id="main-content">
            <div id="main-title" class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center justify-content-center">
                    <h3 class="fw-bold m-0 p-0 text-capitalize"><?= $headerTitle ?></h3>
                </div>
                <button class="btn btn-primary" id="open-request"><i class="bi bi-plus"></i> Nouvelle Requette</button>
            </div>

            <div class="d-flex align-items-center gap-5">
                <div id="main-search-params" class="col-sm-4">
                    <div class="form-group" style="width: auto;">
                        <span class="input-group-text" id="basic-addon1"><i class="bi bi-search"></i><span>
                                <?php $value = isset($_GET["search"]) ? $_GET["search"] : "" ?>
                                <input id="search-input" type="text" class="form-control" placeholder="Recherche par nom" value="<?= $value ?>">
                    </div>
                </div>
                <div id=search-button>
                    <a href="#" class="btn btn-primary">Rechercher</a>
                </div>
            </div>

            <div id="main-table">
                <table class="text-center">
                    <!-- Array HEADER -->
                    <tr id="main-table-header">

                        <?php $headerArray = $tableHeader[$sidebar];
                        foreach ($headerArray as $header) : ?>
                            <th class="text-uppercase"><?= $header ?></th>
                        <?php endforeach ?>

                    </tr>
                    <!-- END Array HEADER -->

                    <!-- Array body -->
                    <?php $splitRequestArrays = array_chunk($requestList, 5);
                    if (count($splitRequestArrays) > 0) : ?>
                        <!-- Cas de plusieur element dans array -->

                        <?php $pageInt = $pageInt >= count($splitRequestArrays) ? $pageInt = count($splitRequestArrays) : $pageInt;
                        foreach ($splitRequestArrays[$pageInt - 1] as $request) : ?>


                            <tr  requestid="<?= $request->getRequestId() ?>">
                                <td><?= $request->getLabel() ?></td>
                                <td><?= $request->getAddress() ?></td>
                                <td><?= $request->getCreatedAt() ?></td>
                                <td><?= $request->getDomainName() ?></td>

                                <?php if ($sidebar == "accepted") : ?>
                                    <td><?= $request->getRepairman()?->getUsername() ?? "N/A" ?></td>

                                <?php elseif ($sidebar == "all") : ?>
                                    <td><?= $request->getRequestStatus() ?></td>
                                    <td><?= $request->getRepairman()?->getUsername() ?? "N/A" ?></td>

                                <?php endif ?>
                            </tr>

                        <?php endforeach ?>

                    <?php else : ?>
                        <!-- Cas aucun elements trouver-->

                        <tr>
                            <td colspan=<?= count($headerArray) ?>> <span>Merde</span></td>
                        </tr>

                    <?php endif ?>

                </table>
            </div>
            <div id="main-page-nav" class="mt-auto <?php if (count($splitRequestArrays) == 0) echo "hidden-item" ?>">
                <ul>
                    <li><a page="1" class="btn btn-primary <?php if ($pageInt == 1) echo "disabled" ?>"><i class="bi bi-chevron-double-left"></i></a></li>

                    <?php for ($i = 1; $i <= count($splitRequestArrays); $i++) : ?>
                        <li>
                            <a page="<?= $i ?>" class=" btn <?php if ($pageInt == $i) {
                                                                echo "btn-primary disabled";
                                                            } else {
                                                                echo "btn-outline-primary";
                                                            } ?>"><?= $i ?></a>
                        </li>
                    <?php endfor ?>

                    <li><a page="<?= count($splitRequestArrays) ?>" class="btn btn-primary <?php if ($pageInt >= count($splitRequestArrays)) echo "disabled" ?>"><i class="bi bi-chevron-double-right"></i></a></li>
                </ul>
            </div>

        </section>
    </main>

    <!-- Modal pour remplir les donnees -->

    <div id="new-request-modal" class="hidden-item">
        <div>
            <h4 class="text-center text-primary fw-bold my-4">Nouvelle requete</h4>
            <form method="post" class="d-flex flex-column gap-5">
                <div id="formulaire">
                    <div class="form-group">
                        <label for="label" class="form-label fw-bold">Que voulez vous reparez :</label>
                        <input type="text" name="label" class="form-control" placeholder="Label" required>
                    </div>
                    <div class="form-group">
                        <label for="domain" class="form-label fw-bold">Domain :</label>
                        <select name="domain" id="domain" class="form-select" required>
                            <option value="electricite">Electricité</option>
                            <option value="mecanique">Mecanique</option>
                            <option value="plomberie">Plomberie</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label fw-bold">Description (optionel) :</label>
                        <textarea name="description" class="form-textarea" placeholder="Descrivez votre probleme"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="adress" class="form-label fw-bold">Adresse :</label>
                        <input type="text" name="address" class="form-control" placeholder="Adresse" required>
                    </div>
                </div>
                <div class="form-confirm d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- div d'affichage de message -->
    <?php if ($message != null) :
        $color = $message["success"] ? "alert-success" : "alert-danger";
        $text = $message["info"]
    ?>

        <div id="request-info" class="alert <?= $color ?> p-2">
            <span> <?= $text ?> </span>
        </div>

    <?php endif ?>
</body>

</html>