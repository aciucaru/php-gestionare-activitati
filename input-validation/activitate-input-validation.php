<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../model/departament.php';
require_once __DIR__ . '/../model/activitate.php';

require_once __DIR__ . '/../log/logging.php';


enum TipInputActivitate: string
{
    case ActivitateData = 'ActivitateData';
    case ActivitateCategorie = 'ActivitateCategorie';
    case ActivitateOre = 'ActivitateOre';
    case ActivitateDescriere = 'ActivitateDescriere';
}

// clasa de baza folosita ca tip comun pentru diferitele tipuri de reguli de validare
class ReguliValidareActivitate
{
    // daca input-ul este obligatoriu sau nu, propr. comuna pt. toate input-urile
    public bool $campObligatoriu = true;
}

class ReguliValidareActivitateData extends ReguliValidareActivitate
{
    public int $maxZileDinUrma = 14; // nu mai mult de 2 saptamani (14 zile)
}

class ReguliValidareActivitateCategorie extends ReguliValidareActivitate
{
    public int $lungimeMinima = 0;
    public int $lungimeMaxima = 255;
}

class ReguliValidareActivitateOre extends ReguliValidareActivitate
{
    public int $valoareMinima = 0;
    public int $valoareMaxima = 8;
}

class ReguliValidareActivitateDescriere extends ReguliValidareActivitate
{
    public int $lungimeMinima = 0;
    public int $lungimeMaxima = 255;
}

