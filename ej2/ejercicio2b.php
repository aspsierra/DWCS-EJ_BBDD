<?php

class Chef {

    private $id;
    private $nombre;
    private $apellidos = "";
    private $nombreArtistico;

    function __get($aux) {
        return $this->$aux;
    }

    function __set($key, $value) {
        if ($key == "apellidos") {
            $this->apellidos .= $value . " ";
        } else {
            $this->$key = $value;
        }
    }

}

try {
    $pdo = new PDO('mysql:dbname=recetas;host=localhost', 'root', '');
} catch (PDOException $exc) {
    die("ERROR: " . $exc->getCode() . "<br>" . $exc->getMessage());
}

$consulta = "SELECT codigo, nombre, apellido1, apellido2, nombreartistico FROM chef";

$arChefs = [];

if ($resultado = $pdo->query($consulta)) {


    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
        $cocinero = new Chef();
        foreach ($fila as $key => $value) {
            switch ($key) {
                case "codigo":
                    $cocinero->id = $value;
                    break;
                case "nombre":
                    $cocinero->nombre = $value;
                    break;
                case "apellido1":
                case "apellido2":
                    $cocinero->apellidos = $value;
                    break;
                case "nombreartistico":
                    $cocinero->nombreArtistico = $value;
                    break;
            }
        }
        //var_dump($cocinero);
        array_push($arChefs, $cocinero);
        unset($cocinero);
    }
}

//var_dump($arChefs);
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

        <h1>LISTADO DE COCINEROS</h1>
        <table>
            <tr>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>NOMBRE ARTÍTICO</th>
                <th></th>
            </tr>

            <?php
            if (isset($arChefs)) {
                foreach ($arChefs as $chef) {
                    echo("<tr>");                    
                    echo("<td>" . $chef->nombre . "</td>");
                    echo("<td>" . $chef->apellidos . "</td>");
                    echo("<td>" . $chef->nombreArtistico . "</td>");
                    
                    echo("<td><a href=' " . $_SERVER['PHP_SELF'] . "?idChef=" . $chef->id ."'>Editar</a></td>");
                    echo("</tr>");
                }
            }
            ?>
        </table>





    </body>
</html>