<?php

class Categorie
{
    private int $id = -1;
    public string $nume = '';
    public string $departament = '';

    public function __construct(int $id, string $nume, string $departament)
    {
        $this->id = $id;

        if($nume != null)
            $this->nume = $nume;

        if($departament != null)
            $this->departament = $departament;
    }

    public function iaId(): int { return $this->id; }

    public function __toString()
    {
        return "{ nume: $this->nume, departament: $this->departament }";
    }
}

?>