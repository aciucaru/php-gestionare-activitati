<?php

class Activitate
{
    private int $id = -1;
    public DateTime $dataTimp;
    public string $emailUtilizator = '';
    public string $departament = '';
    public string $categorie = '';
    public int $ore = 0;
    public string $descriere = '';

    public function __construct(
        int $id,
        string $emailUtilizator,
        DateTime $dataTimp,
        string $departament,
        string $categorie,
        float $ore,
        string $descriere
    )
    {
        $this->id = $id;

        if($emailUtilizator != null)
            $this->emailUtilizator = $emailUtilizator;

        if($dataTimp != null)
            $this->dataTimp = $dataTimp;

        if($departament != null)
            $this->departament = $departament;

        if($categorie != null)
            $this->categorie = $categorie;

        if($ore >= 0)
            $this->ore = $ore;

        if($descriere != null)
            $this->descriere = $descriere;
    }

    public function iaId(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        return "{ 
                    id: $this->id, email: $this->emailUtilizator, departament: $this->departament, categorie: $this->categorie, ore: $this->ore
                    descriere: $this->descriere
                }";
    }

}

?>