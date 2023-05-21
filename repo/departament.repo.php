<?php

require_once __DIR__ . '/../model/departament.php';
require_once __DIR__ . '/../log/logging.php';

class DepartamentRepo
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
            self::$logger->log("DepartamentRepo::construct: nu s-a putut realiza conexiunea la baza de date");
            die('Could not connect to database' . mysqli_connect_error());
        }
    }

    public function iaDepartament(int $id): ?Departament
    {
        self::$logger->log("DepartamentRepo::iaDepartament: inceput rularea");

        $departament = new Departament(-1, '');

        $query = "SELECT * from departamente WHERE departament_id='$id';";
        $rezultat= mysqli_query($this->conn, $query);
        $numarRezultate = mysqli_num_rows($rezultat);

        if($numarRezultate === 1)
        {
            $dateDepartament = mysqli_fetch_assoc($rezultat);

            $departament = new Departament(
                                            $dateDepartament['departament_id'],
                                            $dateDepartament['nume']
                                            );

            self::$logger->log('DepartamentRepo::iaDepartament: departament luat cu succes');
            return $departament;
        }

        if($numarRezultate === 0)
        {
            self::$logger->log('DepartamentRepo::iaDepartament: departamentul nu a fost gasit');
            return null;
        }

        if($numarRezultate > 1)
        {
            self::$logger->log('DepartamentRepo::iaDepartament: EROARE: gasit mai multe departamente cu acelasi id');
            return null;
        }
    }

    public function iaToateDepartamentele(): array
    {
        self::$logger->log("DepartamentRepo::iaToateDepartamentele: inceput rularea");

        $sirDepartamente = [];

        $query = 'SELECT * from departamente';
        $rezultat = mysqli_query($this->conn, $query);

        if($rezultat !== false)
        {
            $departamentCurent = new Departament(-1, '');

            while($departament = mysqli_fetch_array($rezultat))
            {
                $departamentCurent = new Departament(
                                                        $departament['departament_id'],
                                                        $departament['nume']
                                                    );
                                                    
                array_push($sirDepartamente, $departamentCurent);
            }
        }
        else
            self::$logger->log("DepartamentRepo::iaToateDepartamentele: nu exista departamente in baza de date");

        return $sirDepartamente;
    }

    // metoda ce adauga un 'utilizator' in baza de date (cu rol simplu 'Obisnuit', nu 'Admin')
    public function adaugaDepartament(Departament $departament): int
    {
        self::$logger->log("DepartamentRepo::adaugaDepartament: inceput rularea");

        $departamentId = -1;

        if($departament != null)
        {
            $query = "INSERT INTO departamente (nume) VALUES ('$departament->nume');";
            $result = mysqli_query($this->conn, $query);

            if($result !== false)
            {
                $departamentId = mysqli_insert_id($this->conn);
                self::$logger->log("DepartamentRepo::adaugaDepartament: departament adaugat cu succes, id = $departamentId");
            }
            else
                self::$logger->log("DepartamentRepo::adaugaDepartament: EROARE: departamentul nu s-a putut adauga");
        }
        else
            self::$logger->log("DepartamentRepo::adaugaDepartament: EROARE: argumentul 'departament' este nul");

        return $departamentId;
    }

    public function contineDepartament(string $nume): bool
    {
        self::$logger->log("DepartamentRepo::contineDepartament: inceput rularea");

        if($nume != null)
        {
            $query = "SELECT * from departamente WHERE nume='$nume';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            // daca departamentul nu exista in baza de date
            if($numarRezultate === 0)
            {
                self::$logger->log("UtilizatorRepo::contineDepartament: nu s-a gasit departamentul");
                return false;
            }

            if($numarRezultate === 1)
            {
                self::$logger->log("UtilizatorRepo::contineDepartament: departamentul a fost gasit");
                return true;
            }

            if($numarRezultate > 1)
            {
                self::$logger->log('UtilizatorRepo::contineDepartament: EROARE: s-au gasit mai multe departamente');
                return false;
            }
        }
        else
        {
            self::$logger->log('UtilizatorRepo::contineDepartament: EROARE: argument nul: nume');
            return true;
        }
    }
}

?>