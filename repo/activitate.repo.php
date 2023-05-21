<?php

require_once __DIR__ . '/../model/activitate.php';
require_once __DIR__ . '/../log/logging.php';

class ActivitateRepo
{
    private $conn;

    private static ?Logger $logger = null;

    public function __construct()
    {
        if(self::$logger == null)
            self::$logger = new Logger(__FILE__);
        
        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'tema13v2';
        
        $this->conn = mysqli_connect($servername, $username, $password, $dbname);

        if(!$this->conn)
        {
            self::$logger->log("ActivitateRepo::construct: nu s-a putut realiza conexiunea la baza de date");
            die('Could not connect to database' . mysqli_connect_error());
        }
    }

    public function iaActivitate(int $id): ?Activitate
    {
        self::$logger->log("ActivitateRepo::iaActivitate: inceput rularea");

        $data = date_create("now", null);
        $activitate = new Activitate(-1, '', $data, '', '', '', '');

        $query = "SELECT * from activitati WHERE id='$id';";
        $rezultat = mysqli_query($this->conn, $query);
        $numarRezultate = mysqli_num_rows($rezultat);

        if($numarRezultate === 1)
        {
            $dateActivitate = mysqli_fetch_assoc($rezultat);

            $activitate = new Activitate(
                $dateActivitate['activitate_id'],
                $dateActivitate['email_utilizator'],
                date_create($dateActivitate['data_timp'], null),
                $dateActivitate['departament'],
                $dateActivitate['categorie'],
                $dateActivitate['ore'],
                $dateActivitate['descriere']
            );

            self::$logger->log("ActivitateRepo::iaActivitate: activitate luata cu succes");
            return $activitate;
        }

        if($numarRezultate === 0)
        {
            self::$logger->log("ActivitateRepo::iaActivitate: activitatea nu a fost gasita");
            return null;
        }

        if($numarRezultate > 1)
        {
            self::$logger->log("ActivitateRepo::iaActivitate: EROARE: gasit mai multe activitati cu acelasi id");
            return null;
        }
    }

    public function iaToateActivitatile(): array
    {
        self::$logger->log("ActivitateRepo::iaToateActivitatile: inceput rularea");

        $sirActivitati = [];

        $query = 'SELECT * FROM activitati';
        $rezultat = mysqli_query($this->conn, $query);

        if($rezultat !== false)
        {
            $data = date_create("now", null);
            $activitateCurenta = new Activitate(-1, '', $data, '', '', '', '');

            while($activitate = mysqli_fetch_assoc($rezultat))
            {
                $activitateCurenta = new Activitate(
                    $activitate['activitate_id'],
                    $activitate['email_utilizator'],
                    date_create($activitate['data_timp'], null),
                    $activitate['departament'],
                    $activitate['categorie'],
                    $activitate['ore'],
                    $activitate['descriere']
                );
                array_push($sirActivitati, $activitateCurenta);
            }
            self::$logger->log("ActivitateRepo::iaToateActivitatile: toate activitatile luate cu succes");
        }
        else
            self::$logger->log("ActivitateRepo::iaToateActivitatile: nu exista activitati in baza de date");

        return $sirActivitati;
    }

    public function adaugaActivitate(Activitate $activitate): int
    {
        self::$logger->log("ActivitateRepo::adaugaActivitate: inceput rularea");

        $activitateId = -1;

        if($activitate != null)
        {
            $dataTimpSQL = $activitate->dataTimp->format('Y-m-d');
            self::$logger->log("ActivitateRepo::adaugaActivitate: DEBUG: descriere: $activitate->descriere");
            $query = 'INSERT INTO activitati (email_utilizator, data_timp, departament, categorie, ore, descriere) ' .
                        "VALUES ('$activitate->emailUtilizator', '$dataTimpSQL', '$activitate->departament', '$activitate->categorie', '$activitate->ore', '$activitate->descriere');";
            $rezultat = mysqli_query($this->conn, $query);      

            if($rezultat !== false)
            {
                $activitateId = mysqli_insert_id($this->conn);
                self::$logger->log("ActivitateRepo::adaugaActivitate: activitate adaugata cu succes, id=$activitateId");
            }
            else
                self::$logger->log("ActivitateRepo::adaugaActivitate: EROARE: activitatea nu s-a putu adauga");
        }
        else
            self::$logger->log("ActivitateRepo::adaugaActivitate: EROARE: argument nul");

        return $activitateId;
    }

    public function iaActivitatiUtilizator(string $emailUtilizator): array
    {
        $sirActivitati = [];

        if($emailUtilizator != null)
        {
            self::$logger->log("ActivitateRepo::iaActivitatiUtilizator: inceput rularea, email: $emailUtilizator");
            
            $query = "SELECT * FROM activitati WHERE email_utilizator='$emailUtilizator';";
            $rezultat = mysqli_query($this->conn, $query);
            // $numarRezultate = mysqli_num_rows($rezultat);
            // self::$logger->log("ActivitateRepo::iaActivitatiUtilizator: numar activitati gasite: $numarRezultate ");

            if($rezultat !== false)
            {
                $data = date_create("now", null);
                $activitateCurenta = new Activitate(-1, '', $data, '', '', 0, '');

                while($activitate = mysqli_fetch_assoc($rezultat))
                {
                    $activitateCurenta = new Activitate(
                                                            $activitate['activitate_id'],
                                                            $activitate['email_utilizator'],
                                                            date_create($activitate['data_timp'], null),
                                                            $activitate['departament'],
                                                            $activitate['categorie'],
                                                            $activitate['ore'],
                                                            $activitate['descriere']
                                                        );
                    array_push($sirActivitati, $activitateCurenta);
                }
            }
            else
                self::$logger->log("ActivitateRepo::iaActivitatiUtilizator: nu s-a gasit nici o activitate " .
                                    "pt. utilizator cu email: $emailUtilizator");
        }
        else
            self::$logger->log("ActivitateRepo::iaActivitatiUtilizator: EROARE: argument nul");

        return $sirActivitati;
    }
}

?>