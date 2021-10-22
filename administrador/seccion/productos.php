<?php include("../template/cabecera.php"); ?>

<?php

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$imagen = (isset($_FILES['imagen']['name'])) ? $_FILES['imagen']['name'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

include("../config/bd.php");

switch($accion){

    //INSERT INTO `libros` (`id`, `nombre`, `imagen`) VALUES (NULL, 'Libro de php', 'imagen.jpg');
    case "Agregar":
        $sentenciaSQL= $conexion->prepare("INSERT INTO libros (nombre,imagen) VALUES (:nombre,:imagen);");
        $sentenciaSQL->bindParam(':nombre',$txtNombre); //Parametro a reemplazar
        $sentenciaSQL->bindParam(':imagen',$imagen); //Parametro a reemplazar
        $sentenciaSQL->execute();
        break;

    case "Modificar":
        echo "Presionado boton Modificar";
        break; 

    case "Cancelar":
        echo "Presionado boton Cancelar";
        break;

    case "Seleccionar":
        echo "Presionado boton Seleccionar";
        break;

    case "Borrar":
        $sentenciaSQL= $conexion->prepare("DELETE FROM libros WHERE id=:id;");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        echo "Presionado boton Borrar";
        break;
}

//Seleccionar todos los libros de la Base de Datos
$sentenciaSQL= $conexion->prepare("SELECT * FROM libros;");
$sentenciaSQL->execute();
$listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="col-md-5">

    <div class="card">
        <div class="card-header">
            Datos de libro
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class = "form-group">
                    <label for="txtID">ID:</label>
                    <input type="text" class="form-control" name="txtID" id="txtID" placeholder="ID">
                </div>

                <div class = "form-group">
                    <label for="txtNombre">Nombre:</label>
                    <input type="text" class="form-control" name="txtNombre" id="txtNombre" placeholder="Nombre">
                </div>

                <div class = "form-group">
                    <label for="imagen">Imagen:</label>
                    <input type="file" class="form-control" name="imagen" id="imagen" placeholder="Imagen">
                </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" value="Cancelar"class="btn btn-info">Cancelar</button>
                </div>

            </form>
        </div>
    </div>

    


    
    
    
</div>

<div class="col-md-7">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listaLibros as $libro){ ?>
            <tr>
                <td><?php echo $libro['id'];?></td>
                <td><?php echo $libro['nombre'];?></td>
                <td><?php echo $libro['imagen'];?></td>
                <td>
                    Seleccionar | Borrar
                    <form method="post">
                        <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id'];?>"/>
                        <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>
                        <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>
                    </form>
                </td>
            </tr>
            <?php }?>
        </tbody>
    </table>
</div>

<?php include("../template/pie.php"); ?>