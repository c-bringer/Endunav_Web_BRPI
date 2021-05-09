<?php
$numberAccount = require_once("api/v1/dashboard/GetNumberAccount.php");
$accounts = require_once("api/v1/dashboard/GetAccount.php");
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Dashboard - Endunav</title>
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <img class="bi me-2" height="32" src="img/logo/endunav_orange.svg" alt="Logo Endunav">
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li class="px-2">Dashboard</li>
                </ul>

                <div class="text-end">
                    <button type="button" class="btn btn-warning">Déconnexion</button>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total de comptes</h5>
                        <p class="card-text">Ce nombre représente le nombres total de comptes sans prendre en compte le
                            status.</p>
                        <span class="p-2 card-number"><?php echo $numberAccount[0][0]; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Comptes activés</h5>
                        <p class="card-text">Ce nombre représente le nombre de comptes activés.</p>
                        <span class="p-2 card-number"><?php echo $numberAccount[0][1]; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Comptes désactivés</h5>
                        <p class="card-text">Ce nombre représente le nombre de comptes désactivés.</p>
                        <span class="p-2 card-number"><?php echo $numberAccount[0][2]; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-5">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom complet</th>
                        <th>Adresse mail</th>
                        <th>Status</th>
                        <th>Administration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($accounts[0] as $account) {
                            echo "<tr>";
                            echo "<td>" . $account['id'] . "</td>";
                            echo "<td>" . $account['name'] . "</td>";
                            echo "<td>" . $account['email'] . "</td>";
                            echo "<td>" . $account['status'] . "</td>";
                            echo "<td>";
                            echo '<form method="POST" action="api/v1/dashboard/UpdateAccountStatus.php?email=';
                            echo $account['email'];
                            echo '">';
                            echo '<input style="margin-right: 10px;" type="submit" name="action" value="Activer" class="btn btn-primary">';
                            echo '<input type="submit" name="action" value="Désactiver" class="btn btn-secondary">';
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>