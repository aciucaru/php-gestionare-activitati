<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/repo/activitate.repo.php';
require_once __DIR__ . '/repo/departament.repo.php';
require_once __DIR__ . '/repo/categorie.repo.php';
require_once __DIR__ . '/model/utilizator.php';
require_once __DIR__ . '/model/activitate.php';
require_once __DIR__ . '/service/manager-sesiune.php';
require_once __DIR__ . '/service/manager-input-activitati.php';

$managerSesiune = ManagerSesiune::iaSingleton();
$managerActivitati = ManagerInputActivitati::iaSingleton();
$sirActivitatiUtilizatorLogat = [];

// doar utilizatorii logati is pot vedea/gestiona activitatile
// deci se verifica daca exista un utilizator logat
if($managerSesiune->existaUtilizatorLogat())
{
    $repoActivitati = new ActivitateRepo();
    $repoCategorii = new CategorieRepo();

    // se iau activitatile ce apartin utilizatorului logat
    $emailUtilizator = $managerSesiune->iaEmailUtilizator();
    $sirActivitatiUtilizatorLogat = $repoActivitati->iaActivitatiUtilizator($emailUtilizator);

    // se iau categoriile specifice departamentului din care face parte utilizatorul logat
    // doar aceste categorii vor fi disponibile pt. a fi selectate
    $sirCategorii = $repoCategorii->iaCategoriiDepartament($managerSesiune->iaNumeDepartamentUtilizator());

    // adaugare activitate
    // $managerActivitati = ManagerInputActivitati::iaSingleton();
    $managerActivitati->incearcaAdaugareActivitate();
}

?>

<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT School curs PHP: tema curs 13</title>
    <link rel="stylesheet" type="text/css" href="./styles/general.css" />
    <link rel="stylesheet" type="text/css" href="./styles/navbar.css" />
    <link rel="stylesheet" type="text/css" href="./styles/footer.css" />
    <link rel="stylesheet" type="text/css" href="./styles/activitati.css" />
    <link rel="stylesheet" type="text/css" href="./styles/form-input-general.css" />
</head>

<body>
    <?php require('./templates/navbar.template.php') ?>

    <?php if($managerSesiune->existaUtilizatorLogat()): ?>
        <div class="container-activitati">
            <div class="container-tabel">
                <table class="tabel-activitati">
                    <thead class="tabel-activitati-header">
                        <tr>
                            <th scope="col" class="coloana-data-timp celula-tabel">Data & ora</th>
                            <th scope="col" class="coloana-departament celula-tabel">Departament</th>
                            <th scope="col" class="coloana-categorie celula-tabel">Categorie</th>
                            <th scope="col" class="coloana-ore celula-tabel">Ore</th>
                            <th scope="col" class="coloana-descriere celula-tabel">Descriere</th>
                        </tr>
                    </thead>

                    <tbody class="tabel-activitati-body">
                        <?php foreach($sirActivitatiUtilizatorLogat as $index=>$activitate): ?>
                            <?php if($index % 2 === 0): ?>
                                <tr class="rand-par-tabel">
                            <?php endif ?>
                            <?php if($index % 2 === 1): ?>
                                <tr class="rand-impar-tabel">
                            <?php endif ?>
                                <td class="coloana-data-timp celula-tabel"> <?php echo $activitate->dataTimp->format('Y-m-d') ?>  </td>
                                <td class="coloana-departament celula-tabel"> <?php echo $activitate->departament ?>  </td>
                                <td class="coloana-categorie celula-tabel"> <?php echo $activitate->categorie ?>  </td>
                                <td class="coloana-ore celula-tabel"> <?php echo $activitate->ore ?>  </td>
                                <td class="coloana-descriere celula-tabel"> <?php echo $activitate->descriere ?>  </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                <div class="form-activitati">
                <label class="input-data-label">Data</label>
                    <!-- <input type="date" name="data" class="input-data" value="2023-07-22" min="2023-01-01" max="2023-12-31"> -->
                    <?php $managerActivitati->genereazaInputData(); ?>
                    
                    <label class="input-categorie-label">Categorie</label>
                    <select class="input-categorie" name="categorie">
                        <?php
                            foreach($sirCategorii as $categorie)
                            {
                                echo '<option value="' . $categorie->nume .'">' . $categorie->nume . '</option>';
                            }
                        ?>
                    </select>

                    <label class="input-ore-label">Ore</label>
                    <input type="number" name="ore" class="input-ore" min="0" max="8" step="0.25">

                    <label class="input-descriere-label">Descriere</label>
                    <textarea class="input-descriere" name="descriere" rows="3" placeholder="Descriere" ></textarea>

                    <input type="submit" name="submit-activitate" value="Adauga activitate"
                    class="submit-activitate"/>
                </div>
            </form>
        </div>
    <?php endif ?>

    <?php require('./templates/footer.template.php') ?>
</body>

</html>