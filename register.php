<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/input-validation/user-input-validation.php';
require_once __DIR__ . '/repo/utilizator.repo.php';
require_once __DIR__ . '/service/manager-sesiune.php';
require_once __DIR__ . '/log/logging.php';

$managerSesiune = ManagerSesiune::iaSingleton();
$managerSesiune->incearcaRegister();

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
    <link rel="stylesheet" type="text/css" href="./styles/register.css" />
</head>

<body>
    <?php require('./templates/navbar.template.php') ?>

    <div>
        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="form-register">
                <label class="input-nume-label input-label">Nume</label>
                <input type="text" name="nume" placeholder="Numele" class="input-nume">

                <label class="input-email-label input-label">Email</label>
                <input type="text" name="email" placeholder="Email" class="input-email">

                <label class="input-parola1-label input-label">Parola</label>
                <input type="password" name="parolaRegister1" placeholder="Parola" class="input-parola1">

                <label class="input-parola2-label input-label">Repeta parola</label>
                <input type="password" name="parolaRegister2" placeholder="Parola" class="input-parola2">

                <input type="submit" name="submit-register" value="Inregistrare" class="submit-register"/>
            </div>
        </form>
    </div>

    <?php require('./templates/footer.template.php') ?>
</body>

</html>