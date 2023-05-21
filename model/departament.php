<?php

require_once __DIR__ . '/categorie.php';

class Departament
{
    private int $id = -1; 
    public string $nume = '';

    public function __construct(int $id, string $nume)
    {
        $this->id = $id;

        if($nume != null)
            $this->nume = $nume;
    }

    public function iaId(): int { return $this->id; }

    public function __toString()
    {
        return "nume: $this->nume";
    }
}

?>