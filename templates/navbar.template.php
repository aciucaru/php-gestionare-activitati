<?php

require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../service/manager-sesiune.php';

$managerSesiune = ManagerSesiune::iaSingleton();

?>

<style>
<?php include './styles/navbar.css'; ?>
</style>

<nav class="navbar" role="navigation">
    <div class="navbar-left-side">
        <?php if($managerSesiune->iaRolUtilizator() === RolUtilizator::Obisnuit): ?>
            <div class="navbar-item">
                <a class="navbar-link" href="./activitati.php">Activitati</a>
            </div>
        <?php endif ?>

        <?php if($managerSesiune->iaRolUtilizator() === RolUtilizator::Admin): ?>
            <div class="navbar-item">
                <a class="navbar-link" href="./activitati.php">Activitati</a>
            </div>

            <div class="navbar-item">
                <a class="navbar-link" href="./activitati.php">Departamente/Categorii</a>
            </div>
        <?php endif ?>
    </div>

    <div class="navbar-right-side">
        <?php if($managerSesiune->existaUtilizatorLogat()): ?>
            <div class="navbar-item">
                <a class="navbar-link" href="#">
                    <img src="./styles/user-solid.svg" class="user-item-image">
                </a>

                <ul class="dropdown-menu">
                    <li class="dropdown-item">
                        <a class="navbar-link" href="./setari-cont.php">
                            Setari cont
                        </a>
                    </li>

                    <li class="dropdown-item">
                        <a class="navbar-link" href="./logout.php">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <div class="navbar-item ">
                <a class="navbar-link" href="./login.php">Login</a>
            </div>

            <div class="navbar-item">
                <a class="navbar-link" href="./register.php">Inregistrare</a>
            </div>
        <?php endif ?>
    </div>

</nav>