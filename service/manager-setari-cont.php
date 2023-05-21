<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../input-validation/user-input-validation.php';
require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../repo/utilizator.repo.php';
require_once __DIR__ . '/../service/manager-sesiune.php';
require_once __DIR__ . '/../log/logging.php';

class ManagerSetariCont
{
    private static ?ManagerSetariCont $instanta = null;
    private static ?UtilizatorRepo $repoUtilizator = null;

    private static ?Logger $logger = null;

    private function __construct()
    {
        if(self::$logger == null)
            self::$logger = new Logger(__FILE__);
        
        if(self::$repoUtilizator == null)
            self::$repoUtilizator = new UtilizatorRepo();
    }

    public static function iaSingleton(): ManagerSetariCont
    {
        if(self::$instanta == null)
            self::$instanta = new ManagerSetariCont();

        session_start(); // aceasta metoda porneste si sesiunea
        return self::$instanta;
    }

    public function incearcaModificareNume()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-modificare-nume']))
        {
            // functia de validare returneaza un string cu noul nume sau, daca validarea esueaza, atunci returneaza null
            $numeNou = valideazaInputModificareNume();

            // daca validarea a avut succes
            if($numeNou != null)
            {
                $managerSesiune = ManagerSesiune::iaSingleton();
                $idUtilizatorCurent = $managerSesiune->iaIdUtilizator();
                self::$repoUtilizator->modificaNumeUtilizator($idUtilizatorCurent, $numeNou);

                $managerSesiune = ManagerSesiune::iaSingleton();
                $managerSesiune->actualizeazaNume($numeNou);

                // se redirectioneaza catre aceeasi pagina pt. a vedea imediat modificarea
                header('Location:setari-cont.php');
            }
            else
                self::$logger->log('ManagerSetariCont::incearcaModificareNume: client este nul, validarea nu a avut succes');
        }
        else
            self::$logger->log('ManagerSetariCont::incearcaModificareNume: nu s-a facut POST sau input-ul submit lipseste');
    }

    public function incearcaModificareEmail()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-modificare-email']))
        {
            // functia de validare returneaza un string cu noul nume sau, daca validarea esueaza, atunci returneaza null
            $emailNou = valideazaInputModificareEmail();

            // daca validarea a avut succes
            if($emailNou != null)
            {
                $managerSesiune = ManagerSesiune::iaSingleton();
                $idUtilizatorCurent = $managerSesiune->iaIdUtilizator();
                self::$repoUtilizator->modificaEmailUtilizator($idUtilizatorCurent, $emailNou);

                $managerSesiune = ManagerSesiune::iaSingleton();
                $managerSesiune->actualizeazaEmail($emailNou);

                // se redirectioneaza catre aceeasi pagina pt. a vedea imediat modificarea
                header('Location:setari-cont.php');
            }
            else
                self::$logger->log('ManagerSetariCont::incearcaModificareEmail: client este nul, validarea nu a avut succes');
        }
        else
            self::$logger->log('ManagerSetariCont::incearcaModificareEmail: nu s-a facut POST sau input-ul submit lipseste');
    }

    public function incearcaModificareParola()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-modificare-parola']))
        {
            // functia de validare returneaza un string cu noul nume sau, daca validarea esueaza, atunci returneaza null
            $hashNouParola = valideazaInputuriModificareParola();

            // daca validarea a avut succes
            if($hashNouParola != null)
            {
                $managerSesiune = ManagerSesiune::iaSingleton();
                $idUtilizatorCurent = $managerSesiune->iaIdUtilizator();
                self::$repoUtilizator->modificaParolaUtilizator($idUtilizatorCurent, $hashNouParola);

                // se redirectioneaza catre aceeasi pagina pt. a vedea imediat modificarea
                header('Location:setari-cont.php');
            }
            else
                self::$logger->log('ManagerSetariCont::incearcaModificareParola: client este nul, validarea nu a avut succes');
        }
        else
            self::$logger->log('ManagerSetariCont::incearcaModificareParola: nu s-a facut POST sau input-ul submit lipseste');
    }
}

?>