<?php

require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../log/logging.php';

class UtilizatorRepo
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
            self::$logger->log("UtilizatorRepo::construct: nu s-a putut realiza conexiunea la baza de date");
            die('Could not connect to database' . mysqli_connect_error());
        }
    }

    public function iaUtilizator(int $id): ?Utilizator
    {
        self::$logger->log("UtilizatorRepo::iaUtilizator: inceput rularea");

        $utilizator = new Utilizator(-1, '', '', '', '', RolUtilizator::Obisnuit);

        $query = "SELECT * FROM utilizatori WHERE utilizator_id='$id';";
        $rezultat = mysqli_query($this->conn, $query);
        $numarRezultate = mysqli_num_rows($rezultat);

        if($numarRezultate === 1)
        {
            $dateUtilizator = mysqli_fetch_assoc($rezultat);
            $rolUtilizator = RolUtilizator::Obisnuit;

            if($dateUtilizator['rol'] === 'Admin')
                $rolUtilizator = RolUtilizator::Admin;
            
            $utilizator = new Utilizator(
                                            $dateUtilizator['utilizator_id'],
                                            $dateUtilizator['nume'],
                                            $dateUtilizator['username'],
                                            $dateUtilizator['email'],
                                            $dateUtilizator['hash_parola'],
                                            $dateUtilizator['departament'],
                                            $rolUtilizator
                                        );
            self::$logger->log("UtilizatorRepo::iaUtilizator: utilizator luat cu succes");
            return $utilizator;
        }

        if($numarRezultate === 0)
        {
            self::$logger->log("UtilizatorRepo::iaUtilizator: utilizatorul nu a fost gasit");
            return null;
        }

        if($numarRezultate > 1)
        {
            self::$logger->log("UtilizatorRepo::iaUtilizator: EROARE: gasit mai multi utilizatori cu acelasi id");
            return null;
        }
    }

    public function iaTotiUtilizatorii(): array
    {
        self::$logger->log("UtilizatorRepo::iaTotiUtilizatorii: inceput rularea");

        $sirUtilizatori = [];

        $query = 'SELECT * from utilizatori';
        $result = mysqli_query($this->conn, $query);

        if($result !== false)
        {
            $utilizatorCurent = new Utilizator(-1, '', '', '', '', RolUtilizator::Obisnuit);

            while($utilizator = mysqli_fetch_array($result))
            {
                $utilizatorCurent = new Utilizator(
                                                    $utilizator['utilizator_id'],
                                                    $utilizator['nume'],
                                                    $utilizator['username'],
                                                    $utilizator['email'],
                                                    '', // parola nu se trimite mai departe catre browser
                                                    $utilizator['departament'],
                                                    RolUtilizator::Obisnuit
                                                    );

                array_push($sirUtilizatori, $utilizatorCurent);
            }
        }
        else
            self::$logger->log("UtilizatorRepo::iaTotiUtilizatorii: nu exista clienti in baza de date");

        return $sirUtilizatori;
    }

    // metoda ce adauga un 'utilizator' in baza de date (cu rol simplu 'Obisnuit', nu 'Admin')
    public function adaugaUtilizator(Utilizator $utilizator): int
    {
        self::$logger->log("UtilizatorRepo::adaugaUtilizator: inceput rularea");

        $utilizatorId = -1;

        if($utilizator != null)
        {
            $query = "INSERT INTO utilizatori (nume, email, hash_parola, departament, rol)
                        VALUES ('$utilizator->nume',
                                '$utilizator->email',
                                '$utilizator->hashParola',
                                '$utilizator->departament',
                                'Obisnuit');";

            $result = mysqli_query($this->conn, $query);

            if($result !== false)
            {
                $utilizatorId = mysqli_insert_id($this->conn);
                self::$logger->log("UtilizatorRepo::adaugaUtilizator: client adaugat cu succes, id = $utilizatorId");
            }
            else
                self::$logger->log("UtilizatorRepo::adaugaUtilizator: clientul nu s-a putut adauga");
        }
        else
            self::$logger->log("UtilizatorRepo::adaugaUtilizator: obiectul 'utilizator' este nul");

        return $utilizatorId;
    }

    // metoda ce verifica daca exista in baza de date un utilizator cu username-ul si parola specificate,
    // iar daca exista returneaza un obiect de tip Client cu informatiile din baza de date
    public function verificaUtilizator(string $email, string $parola): ?Utilizator
    {
        self::$logger->log("UtilizatorRepo::verificaUtilizator: inceput rularea");

        if($email != null and $parola != null)
        {
            $query = "SELECT * from utilizatori WHERE email='$email';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            if($numarRezultate === 1)
            {
                $dateUtilizator = mysqli_fetch_assoc($rezultat);

                $rol = RolUtilizator::Obisnuit;
                $rolString = $dateUtilizator['rol'];
                if($rolString === 'Admin')
                    $rol = RolUtilizator::Admin;
                if($rolString === 'Obisnuit')
                    $rol = RolUtilizator::Obisnuit;

                if( password_verify($parola, $dateUtilizator['hash_parola']) )
                {
                    $utilizator = new Utilizator(
                        $dateUtilizator['utilizator_id'],
                        $dateUtilizator['nume'],
                        $dateUtilizator['email'],
                        $dateUtilizator['hash_parola'],
                        $dateUtilizator['departament'],
                        $rol
                    );

                    self::$logger->log("UtilizatorRepo::verificaUtilizator: username si parola validate");
                    return $utilizator;
                }
                else
                {
                    self::$logger->log("UtilizatorRepo::verificaUtilizator: parola gresita");
                    return null;
                }

            }
            else
            {
                self::$logger->log("UtilizatorRepo::verificaUtilizator: username sau parola gresita");
                return null;
            }
        }
        else
        {
            self::$logger->log("UtilizatorRepo::verificaUtilizator: argumente nule sau goale");
            return null;
        }
    }

    public function contineUsername(string $nume): bool
    {
        self::$logger->log("UtilizatorRepo::contineUsername: inceput rularea");

        if($nume != null)
        {
            $query = "SELECT * from utilizatori WHERE username='$nume';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            // daca numele nu exista in baza de date
            if($numarRezultate === 0)
            {
                self::$logger->log("UtilizatorRepo::contineUsername: nu s-a gasit username-ul");
                return false;
            }
            else // daca numele exista deja in baza de date
            {
                self::$logger->log("UtilizatorRepo::contineUsername: username-ul a fost gasit");
                return true;
            }
        }
        else
        {
            self::$logger->log("UtilizatorRepo::contineUsername: argument nul: nume");
            return false; // numele nu exista in baza de date
        }
    }

    public function contineEmail(string $email): bool
    {
        self::$logger->log("ClientRepo::contineEmail: inceput rularea");

        if($email != null)
        {
            $query = "SELECT * from utilizatori WHERE nume='$email';";
            $rezultat = mysqli_query($this->conn, $query);
            $numarRezultate = mysqli_num_rows($rezultat);

            if($numarRezultate === 0)
            {
                self::$logger->log("UtilizatorRepo::contineEmail: nu s-a gasit email-ul");
                return false; // email-ul nu exista in baza de date
            }
            else
            {
                self::$logger->log("UtilizatorRepo::contineEmail: email-ul a fost gasit");
                return true; // email-ul exista deja in baza de date
            }
        }
        else
        {
            self::$logger->log("UtilizatorRepo::contineEmail: email-ul a fost gasit");
            return false; // email-ul nu exista in baza de date
        }
    }

    public function modificaNumeUtilizator(int $id, string $numeNou)
    {
        self::$logger->log("UtilizatorRepo::modificaNumeUtilizator: inceput rularea");

        if($numeNou != null)
        {
            $query = "UPDATE utilizatori SET nume='$numeNou' WHERE utilizator_id=$id;";
            $result = mysqli_query($this->conn, $query);
        }
        else
            self::$logger->log("UtilizatorRepo::modificaNumeUtilizator: EROARE argument nul");
    }

    public function modificaEmailUtilizator(int $id, string $emailNou)
    {
        self::$logger->log("UtilizatorRepo::modificaEmailUtilizator: inceput rularea");

        if($emailNou != null)
        {
            $query = "UPDATE utilizatori SET email='$emailNou' WHERE utilizator_id=$id;";
            $result = mysqli_query($this->conn, $query);
        }
        else
            self::$logger->log("UtilizatorRepo::modificaEmailUtilizator: EROARE argument nul");
    }

    public function modificaParolaUtilizator(int $id, string $hashNouParola)
    {
        self::$logger->log("UtilizatorRepo::modificaEmailUtilizator: inceput rularea");

        if($hashNouParola != null)
        {
            $query = "UPDATE utilizatori SET hash_parola='$hashNouParola' WHERE utilizator_id=$id;";
            $result = mysqli_query($this->conn, $query);
        }
        else
            self::$logger->log("UtilizatorRepo::modificaEmailUtilizator: EROARE argument nul");
    }
}
?>