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
$managerSesiune->incearcaLogin();

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
    <link rel="stylesheet" type="text/css" href="./styles/login.css" />
</head>

<body>
    <?php require('./templates/navbar.template.php') ?>

    <div>
        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="form-login">
                <label class="input-email-label input-label">Email</label>
                <input type="text" name="email" placeholder="Email" class="input-email">

                <label class="input-parola-label input-label">Parola</label>
                <input type="password" name="parolaLogin" placeholder="Parola" class="input-parola">

                <input type="submit" name="submit-login" value="Login" class="submit-login"/>
            </div>
        </form>
    </div>

    <?php require('./templates/footer.template.php') ?>
</body>

</html>