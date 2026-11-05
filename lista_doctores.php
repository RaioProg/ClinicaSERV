<?php
require_once "AccesoDatos.php";
// PASO A: Iniciar o reanudar la sesión (OBLIGATORIO al principio)
session_start();
// PASO B: Inicializar el array de citas 'lista_citas'en la sesión si no existe aún
if (!isset($_SESSION["lista_doctores"])) {
    $_SESSION["lista_doctores"] = [];
 
    $doctoresExistentes = [];
    $doctoresExistentes = PA_ListaDoctores();
 
 
}
// PASO D: Si se recibe una nueva cita individual por $_POST, se procesa
 
    // 1. Capturamos los datos que viajan en el POST
    if ($_SERVER["REQUEST_METHOD"] == "POST" &&
        !empty($_POST['licencia_doctor']) && !empty($_POST['nombre_doctor']) && !empty($_POST['apellido_doctor'])) {
            $licencia_doctor = $_POST['licencia_doctor'];
            $nombre_doctor = $_POST['nombre_doctor'];
            $apellido_doctor = $_POST['apellido_doctor'];
           
            // 2. Acumulamos la nueva cita dentro del array guardado en la SESIÓN
 
        $resultado = PA_AltaDoctor($_POST['licencia_doctor'],$_POST['nombre_doctor'],$_POST['apellido_doctor']);
        if ($resultado === -1) {
            $mensaje = "Nombre vacio";
        }elseif ($resultado === -2) {
            $mensaje = "Apellido vacio";
        }elseif ($resultado === -3) {
            $mensaje = "Doctor repetido";
        }elseif ($resultado === 0) {
            $_SESSION["lista_doctores"][] = [
            "licencia_doctor" => $licencia_doctor,
            "nombre_doctor"   => $nombre_doctor,
            "apellido_doctor" => $apellido_doctor
            ];
        }
   
    }
   
 
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Centro Médico - Mis Doctores</title>
</head>
<body>
 
    <h2>Gestión de Doctores</h2>
 
    <?php if (isset($mensaje)): ?>
        <p style="color: green; font-weight: bold;"><?php echo $mensaje; ?></p>
    <?php endif; ?>
 
    <h3>Doctores Registrados en esta Sesión (Total: <?php echo count($_SESSION['lista_doctores']); ?>)</h3>
 
    <!-- PASO E: Renderizar la tabla si existen citas guardadas -->
    <?php if (!empty($_SESSION['lista_doctores'])): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>licencia_doctor</th>
                    <th>nombre_doctor</th>
                    <th>apellido_doctor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['lista_doctores'] as $indice => $alta): ?>
                    <tr>
                        <td><?php echo $alta['licencia_doctor'] ?></td>
                        <td><?php echo $alta['nombre_doctor'] ?></td>
                        <td><?php echo $alta['apellido_doctor'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
 
 
    <?php else: ?>
        <p>No hay doctores actualmente.</p>
    <?php endif; ?>
 
    <br><hr>
    <a href="portal_citas.html"> +Añadir otro doctor</a>
 
</body>
</html>