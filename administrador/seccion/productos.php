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
        
        $fecha= new DateTime();
        //Si txtImagen es diferente de vacio agregamos fecha y concatenamos _ y concatenamos nombre del archivo imagen
        //Si no, solo agregamos como nombre imagen.jpg
        $nombreArchivo=($imagen!="")? $fecha->getTimestamp()."_".$_FILES["imagen"]["name"]:"imagen.jpg" ;
        $tmpImagen=$_FILES["imagen"]["tmp_name"];
        
        if($tmpImagen!=""){
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
        }
        $sentenciaSQL->bindParam(':imagen',$nombreArchivo); //Parametro a reemplazar
        $sentenciaSQL->execute();
        break;

    case "Modificar":


        $sentenciaSQL= $conexion->prepare("UPDATE libros SET nombre=:nombre WHERE id=:id;");
        $sentenciaSQL->bindParam(':nombre',$txtNombre);
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();

        if($imagen!=""){

            $fecha= new DateTime();
            $nombreArchivo=($imagen!="") ? $fecha->getTimestamp()."_".$_FILES["imagen"]["name"] : "imagen.jpg" ;//renombra el archivo imagen
            $tmpImagen=$_FILES["imagen"]["tmp_name"];
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);//sube la imagen nueva a la carptea img

            //Seleccionamos la imagen de la Base de datos de acuerdo al id
            $sentenciaSQL= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id;");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

            //borramos la imagen de la carpeta IMG de acuerdo a la imagen seleccionada de la Base de Datos
            if(isset($libro["imagen"]) && $libro["imagen"]!="imagen.jpg"){
                if(file_exists("../../img/".$libro["imagen"])){
                    unlink("../../img/".$libro["imagen"]);
                }
            }
            
            //Actualizamos la Base de datos con el nombre de la imagen nueva
            $sentenciaSQL= $conexion->prepare("UPDATE libros SET imagen=:imagen WHERE id=:id;");
            $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
        }
        break; 

    case "Cancelar":
        echo "Presionado boton Cancelar";
        break;

    case "Seleccionar":
        $sentenciaSQL= $conexion->prepare("SELECT * FROM libros WHERE id=:id;");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        $txtNombre=$libro['nombre'];
        $imagen=$libro['imagen'];
        break;

    case "Borrar":
        $sentenciaSQL= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id;");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        if(isset($libro["imagen"]) && $libro["imagen"]!="imagen.jpg"){
            if(file_exists("../../img/".$libro["imagen"])){
                unlink("../../img/".$libro["imagen"]);
            }
        }

        
        $sentenciaSQL= $conexion->prepare("DELETE FROM libros WHERE id=:id;");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        
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
                    <input type="text" class="form-control" value="<?php echo $txtID;?>" name="txtID" id="txtID" placeholder="ID">
                </div>

                <div class = "form-group">
                    <label for="txtNombre">Nombre:</label>
                    <input type="text" class="form-control" value="<?php echo $txtNombre;?>" name="txtNombre" id="txtNombre" placeholder="Nombre">
                </div>

                <div class = "form-group">
                    <label for="imagen">Imagen:</label>
                    <br/>
                    <?php if($imagen!=""){?>

                        <img class="img-thumbnail rounded" src="../../img/<?php echo $imagen;?>" width="50" alt="">

                    <?php }?>
                    
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
                <td>
                    
                    <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen'];?>" width="50" alt="">
                </td>
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