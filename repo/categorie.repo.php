<?php

require_once __DIR__ . '/../model/categorie.php';
require_once __DIR__ . '/../log/logging.php';

class CategorieRepo
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
            self::$logger->log("CategorieRepo::construct: nu s-a putut realiza conexiunea la baza de date");
            die('Could not connect to database' . mysqli_connect_error());
        }
    }

    public function iaCategorie(int $id): ?Categorie
    {
        self::$logger->log("CategorieRepo::iaCategorie: inceput rularea");

        $categorie = new Categorie(-1, '', '');

        $query = "SELECT * FROM categorii WHERE categorie_id='$id';";
        $rezultat = mysqli_query($this->conn, $query);
        $numarRezultate = mysqli_num_rows($rezultat);

        if($numarRezultate === 1)
        {
            $dateCategorie = mysqli_fetch_assoc($rezultat);

            $categorie = new Categorie(
                                        $dateCategorie['categorie_id'],
                                        $dateCategorie['nume'],
                                        $dateCategorie['departament']
                                    );
            
            self::$logger->log("CategorieRepo::iaCategorie: categorie luata cu succes");
            return $categorie;
        }

        if($numarRezultate === 0)
        {
            self::$logger->log("CategorieRepo::iaCategorie: categoria nu a fost gasita");
            return $categorie;
        }

        if($numarRezultate > 1)
        {
            self::$logger->log("CategorieRepo::iaCategorie: EROARE: gasit mai multe categorii cu acelasi id");
            return $categorie;
        }
    }

    public function iaToateCategoriile(): array
    {
        self::$logger->log("CategorieRepo::iaToateCategoriile: inceput rularea");

        $sirCategorii = [];

        $query = 'SELECT * FROM categorii';
        $rezultat = mysqli_query($this->conn, $query);

        if($rezultat !== false)
        {
            $categorieCurenta = new Categorie(-1, '', '');

            while($categorie = mysqli_fetch_assoc($rezultat))
            {
                $categorieCurenta = new Categorie(
                                                    $categorie['categorie_id'],
                                                    $categorie['nume'],
                                                    $categorie['departament']
                                                );

                array_push($sirCategorii, $categorieCurenta);
            }
        }
        else
            self::$logger->log("CategorieRepo::iaToateCategoriile: nu exista categorii in baza de date");

        return $sirCategorii;
    }

    public function iaCategoriiDepartament(string $departament): array
    {
        self::$logger->log("CategorieRepo::iaCategoriiDepartament: inceput rularea");

        $sirCategorii = [];

        if($departament != null)
        {
            self::$logger->log("CategorieRepo::iaCategoriiDepartament: departament este: $departament");
            $query = "SELECT * FROM categorii WHERE departament='$departament';";
            $rezultat = mysqli_query($this->conn, $query);

            if($rezultat !== false)
            {
                $categorieCurenta = new Categorie(-1, '', '');

                while($categorie = mysqli_fetch_assoc($rezultat))
                {
                    $categorieCurenta = new Categorie(
                                                        $categorie['categorie_id'],
                                                        $categorie['nume'],
                                                        $categorie['departament']
                                                    );
                    array_push($sirCategorii, $categorieCurenta);
                }
                // $numarCategorii = count($sirCategorii);
                // self::$logger->log("CategorieRepo::iaCategoriiDepartament: s-au gasit $numarCategorii categorii");
            }
            else
                self::$logger->log("CategorieRepo::iaCategoriiDepartament: nu exista categorii cu departamentul $departament");
        }
        else
            self::$logger->log('CategorieRepo::iaCategoriiDepartament: argumentul departament este nul');

        return $sirCategorii;
    }

    public function adaugaCategorie(Categorie $categorie): int
    {
        self::$logger->log("CategorieRepo::adaugaCategorie: inceput rularea");

        $categorieId = -1;

        if($categorie != null)
        {
            $query = "INSERT INTO categorii (nume, departament) VALUES ('$categorie->nume', '$categorie->departament');";
            $rezultat = mysqli_query($this->conn, $query);

            if($rezultat !== false)
            {
                $categorieId = mysqli_insert_id($this->conn);
                self::$logger->log("CategorieRepo::adaugaCategorie: categorie adaugata cu succes, id=$categorieId");
            }
            else
                self::$logger->log("CategorieRepo::adaugaCategorie: EROARE: categoria nu s-a putut adauga");
        }
        else
            self::$logger->log("CategorieRepo::adaugaCategorie: EROARE: argument nul: categorie");

        return $categorieId;
    }
}

?>