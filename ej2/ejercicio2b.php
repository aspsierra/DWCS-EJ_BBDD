<?php

function compruebaOptions($valor, $valorEnBD) {
    if ($valorEnBD == $valor)
        return "selected";
    else
        return "";
}

class Chef {

    private $id;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $nombreArtistico;
    private $sexo;
    private $fecha;
    private $localidad;
    private $provincia;

    function __construct($data) {
        $this->id = $data['codigo'];
        $this->nombre = $data['nombre'];
        $this->apellido1 = $data['apellido1'];
        $this->apellido2 = $data['apellido2'];
        $this->nombreArtistico = $data['nombreartistico'];
        $this->sexo = $data['sexo'];
        $this->fecha = $data['fecha_nacimiento'];
        $this->localidad = $data['localidad'];
        $this->provincia = $data['cod_provincia'];
    }

    function __get($name) {
        return $this->$name;
    }

}

try {
    $pdo = new PDO('mysql:dbname=recetas;host=localhost', 'root', '');
} catch (PDOException $exc) {
    die("ERROR: " . $exc->getCode() . "<br>" . $exc->getMessage());
}
if (!isset($_GET['idChef'])) {

    $consulta = "SELECT codigo, nombre, apellido1, apellido2, nombreartistico FROM chef";

    $arChefs = [];

    if ($resultado = $pdo->query($consulta)) {
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            array_push($arChefs, $fila);
        }
    }
} else {

    $consChef = "SELECT * FROM chef WHERE codigo = " . $_GET['idChef'];
    if ($resChef = $pdo->query($consChef)) {
        while ($chef = $resChef->fetch(PDO::FETCH_ASSOC)) {

            $editChef = new Chef($chef);
        }
        $editChef->id;
    }

    $consultProv = "SELECT * FROM provincia";
    $options = "";
    if ($resProv = $pdo->query($consultProv)) {
        while ($prov = $resProv->fetch(PDO::FETCH_ASSOC)) {
            $options .= "<option value= '" . $prov['codigo'] . "'"
                    . compruebaOptions($prov['codigo'], $editChef->provincia) . ">"
                    . $prov['nombre'] . "</option>";
        }
    }
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'Cancelar':

                header('Location:ejercicio2b.php');
                break;

            case 'Actualizar':
                $consActualizar = "UPDATE chef SET "
                        . "nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2,"
                        . "nombreartistico = :alias, sexo = :sexo,"
                        . "fecha_nacimiento = :fecha, localidad = :localidad,"
                        . "cod_provincia = :provincia WHERE codigo = " . $_GET['idChef'];

                $update = $pdo->prepare($consActualizar);
                $update->bindValue(':nombre', $_POST['name'], PDO::PARAM_STR);
                $update->bindValue(':apellido1', $_POST['surn'][0], PDO::PARAM_STR);
                $update->bindValue(':apellido2', $_POST['surn'][1], PDO::PARAM_STR);
                $update->bindValue(':alias', $_POST['alias'], PDO::PARAM_STR);
                $update->bindValue(':sexo', $_POST['sex'], PDO::PARAM_STR);
                $update->bindValue(':fecha', $_POST['bdate'], PDO::PARAM_STR);
                $update->bindValue(':localidad', $_POST['city'], PDO::PARAM_STR);
                $update->bindValue(':provincia', $_POST['province'], PDO::PARAM_INT);
                $resUpdate = $update->execute();
                
                if($resUpdate){
                    header('Refresh:4');
                } else{
                    echo "Se produjo un error al actualizar";
                }
          
                unset($resUpdate);
                unset($consActualizar);
                break;
            case 'delete':
                break;
            default:
                break;
        }
    }
}




unset($resChef);
unset($consChef);

unset($resProv);
unset($consultProv);

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
        if (!isset($_GET['idChef'])) {
            ?>
            <h1>LISTADO DE COCINEROS</h1>
            <table>
                <tr>
                    <th>NOMBRE</th>
                    <th>APELLIDOS</th>
                    <th></th>
                    <th>NOMBRE ARTÍTICO</th>
                    <th></th>
                </tr>

                <?php
                if (isset($arChefs)) {
                    foreach ($arChefs as $chef) {
                        echo("<tr>");
                        foreach ($chef as $key => $data) {
                            if ($key != 'codigo') {
                                echo("<td>" . $data . "</td>");
                            }
                        }
                        echo("<td><a href=' " . $_SERVER['PHP_SELF'] . "?idChef=" . $chef['codigo'] . "'>Editar</a></td>");
                        echo("</tr>");
                    }
                }
                ?>
            </table>
            <?php
        } else {
            ?>

            <h1>EDITAR COCINERO</h1>
            <form action="" method="POST">
                <table class="edicion">
                    <tr>
                        <td> <label for="id">Código:</label></td>
                        <td><input disabled type="number" name="id" id="id" value="<?php echo $editChef->id ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="name">Nombre:</label></td>
                        <td><input type="text" name="name" id="name" value="<?php echo $editChef->nombre ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="surn">Apellidos:</label></td>
                        <td><input type="text" name="surn[]" id="" value="<?php echo $editChef->apellido1 ?>"></td>
                        <td><input type="text" name="surn[]" id="" value="<?php echo $editChef->apellido2 ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="alias">Nombre Artístico:</label></td>
                        <td><input type="text" name="alias" id="alias" value="<?php echo $editChef->nombreArtistico ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="sex">Sexo:</label></td>
                        <td>
                            <select name="sex" id="sex">
                                <option value="H" <?php echo compruebaOptions("H", $editChef->sexo) ?>>Hombre</option>
                                <option value="M" <?php echo compruebaOptions("M", $editChef->sexo) ?>>Mujer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="bdate">Feche de nacimiento:</label></td>
                        <td><input type="date" name="bdate" id="bdate" value="<?php echo $editChef->fecha ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="city">Localidad:</label></td>
                        <td><input type="text" name="city" id="city" value="<?php echo $editChef->localidad ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="province">Provincia:</label></td>
                        <td>
                            <select name="province" id="province">
                                <?php
                                echo $options;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td> <button type="submit" name="action" value="Actualizar"> Actualizar</button></td>                   
                        <td> <button type="submit" name="action" value="Eliminar"> Eliminar</button></td>
                        <td> <button type="submit" name="action" value="Cancelar"> Cancelar</button></td>
                    </tr>

                </table>
            </form>


            <?php
        }
        ?>

    </body>
</html>