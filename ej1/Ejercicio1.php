<?php

try {
    $pdo = new PDO('mysql:dbname=recetas;host=localhost', 'root', '');
} catch (PDOException $exc) {
    die("ERROR: " . $exc->getCode() . "<br>"  . $exc->getMessage());
}

$consulta = "SELECT nombre, dificultad, tiempo, (SELECT nombreartistico FROM chef where codigo = receta.cod_chef) as nombreartistico_chef FROM receta";

if ($res = $pdo->query($consulta)){
    $matriz = $res->fetchAll(PDO::FETCH_ASSOC);    
}

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
        
        <table>
            <tr>
                <th>RECETA</th>
                <th>DIFICULTAD</th>
                <th>TIEMPO</th>
                <th>CHEF</th>
            </tr>
            
        <?php 
        
        if(isset($matriz)){
            foreach ($matriz as $fila) {
                echo("<tr>");
                foreach ($fila as $data) {
                    echo("<td>" . $data . "</td>");
                }
                echo("</tr>");
            }
        }       
        ?>
        </table>
        

    </body>
</html>
