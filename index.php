<?php
include_once('./conf/con.php');

// Función para recargar la página
function reloadPage() {
    echo '<script>window.location.reload()</script>';
}

try {
    // Preparar la consulta
    $stmt = $pdo->prepare('SELECT code, nombre, existencia, fecharegistro, precio, imagen FROM medicamentos');

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar los resultados en un array 
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error de base de datos: ' . $e->getMessage();
}

// Inicializar variables para los valores del formulario
$code = '';
$nombre = '';
$existencia = '';
$fecharegistro = '';
$precio = '';
$imagenPath = ''; // Definir la variable para evitar errores

// Mensaje de éxito o error
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar si el campo 'code' está definido en $_POST
        if (isset($_POST['code'])) {
            // Recuperar los datos del formulario
            $code = $_POST['code'];
            $nombre = $_POST['nombre'];
            $existencia = $_POST['existencia'];
            $fecharegistro = $_POST['fecharegistro'];
            $precio = $_POST['precio'];

            // Verificar si el código ya existe en la base de datos antes de la inserción
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM medicamentos WHERE code = ?');
            $checkStmt->execute([$code]);
            $count = $checkStmt->fetchColumn();

            // Convertir el resultado a un entero
            $count = intval($count);

            if ($count === 0) {
                // Código de inserción de imagen
                if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $imagenTmpName = $_FILES['imagen']['tmp_name'];
                    $imagenName = $_FILES['imagen']['name'];
                    $imagenPath = './images/' . $imagenName; // Directorio donde deseas guardar las imágenes
                    
                    // Mueve la imagen al directorio de destino
                    move_uploaded_file($imagenTmpName, $imagenPath);
                }

                // Preparar la consulta de inserción
                $insertStmt = $pdo->prepare('INSERT INTO medicamentos (code, nombre, existencia, fecharegistro, precio, imagen) VALUES (?, ?, ?, ?, ?, ?)');
                
                // Ejecutar la consulta de inserción
                $insertStmt->execute([$code, $nombre, $existencia, $fecharegistro, $precio, $imagenPath]);
                
                // Limpiar los campos después de la inserción exitosa
                $code = '';
                $nombre = '';
                $existencia = '';
                $fecharegistro = '';
                $precio = '';
                
                // Mensaje de éxito
                $message = 'Inserción exitosa';
            } else {
                // Código duplicado
                $message = 'El código ya existe en la base de datos.';
            }
        }
    } catch (PDOException $e) {
        $message = 'Error de base de datos: ' . $e->getMessage();
    }
}

// Mensaje de éxito o error para la edición
$editMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code']) && isset($_POST['nombre'])) {
    try {
        // Recuperar los datos del formulario de edición
        $code = $_POST['code'];
        $nombre = $_POST['nombre'];
        $existencia = $_POST['existencia'];
        $fecharegistro = $_POST['fecharegistro'];
        $precio = $_POST['precio'];

        // Preparar la consulta de actualización
        $updateStmt = $pdo->prepare('UPDATE medicamentos SET nombre = ?, existencia = ?, fecharegistro = ?, precio = ? WHERE code = ?');

        // Ejecutar la consulta de actualización
        $updateStmt->execute([$nombre, $existencia, $fecharegistro, $precio, $code]);

        // Verificar si se realizó la edición exitosamente
        $rowsAffected = $updateStmt->rowCount();

        if ($rowsAffected > 0) {
            // Mensaje de éxito para la edición
            $editMessage = 'Edición exitosa';
        } else {
            // No se realizaron cambios
            
        }
    } catch (PDOException $e) {
        $editMessage = 'Error de base de datos: ' . $e->getMessage();
    }
}

// Mensaje de éxito o error para la eliminación
$deleteMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        // Recuperar el código del formulario de eliminación
        $code = $_POST['id'];

        // Preparar la consulta de eliminación
        $deleteStmt = $pdo->prepare('DELETE FROM medicamentos WHERE code = ?');

        // Ejecutar la consulta de eliminación
        $deleteStmt->execute([$code]);

        // Verificar si se realizó la eliminación exitosamente
        $rowsAffected = $deleteStmt->rowCount();

        if ($rowsAffected > 0) {
            // Mensaje de éxito para la eliminación
            $deleteMessage = 'Eliminación exitosa';
        } else {
            // No se eliminó ningún registro
            
        }
    } catch (PDOException $e) {
        $deleteMessage = 'Error de base de datos: ' . $e->getMessage();
    }
}


