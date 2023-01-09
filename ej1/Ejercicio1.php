<?php

try {
    $pdo = new PDO('mysql:dbname=recetas;host=localhost', 'root', '');
} catch (PDOException $exc) {
    die("ERROR: " . $exc->getCode() . "<br>"  . $exc->getMessage());
}
$columnas = ["nombre", "dificultad", "tiempo", "nombreartistico_chef"];
$matriz = [];

$consulta = "SELECT nombre, dificultad, tiempo, (SELECT nombreartistico FROM chef where codigo = receta.cod_chef) as nombreartistico_chef FROM receta";
$res = $pdo->prepare($consulta);
$res->execute();



foreach ($columnas as $col){
    $res->bindColumn($col, $matriz[]);
}


/*if ($res = $pdo->prepare($consulta)){
    $res->bindColumn('nombreReceta', $res)
    //$matriz = $res->fetchAll(PDO::FETCH_ASSOC);    
}*/

unset($consulta);
unset($pdo);
?>

<!doctype html>
<html>
    <head>
        <title>Ejercicio PDO 1</title>
        <link rel="stylesheet" href="./tareas.css">
    </head>
    <body>
        <h1>LISTADO DE RECETAS</h1>
        <table>
            <tr>
                <th>RECETA</th>
                <th>DIFICULTAD</th>
                <th>TIEMPO</th>
                <th>CHEF</th>
            </tr>
            
        <?php 
        while($res->fetch(PDO::FETCH_BOUND)){
            //foreach ($matriz as $fila) {
                echo("<tr>");
                foreach ($matriz as $data) {
                    echo("<td>" . $data . "</td>");
                }
                echo("</tr>");
            //}
        }
              
        ?>
        </table>
        

    </body>
</html>