function valideazaInputActivitateData(string $numeCamp, ReguliValidareActivitateData $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputActivitateData: inceput rularea, validare: $numeCamp");

    if ($reguliValidare != null)
    {
        if ($reguliValidare->campObligatoriu)
        {
            if ($numeCamp != null)
            {
                if(isset($_POST[$numeCamp]) && !empty($_POST[$numeCamp]))
                {
                    $dataString = date('Y-m-d', strtotime($_POST[$numeCamp]));
                    $dataCurentaString = date('Y-m-d'); // data curenta

                    $data = new DateTime($dataString);
                    $dataCurenta = new DateTime($dataCurentaString);
                    $diferentaZile = (int) $data->diff($dataCurenta)->format("%r%a");

                    // daca data este in viitor (mai noua decat ziua de curenta)
                    if($diferentaZile < 0)
                    {
                        $logger->log("valideazaInputActivitateData: $numeCamp este o data mai noua decat ziua curenta");
                        return false;
                    }

                    // daca data este in trecut dar mai veche de 'maxZileDinUrma' zile fata de ziua curenta
                    if($diferentaZile > $reguliValidare->maxZileDinUrma)
                    {
                        $logger->log("valideazaInputActivitateData: $numeCamp nu exista in POST");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputActivitateData: $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputUsername: $numeCamp este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

function valideazaInputActivitateCategorie(string $numeCamp, ReguliValidareActivitateCategorie $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputActivitateCategorie: inceput rularea, validare: $numeCamp");

    if ($reguliValidare != null)
    {
        if ($reguliValidare->campObligatoriu)
        {
            if ($numeCamp != null)
            {
                if(isset($_POST[$numeCamp]) && !empty($_POST[$numeCamp]))
                {
                    // curatam inputul de posibile atacuri XSS
                    $camp = htmlspecialchars($_POST[$numeCamp]);

                    // 1. verificare prima regula: lungimea minima
                    if (strlen($camp) < $reguliValidare->lungimeMinima)
                    {
                        $logger->log("valideazaInputActivitateCategorie: $numeCamp este prea scurt");
                        return false;
                    }

                    // 2. verificare prima regula: lungimea maxima
                    if (strlen($camp) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputActivitateCategorie: $numeCamp este prea lung");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputActivitateCategorie: $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputActivitateCategorie: $numeCamp este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

function valideazaInputActivitateOre(string $numeCamp, ReguliValidareActivitateOre $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputActivitateOre: inceput rularea, validare: $numeCamp");

    if ($reguliValidare != null)
    {
        if ($reguliValidare->campObligatoriu)
        {
            if($numeCamp != null)
            {
                if(isset($_POST[$numeCamp]) && !empty($_POST[$numeCamp]))
                {
                    $numarOre = $_POST[$numeCamp];

                    // daca data este in viitor (mai noua decat ziua de curenta)
                    if($numarOre < $reguliValidare->valoareMinima)
                    {
                        $logger->log("valideazaInputActivitateOre: $numeCamp este mai mic decat valoare minima");
                        return false;
                    }

                    // daca data este in trecut dar mai veche de 14 zile fata de ziua curenta
                    if($numarOre > $reguliValidare->valoareMaxima)
                    {
                        $logger->log("valideazaInputActivitateOre: $numeCamp este mai mare decat valoare maxima");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputActivitateOre: $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputActivitateOre: $numeCamp este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

function valideazaInputActivitateDescriere(string $numeCamp, ReguliValidareActivitateDescriere $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputActivitateDescriere: inceput rularea, validare: $numeCamp");

    if ($reguliValidare != null)
    {
        if ($reguliValidare->campObligatoriu)
        {
            if ($numeCamp != null)
            {
                if(isset($_POST[$numeCamp]) && !empty($_POST[$numeCamp]))
                {
                    // curatam inputul de posibile atacuri XSS
                    $camp = htmlspecialchars($_POST[$numeCamp]);

                    // 1. verificare prima regula: lungimea minima
                    if (strlen($camp) < $reguliValidare->lungimeMinima)
                    {
                        $logger->log("valideazaInputActivitateDescriere: $numeCamp este prea scurt");
                        return false;
                    }

                    // 2. verificare prima regula: lungimea maxima
                    if (strlen($camp) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputActivitateDescriere: $numeCamp este prea lung");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputActivitateDescriere: $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputActivitateDescriere: $numeCamp este nul sau gol");
                return false;
            }
        }
        else
            // daca acest camp nu este obligatoriu, atunci teoretic campul este valid
            return true;
    }
    else
        // daca nu s-au specificat reguli de validare, atunci teoretic campul este valid
        return true;
}

// functie ce valideaza toate input-urile necesare paginii 'activitati'
function valideazaInputuriActivitate(): ?Activitate
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriActivitate: inceput rularea");

    $data = new DateTime('now');
    $categorie = '';
    $ore = 0.0;
    $descriere = '';

    $inputurileSuntValide = true;

    $inputuriDeValidat =
    [
        'data' =>
        [
            'tip' => TipInputActivitate::ActivitateData,
            'reguliValidare' => new ReguliValidareActivitateData()
        ],

        'categorie' =>
        [
            'tip' => TipInputActivitate::ActivitateCategorie,
            'reguliValidare' => new ReguliValidareActivitateCategorie()
        ],

        'ore' =>
        [
            'tip' => TipInputActivitate::ActivitateOre,
            'reguliValidare' => new ReguliValidareActivitateOre()
        ],

        'descriere' =>
        [
            'tip' => TipInputActivitate::ActivitateDescriere,
            'reguliValidare' => new ReguliValidareActivitateDescriere()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputActivitate::ActivitateData:
                if(valideazaInputActivitateData($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $dataString = date('Y-m-d', strtotime($_POST[$numeInput]));
                    $data = new DateTime($dataString);
                }
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputActivitate::ActivitateCategorie:
                if(valideazaInputActivitateCategorie($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $categorie = htmlspecialchars($_POST[$numeInput]);
                }
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputActivitate::ActivitateOre:
                if(valideazaInputActivitateOre($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $ore = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputActivitate::ActivitateDescriere:
                if(valideazaInputActivitateDescriere($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $descriere = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true)
    {
        $logger->log("valideazaInputuriActivitate: validare cu succes");

        $managerSesiune = ManagerSesiune::iaSingleton();

        $activitateNoua = new Activitate(
                                            -1,
                                            $managerSesiune->iaEmailUtilizator(),
                                            $data,
                                            $managerSesiune->iaNumeDepartamentUtilizator(),
                                            $categorie,
                                            $ore,
                                            $descriere
                                        );

        return $activitateNoua;
    }
    else
    {
        $logger->log("valideazaInputuriActivitate: validare esuata");
        return null;
    }
}

?>