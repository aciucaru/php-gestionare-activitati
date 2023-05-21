<?php

require_once __DIR__ . '/departament.php';

enum RolUtilizator: string
{
    case Obisnuit = 'Obisnuit';
    case Admin = 'Admin';
}

class Utilizator
{
    private int $id = -1;
    public string $nume = '';
    public string $email = '';
    public string $hashParola = '';
    public string $departament = '';
    public RolUtilizator $rol = RolUtilizator::Obisnuit;

    public function __construct(
                                    int $id,
                                    string $nume,
                                    string $email,
                                    string $hashParola,
                                    string $departament,
                                    RolUtilizator $rol
                                )
    {
        $this->id = $id;

        if($nume != null)
            $this->nume = $nume;

        if($email != null)
            $this->email = $email;

        if($hashParola != null)
            $this->hashParola = $hashParola;

        if($departament != null)
            $this->departament = $departament;

        if($rol != null)
            $this->rol = $rol;
    }

    public function iaId(): int { return $this->id; }

    public function __toString()
    {

    }
}

?>