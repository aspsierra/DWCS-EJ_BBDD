<?php
try {
    $pdo = new PDO('mysql:dbname=recetas;host=localhost', 'root', '');
} catch (PDOException $exc) {
    die("ERROR: " . $exc->getCode() . "<br>" . $exc->getMessage());
}

class Receta {

    public $codigo;
    public $nombre;
    public $dificultad;
    public $tiempo;
    public $nombreartistico_chef;

    public function setNombre($str) {
        $str = strtolower($str);
        $this->nombre = ucfirst($str);
    }

    public function setChef($str) {
        $str = strtolower($str);
        $this->nombreartistico_chef = ucfirst($str);
    }

}

class RecetaDetalles {

    private $nombre;
    private $ingredientes = [];

    function __set($clave, $valor) {
        if(gettype($valor)=="object"){
            array_push($this->$clave, $valor);
        } else{
            $this->$clave = $valor;
        }
        
    }

    function __get($aux) {
        return $this->$aux;
    }

}

class Ingredientes {

    private $nombre;
    private $cantidad;
    private $medida;

    function __set($clave, $valor) {
        $this->$clave = $valor;
    }

    function __get($aux) {
        return $this->$aux;
    }

}

$consulta = "";

if (!isset($_GET['idReceta']) & !isset($_GET['nombreReceta'])) {
    $matriz = [];
    $fila = new Receta();

    $consulta = "SELECT codigo, nombre, dificultad, tiempo, (SELECT nombreartistico FROM chef where codigo = receta.cod_chef) as nombreartistico_chef FROM receta";

    $res = $pdo->prepare($consulta);
    $res->execute();

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {

        foreach ($fila as $key => &$prop) {
            if ($key == 'nombre') {
                $fila->setNombre($row[$key]);
            } else if ($key == 'nombreartistico_chef') {
                $fila->setChef($row[$key]);
            } else {
                $prop = $row[$key];
            }
        }

        array_push($matriz, $fila);
        $i = 0;
        unset($fila);
        $fila = new Receta();
    }
} else {

    $idReceta = (integer) $_GET['idReceta'];

    $consulta = "SELECT i.nombre, r_i.cantidad, r_i.medida FROM ingrediente AS i JOIN receta_ingrediente AS r_i ON i.codigo = r_i.cod_ingrediente JOIN receta AS r ON r_i.cod_receta = r.codigo WHERE r.codigo = " . $idReceta;

    $res = $pdo->prepare($consulta);
    $res->execute();

    $i = 0;

    $recetaDetallada = new RecetaDetalles();

    $recetaDetallada->nombre = $_GET["nombreReceta"];

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {

        $ingrediente = new Ingredientes();
        foreach ($row as $key => $value) {
            $ingrediente->$key = $value;
        }
        $recetaDetallada->ingredientes = $ingrediente;
        unset($ingrediente);
    }
}



//var_dump($matriz);
unset($consulta);
unset($pdo);
?>

<!doctype html>
<html>
    <head>
        <title>Ejercicio PDO 2</title>
        <link rel="stylesheet" href="./tareas.css">
    </head>
    <body>
        <?php
        if (!isset($_GET['idReceta']) && !isset($_GET['nombreReceta'])) {
            ?>
            <h1>LISTADO DE RECETAS</h1>
            <table>
                <tr>
                    <th>RECETA</th>
                    <th>DIFICULTAD</th>
                    <th>TIEMPO</th>
                    <th>CHEF</th>
                </tr>

                <?php
                if (isset($matriz)) {
                    foreach ($matriz as $fila) {
                        echo("<tr>");
                        foreach ($fila as $prop => $data) {
                            if ($prop == "nombre") {
                                echo("<td><a href=' " . $_SERVER['PHP_SELF'] . "?idReceta=" . $fila->codigo . "&nombreReceta=" . $data . "'>" . $data . "</a></td>");
                            } else if ($prop != 'codigo') {
                                echo("<td>" . $data . "</td>");
                            }
                        }

                        echo("</tr>");
                    }
                }
                ?>
            </table>

            <?php
        } else {
            ?> 

            <h1><?php echo $recetaDetallada->nombre ?></h1>

            <h2>Ingredientes de la receta</h2>
            <div class='ingredientes'>
                <?php
                foreach ($recetaDetallada->ingredientes as $ingr) {
                    echo('<p>' . $ingr->nombre . ": " . $ingr->cantidad . " " . $ingr->medida . "</p>");
                }
                ?>
            </div>

            <a href="./Ejercicio2.php"><< Volver</a>



    <?php
}
?>



    </body>
</html>
