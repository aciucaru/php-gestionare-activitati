<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/input-validation/user-input-validation.php';
require_once __DIR__ . '/repo/utilizator.repo.php';
require_once __DIR__ . '/service/manager-sesiune.php';
require_once __DIR__ . '/service/manager-setari-cont.php';
require_once __DIR__ . '/log/logging.php';

$managerSesiune = ManagerSesiune::iaSingleton();
$numeUtilizator = '';
$emailUtilizator = '';
$departamentUtilizator = '';
$rolUtilizator = '';

$managerSetariCont = ManagerSetariCont::iaSingleton();

// doar utilizatorii logati is pot vedea/gestiona setarile/datele personale
// deci se verifica daca exista un utilizator logat
if($managerSesiune->existaUtilizatorLogat())
{
    // se iau datele personale ce apartin utilizatorului logat
    $numeUtilizator = $managerSesiune->iaNumeUtilizator();
    $emailUtilizator = $managerSesiune->iaEmailUtilizator();
    $departamentUtilizator = $managerSesiune->iaNumeDepartamentUtilizator();
    $rolUtilizator = $managerSesiune->iaRolUtilizator()->value; 

    $managerSetariCont->incearcaModificareNume();
    $managerSetariCont->incearcaModificareEmail();
    $managerSetariCont->incearcaModificareParola();
}

?>

<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT School curs PHP: tema curs 13</title>
    <link rel="stylesheet" type="text/css" href="./styles/general.css" />
    <link rel="stylesheet" type="text/css" href="./styles/navbar.css" />
    <link rel="stylesheet" type="text/css" href="./styles/footer.css" />
    <link rel="stylesheet" type="text/css" href="./styles/form-input-general.css" />
    <link rel="stylesheet" type="text/css" href="./styles/setari-cont.css" />
</head>

<body>
    <?php require('./templates/navbar.template.php') ?>

    <div>
        <div class="property-editor">
            <div class="property-name inline-block-item">Departament</div>
            <div class="property-value inline-block-item"> <?php echo $departamentUtilizator?> </div>
        </div>

        <hr class="horizontal-line">

        <div class="property-editor">
            <div class="property-name inline-block-item">Rol</div>
            <div class="property-value inline-block-item"> <?php echo $rolUtilizator ?> </div>
        </div>

        <hr class="horizontal-line">

        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="property-editor">
                <div class="property-name inline-block-item">Nume</div>
                <div class="property-value inline-block-item"> <?php echo $numeUtilizator ?> </div>

                <input type="checkbox" id="checkbox-modificare-nume" class="collapsible-toggle">
                <label for="checkbox-modificare-nume" class="collapsible-toggle-label property-edit-button">Modifica nume</label>

                <div class="property-edit-collapsible-content-row">
                    <label class="input-label property-name">Nume nou</label>
                    <input type="text" name="nume-modificat" placeholder="Nume nou" class="property-value">
                    <div class="col-separator"></div>
                    <input type="submit" name="submit-modificare-nume" value="Aplica modificare" class="property-edit-button"/>
                </div>
            </div>
        </form>

        <hr class="horizontal-line">

        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="property-editor">
                <div class="property-name inline-block-item">Email</div>
                <div class="property-value inline-block-item"> <?php echo $emailUtilizator ?> </div>

                <input type="checkbox" id="checkbox-modificare-email" class="collapsible-toggle">
                <label for="checkbox-modificare-email" class="collapsible-toggle-label">Modifica email</label>

                <div class="property-edit-collapsible-content-row">
                    <label class="input-label property-name">Email</label>
                    <input type="text" name="email-modificat" placeholder="Email" class="property-value">
                    <div class="col-separator"></div>
                    <input type="submit" name="submit-modificare-email" value="Aplica modificare" class="property-edit-button"/>
                </div>
            </div>
        </form>

        <hr class="horizontal-line">

        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="property-editor">
                <div class="property-name inline-block-item">Parola</div>
                <div class="property-value inline-block-item">****</div>

                <input type="checkbox" id="checkbox-modificare-parola" class="collapsible-toggle">
                <label for="checkbox-modificare-parola" class="collapsible-toggle-label">Modifica parola</label>

                <div class="property-edit-collapsible-content-column">
                    <div class="property-edit-row-content inline-block-item">
                        <label class="input-label property-name">Parola</label>
                        <input type="password" name="parola-modificata1" placeholder="Parola" class="property-value">
                    <div>

                    <div class="property-edit-row-content inline-block-item">
                        <label class="input-label property-name">Repeta parola</label>
                        <input type="password" name="parola-modificata2" placeholder="Parola" class="property-value">
                        <div class="col-separator"></div>
                        <input type="submit" name="submit-modificare-parola" value="Aplica modificare" class="property-edit-button"/>
                    <div>
                </div>
            </div>
        </form>
    </div>

    <?php require('./templates/footer.template.php') ?>
</body>

</html>