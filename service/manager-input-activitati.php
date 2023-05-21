<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/activitate.php';
require_once __DIR__ . '/../repo/activitate.repo.php';
require_once __DIR__ . '/../service/manager-sesiune.php';
require_once __DIR__ . '/../input-validation/activitate-input-validation.php';
require_once __DIR__ . '/../log/logging.php';

class ManagerInputActivitati
{
    private static $instanta = null;

    private static ?Logger $logger;

    private function __construct()
    {
        if(self::$logger == null)
            self::$logger = new Logger(__FILE__);
    }

    public static function iaSingleton(): ManagerInputActivitati
    {
        if(self::$instanta == null)
            self::$instanta = new ManagerInputActivitati();

        session_start(); // aceasta metoda porneste si sesiunea
        return self::$instanta;
    }

    public function incearcaAdaugareActivitate()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-activitate']))
        {
            $repoActivitati = new ActivitateRepo();

            $activitateNoua = valideazaInputuriActivitate();

            if($activitateNoua != null)
            {
                $repoActivitati->adaugaActivitate($activitateNoua);
                
                // se redirectioneaza catre aceeasi pagina pt. a vedea imediat modificarea
                header('Location:activitati.php');
            }
        }
        else
            self::$logger->log('ManagerInputActivitati::incearcaAdaugareActivitate: nu s-a facut POST sau input-ul submit lipseste');
    }

    // metoda ce genereaza data maxima din trecut la ca utilizatorul incam ai poate adauga acitivitati
    // ca o conventie, aceasta data din tercut este generata scazand 14 zile din data curenta
    public function iaDataCurenta(): string
    {
        self::$logger->log("ManagerInputActivitati::iaDataCurenta: inceput rularea");

        // data curenta
        $dataCurenta = new DateTime(date('Y-m-d'));
        // self::$logger->log("ManagerInputActivitati::iaDataCurenta: data curenta: $dataCurenta");

        return $dataCurenta->format('Y-m-d');
    }

    // metoda ce genereaza data maxima din trecut la care utilizatorul inca mai poate adauga activitati
    // ca o conventie, aceasta data din trecut este generata scazand 14 zile din data curenta
    public function iaDataMaximaDinTrecut(): string
    {
        self::$logger->log("ManagerInputActivitati::iaDataMaximaDinTrecut: inceput rularea");

        // data curenta
        $dataCurenta = new DateTime(date('Y-m-d'));
        // self::$logger->log("ManagerInputActivitati::iaDataMaximaDinTrecut: data curenta: $dataCurenta");

        // $data maxima din trecut 
        $dataMaximaDinTrecut = date_modify($dataCurenta, '-14 day');
        // self::$logger->log("ManagerInputActivitati::iaDataMaximaDinTrecut: data trecut: $dataMaximaDinTrecut");

        return $dataMaximaDinTrecut->format('Y-m-d');
    }

    // metoda ce genereaza tot codul input-ului pt. data la care utilizatorul adauga  activitatea
    // adica acest input nu va fi scris in HTML, ci va fi generat direct de aceasta metoda cu 'echo'
    // metoda este necesara deoarece input-ul de tip 'data' trebuie sa aiba o data minima si o data maxima
    public function genereazaInputData()
    {
        self::$logger->log("ManagerInputActivitati::genereazaInputData: inceput rularea");

        // echo '<option value="' . $categorie->nume .'">' . $categorie->nume . '</option>';
        echo '<input type="date" name="data" class="input-data"'
                . 'value="' . $this->iaDataCurenta() . '"'
                . 'min="' . $this->iaDataMaximaDinTrecut() . '"'
                . 'max="' . $this->iaDataCurenta() . '"'
            . '>';
    }
}

?>