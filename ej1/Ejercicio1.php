<?php

try {
    $pdo = new PDO('mysql:dbname=recetas;host=localhost', 'root', '');
} catch (PDOException $exc) {
    die("ERROR: " . $exc->getCode() . "<br>"  . $exc->getMessage());
}

class Receta{    
    public $nombre;
    public $dificultad;
    public $tiempo;
    public $nombreartistico_chef;       
    
    public function setNombre($str){
        $str = strtolower($str);
        $this->nombre = ucfirst($str);
    }
    
    public function setChef($str){
        $str = strtolower($str);
        $this->nombreartistico_chef = ucfirst($str);
    }
}


$matriz = [];
$fila = new Receta();

$consulta = "SELECT nombre, dificultad, tiempo, (SELECT nombreartistico FROM chef where codigo = receta.cod_chef) as nombreartistico_chef FROM receta";
$res = $pdo->prepare($consulta);
$res->execute();

$i=0;
while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    
    foreach ($fila as $key => &$prop) {
        if ($key == 'nombre'){
            $fila->setNombre($row[$key]);
        }else if($key == 'nombreartistico_chef'){
            $fila->setChef($row[$key]);
        }else{
            $prop = $row[$key];
        }
    }
    
    array_push($matriz, $fila);
    $i = 0;
    unset($fila);
    $fila = new Receta();  
}

//var_dump($matriz);
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
