<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../model/departament.php';

require_once __DIR__ . '/../log/logging.php';

enum TipInputUtilizator: string
{
    case Nume = 'Nume';
    case Username = 'Username';
    case Email = 'Email';
    case ParolaLogin = 'ParolaLogin';
    case ParolaRegister1 = 'ParolaRegister1';
    case ParolaRegister2 = 'ParolaRegister2';
}

// clasa de baza folosita ca tip comun pentru diferitele tipuri de reguli de validare
class ReguliValidareUtilizator
{
    // daca input-ul este obligatoriu sau nu, propr. comuna pt. toate input-urile
    public bool $campObligatoriu = true;
}

class ReguliValidareNume extends ReguliValidareUtilizator
{
    public int $lungimeMinima = 3;
    public int $lungimeMaxima = 255;
}

class ReguliValidareUsername extends ReguliValidareUtilizator
{
    public int $lungimeMinima = 3;
    public int $lungimeMaxima = 255;
}

class ReguliValidareParola extends ReguliValidareUtilizator
{
    public int $lungimeMinima = 4; // prea mic, e doar de exemplu
    public int $lungimeMaxima = 255;
}

function valideazaInputNume(string $numeCamp, ReguliValidareNume $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);

    $logger->log("valideazaInputNume: inceput rularea, validare: $numeCamp");

    if (isset($reguliValidare))
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
                        $logger->log("valideazaInputNume: $numeCamp este prea scurt");
                        return false;
                    }

                    // 2. verificare prima regula: lungimea maxima
                    if (strlen($camp) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputNume: $numeCamp este prea lung");
                        return false;
                    }

                    // 3. verificare daca string-ul contine caractere speciale (interzise pt. nume de persoane)
                    if(strpbrk($camp, ',;-()[]{}~!@#$%^&*?') != false)
                    {
                        $logger->log("valideazaInputUsername: $numeCamp contine caractere ilegale (,;-()[]{}~!@#$%^&*?)");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputNume: input $numeCamp nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputNume: $numeCamp este nul sau gol");
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

function valideazaInputEmail(string $numeCamp, ReguliValidareUtilizator $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputEmail: inceput rularea, validare: $numeCamp");

    if (isset($reguliValidare))
    {
        if ($reguliValidare->campObligatoriu)
        {
            if ($numeCamp != null)
            {
                if(isset($_POST[$numeCamp]) && !empty($_POST[$numeCamp]))
                {
                    // curatam inputul de posibile atacuri XSS
                    $camp = htmlspecialchars($_POST[$numeCamp]);

                    if(filter_var($camp, FILTER_VALIDATE_EMAIL) === false)
                    {
                        $logger->log("valideazaInputEmail: $numeCamp nu este o adresa de email valida");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputEmail: $numeCamp nu exista in POST");
                    return false;
                }
            }
            else
            {
                $logger->log("valideazaInputEmail: $numeCamp: este obligatoriu");
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

function valideazaInputParola(string $numeCampParola, ReguliValidareParola $reguliValidare): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputParola: inceput rularea, validare: $numeCampParola");

    if (isset($reguliValidare))
    {
        if ($reguliValidare->campObligatoriu)
        {
            if ($numeCampParola != null)
            {
                if(isset($_POST[$numeCampParola]) && !empty($_POST[$numeCampParola]))
                {
                    // curatam inputurile de posibile atacuri XSS
                    $campParola = htmlspecialchars($_POST[$numeCampParola]);

                    // 1. verificare prima regula: lungimea minima
                    if (strlen($campParola) < $reguliValidare->lungimeMinima)
                    {
                        $logger->log("valideazaInputParola: lungimea minima nu este respectata");
                        return false;
                    }

                    // 2. verificare prima regula: lungimea maxima
                    if (strlen($campParola) > $reguliValidare->lungimeMaxima)
                    {
                        $logger->log("valideazaInputParola: lungimea maxima nu este respectata");
                        return false;
                    }

                    // daca s-a ajuns pana aici inseamna ca s-au trecut toate validarile, deci input-ul este bun
                    return true;
                }
                else
                {
                    $logger->log("valideazaInputParola: input $numeCampParola nu exista in POST");
                    return false;
                }

            }
            else
            {
                $logger->log("valideazaInputParola: $numeCampParola este nul sau gol");
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

// functie ce valideaza toate input-urile necesare paginii 'login'
function valideazaInputuriLogin(): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriLogin: inceput rularea");

    $inputurileSuntValide = true;

    $inputuriDeValidat =
    [
        'email' =>
        [
            'tip' => TipInputUtilizator::Email,
            'reguliValidare' => new ReguliValidareUtilizator()
        ],

        'parolaLogin' =>
        [
            'tip' => TipInputUtilizator::ParolaLogin,
            'reguliValidare' => new ReguliValidareParola()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputUtilizator::Email:
                if(valideazaInputEmail($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $email = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::ParolaLogin:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parola = htmlspecialchars($_POST[$numeInput]);
                    $hashParola = password_hash($parola, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true)
    {
        $logger->log("valideazaInputuriLogin: validare cu succes");
        return true;
    }
    else
    {
        $logger->log("valideazaInputuriLogin: validare esuata");
        return false;
    }
}

// functie ce valideaza toate input-urile necesare paginii 'regsiter'
function valideazaInputuriRegister(): ?Utilizator
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriRegister: inceput rularea");
    $inputurileSuntValide = true;

    // variabile folosite pt. a cosntrui un nou obiect de tip Utilizator
    $id = -1;
    $nume = "";
    $email = "";
    $parolaRegister1 = '';
    $parolaRegister2 = '';
    $departament = new Departament(-1, '');
    $rol = RolUtilizator::Obisnuit;

    $inputuriDeValidat =
    [
        'nume' =>
        [
            'tip' => TipInputUtilizator::Nume,
            'reguliValidare' => new ReguliValidareNume()
        ],

        'email' =>
        [
            'tip' => TipInputUtilizator::Email,
            'reguliValidare' => new ReguliValidareUtilizator()
        ],

        'parolaRegister1' =>
        [
            'tip' => TipInputUtilizator::ParolaRegister1,
            'reguliValidare' => new ReguliValidareParola()
        ],

        'parolaRegister2' =>
        [
            'tip' => TipInputUtilizator::ParolaRegister2,
            'reguliValidare' => new ReguliValidareParola()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputUtilizator::Nume:
                if(valideazaInputNume($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $nume = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::Email:
                if(valideazaInputEmail($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $email = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::ParolaRegister1:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parolaRegister1 = htmlspecialchars($_POST[$numeInput]);
                    // $hashParola1 = password_hash($parola1, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::ParolaRegister2:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parolaRegister2 = htmlspecialchars($_POST[$numeInput]);
                    // $hashParola2 = password_hash($parola2, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true and $parolaRegister1 === $parolaRegister2)
    {
        $logger->log("valideazaInputuriRegister: validare cu succes");

        $hashParola = password_hash($parolaRegister1, PASSWORD_DEFAULT);
        return new Utilizator(
            $id,
            $nume,
            $email,
            $hashParola,
            $departament->nume,
            $rol
        );
    }
    else if($inputurileSuntValide === false)
    {
        $logger->log("valideazaInputuriRegister: validare esuata, input-urile nu sunt valide");
        return null;
    }
    else if($parolaRegister1 !== $parolaRegister2)
    {
        $logger->log("valideazaInputuriRegister: validare esuata, parolele nu sunt identice");
        return null;
    }
}

// functie ce valideaza input-ul/input-urile necesare modificarii numelui utilizatorului
function valideazaInputModificareNume(): ?string
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputModificareNume: inceput rularea");

    $numeInput = 'nume-modificat';
    $regulaValidare = new ReguliValidareNume();

    if(valideazaInputNume($numeInput, $regulaValidare) === true)
    {
        $numeNou = htmlspecialchars($_POST[$numeInput]);
        $logger->log("valideazaInputModificareNume: validare cu succes, nume modificat: $numeNou");
        return $numeNou;
    }
    else
    {
        $logger->log("valideazaInputModificareNume: validare esuata");
        return null;
    }
}

// functie ce valideaza input-ul/input-urile necesare modificarii email-ului utilizatorului
function valideazaInputModificareEmail(): ?string
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputModificareEmail: inceput rularea");

    $numeInput = 'email-modificat';
    $regulaValidare = new ReguliValidareUtilizator();

    if(valideazaInputEmail($numeInput, $regulaValidare) === true)
    {
        $emailNou = htmlspecialchars($_POST[$numeInput]);
        $logger->log("valideazaInputModificareEmail: validare cu succes, email modificat: $emailNou");
        return $emailNou;
    }
    else
    {
        $logger->log("valideazaInputModificareEmail: validare esuata");
        return null;
    }
}

// functie ce valideaza toate input-urile necesare paginii 'regsiter'
function valideazaInputuriModificareParola(): ?string
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriModificareParola: inceput rularea");

    $inputurileSuntValide = true;

    $parolaModificata1 = '';
    $parolaModificata2 = '';

    $numeInputParola1 = 'parola-modificata1';
    $numeInputParola2 = 'parola-modificata2';

    if(valideazaInputParola($numeInputParola1, new ReguliValidareParola()) === true)
    {
        $parolaModificata1 = htmlspecialchars($_POST[$numeInputParola1]);
    }
    else
        $inputurileSuntValide = false;

    if(valideazaInputParola($numeInputParola2, new ReguliValidareParola()) === true)
    {
        $parolaModificata2 = htmlspecialchars($_POST[$numeInputParola2]);
    }
    else
        $inputurileSuntValide = false;


    if($inputurileSuntValide === true and $parolaModificata1 === $parolaModificata2)
    {
        $logger->log("valideazaInputuriModificareParola: validare cu succes");

        $hashParola = password_hash($parolaModificata1, PASSWORD_DEFAULT);
        return $hashParola;
    }
    else if($inputurileSuntValide === false)
    {
        $logger->log("valideazaInputuriModificareParola: validare esuata, input-urile nu sunt valide");
        return null;
    }
    else if($parolaModificata1 !== $parolaModificata2)
    {
        $logger->log("vvalideazaInputuriModificareParola: validare esuata, parolele nu sunt identice");
        return null;
    }
}

?>