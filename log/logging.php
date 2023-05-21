<?php

enum TipLog: string
{
    case Info = 'Info';
    case EroareRecuperabila = 'EroareRecuperabila';
    case EroareNerecuperabila = 'EroareNerecuperabila';
}

class Logger
{
    private $phpScriptPath = '';
    private $caleFisierLog = ''; // cale pt. mesaje complete in format JSON
    private $caleFisierLogMesajeScurte = ''; // cale pt. mesaje scurte (doar mesajul, fara alte detalii)

    public function __construct($phpScriptPath)
    {
        $this->caleFisierLog = __DIR__ . '/errors.log';
        $this->caleFisierLogMesajeScurte = __DIR__ . '/errors-short.log';

        if(file_exists($this->caleFisierLog) === false)
        {
            $fisierLog = fopen($this->caleFisierLog, 'r');
            fclose($fisierLog);
        }

        if(file_exists($this->caleFisierLogMesajeScurte) === false)
        {
            $fisierLog = fopen($this->caleFisierLogMesajeScurte, 'r');
            fclose($fisierLog);
        }

        if($phpScriptPath != null)
            $this->phpScriptPath = $phpScriptPath;
    }

    private function iaUsername(): string
    {
        if(isset($_SESSION['username']) and !empty($_SESSION['username']))
            return $_SESSION['username'];
        else
            return 'unautenthicated';
    }

    public function log(string $mesaj)
    {
        if($mesaj != null)
        {
            $sirMesaj =
            [
                'timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
                'requestMethod' => $_SERVER['REQUEST_METHOD'],
                'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                'username' => $this->iaUsername(),
                'phpScripPath' => $this->phpScriptPath,
                'mesaj' => $mesaj
            ];

            $mesajJSON = json_encode($sirMesaj, JSON_PRETTY_PRINT);

            if($mesajJSON != false)
            {
                file_put_contents($this->caleFisierLog, $mesajJSON . PHP_EOL, FILE_APPEND);
                file_put_contents($this->caleFisierLogMesajeScurte, $mesaj . PHP_EOL, FILE_APPEND);
            }
        }
    }
}

?>