// Mensaje de éxito o error para la edición
$editMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    try {
        // Recuperar el ID del formulario de edición
        $id = $_POST['edit_id'];

        // Recuperar los datos del formulario de edición
        $nombre = $_POST['edit_nombre'];
        $existencia = $_POST['edit_existencia'];
        $fecharegistro = $_POST['edit_fecharegistro'];
        $precio = $_POST['edit_precio'];

        // Código de actualización de imagen
        $imagenPath = ''; // Definir la variable para evitar errores

        if ($_FILES['edit_imagen']['error'] === UPLOAD_ERR_OK) {
            $imagenTmpName = $_FILES['edit_imagen']['tmp_name'];
            $imagenName = $_FILES['edit_imagen']['name'];
            $imagenPath = './images/' . $imagenName; // Directorio donde deseas guardar las imágenes
            
            // Mueve la imagen al directorio de destino
            move_uploaded_file($imagenTmpName, $imagenPath);
        }

        // Preparar la consulta de actualización
        $updateStmt = $pdo->prepare('UPDATE medicamentos SET nombre = ?, existencia = ?, fecharegistro = ?, precio = ?, imagen = ? WHERE code = ?');

        // Ejecutar la consulta de actualización
        $updateStmt->execute([$nombre, $existencia, $fecharegistro, $precio, $imagenPath, $id]);

        // Verificar si se realizó la edición exitosamente
        $rowsAffected = $updateStmt->rowCount();

        if ($rowsAffected > 0) {
            // Mensaje de éxito para la edición
            $editMessage = 'Edición exitosa';
        } else {
            // No se realizaron cambios
            
        }
    } catch (PDOException $e) {
        $editMessage = 'Error de base de datos: ' . $e->getMessage();
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>PDO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Lista de Medicamentos</h1>
        <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#myModal">Agregar Medicamento</button>

        <!-- Modal para la inserción -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Medicamento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="insertForm" method="post" enctype="multipart/form-data"> <!-- Agregar enctype para el manejo de archivos -->
                            <div class="form-group">
                                <label for="code">Código:</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?php echo $code; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="imagen">Imagen:</label>
                                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="existencia">Existencia:</label>
                                <input type="number" class="form-control" id="existencia" name="existencia" value="<?php echo $existencia; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="fecharegistro">Fecha de Registro:</label>
                                <input type="date" class="form-control" id="fecharegistro" name="fecharegistro" value="<?php echo $fecharegistro; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="precio">Precio:</label>
                                <input type="number" class="form-control" step="0.01" id="precio" name="precio" value="<?php echo $precio; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($medicamentos as $medicamento): ?>
    <!-- Modal para la eliminación -->
    <div class="modal fade" id="deleteModal<?php echo $medicamento['code']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este medicamento?</p>
                </div>
                <div class="modal-footer">
                    <!-- Formulario para enviar la solicitud de eliminación -->
                    <form method="post">
                        <input type="hidden" name="id" value="<?php echo $medicamento['code']; ?>">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<?php foreach ($medicamentos as $medicamento): ?>
    <!-- Modal para la edición -->
    <div class="modal fade" id="editModal<?php echo $medicamento['code']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Medicamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="edit_id" value="<?php echo $medicamento['code']; ?>">
                        <div class="form-group">
                            <label for="edit_nombre">Nombre:</label>
                            <input type="text" class="form-control" id="edit_nombre" name="edit_nombre" value="<?php echo $medicamento['nombre']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_existencia">Existencia:</label>
                            <input type="number" class="form-control" id="edit_existencia" name="edit_existencia" value="<?php echo $medicamento['existencia']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_fecharegistro">Fecha de Registro:</label>
                            <input type="date" class="form-control" id="edit_fecharegistro" name="edit_fecharegistro" value="<?php echo $medicamento['fecharegistro']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_precio">Precio:</label>
                            <input type="number" class="form-control" step="0.01" id="edit_precio" name="edit_precio" value="<?php echo $medicamento['precio']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_imagen">Imagen:</label>
                            <input type="file" class="form-control-file" id="edit_imagen" name="edit_imagen" accept="image/*">
                        </div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>




        <?php if (!empty($editMessage)) : ?>
            <div class="alert <?php echo ($editMessage === 'Edición exitosa') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo $editMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (!empty($message)) : ?>
            <div class="alert <?php echo ($message === 'Inserción exitosa') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito o error para la eliminación -->
        <?php if (!empty($deleteMessage)) : ?>
            <div class="alert <?php echo ($deleteMessage === 'Eliminación exitosa') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo $deleteMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Tabla de medicamentos -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Existencia</th>
                    <th>Fecha de Registro</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Acciones</th> <!-- Nueva columna para botones de acción -->
                </tr>
                <?php foreach ($medicamentos as $medicamento): ?>
                    <tr>
                        <td><?php echo $medicamento['code']; ?></td>
                        <td><?php echo $medicamento['nombre']; ?></td>
                        <td><?php echo $medicamento['existencia']; ?></td>
                        <td><?php echo $medicamento['fecharegistro']; ?></td>
                        <td><?php echo $medicamento['precio']; ?></td>
                        <td>
                            <img src="<?php echo isset($medicamento['imagen']) ? $medicamento['imagen'] : ''; ?>" alt="Imagen del medicamento" width="100">
                        </td>
                        <td>
                            <button class="btn btn-info" data-toggle="modal" data-target="#editModal<?php echo $medicamento['code']; ?>">Editar</button>
                            <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $medicamento['code']; ?>">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
