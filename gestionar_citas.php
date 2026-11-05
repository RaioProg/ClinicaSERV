<?php
// PASO A: Iniciar o reanudar la sesión (OBLIGATORIO al principio)
session_start();
// PASO B: Inicializar el array de citas 'lista_citas'en la sesión si no existe aún
if (!isset($_SESSION["lista_citas"])) {
    $_SESSION["lista_citas"] = [];
}
// PASO C: Comprobar si se ha solicitado borrar todas las citas
    if (isset($_POST['accion'])){
        $_SESSION["lista_citas"] = [];
    }
// PASO D: Si se recibe una nueva cita individual por $_POST, se procesa
 
    // 1. Capturamos los datos que viajan en el POST
    if ($_SERVER["REQUEST_METHOD"] == "POST" &&
        !empty($_POST['paciente']) && !empty($_POST['especialidad']) && !empty($_POST['fecha_hora'])) {
            $paciente = $_POST['paciente'];
            $especialidad = $_POST['especialidad'];
            $fecha_hora = $_POST['fecha_hora'];
           
            // 2. Acumulamos la nueva cita dentro del array guardado en la SESIÓN
            array_push($_SESSION["lista_citas"], ["paciente" => $paciente, "especialidad" => $especialidad, "fecha_hora" => $fecha_hora]);
        }
 
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Centro Médico - Mis Citas</title>
</head>
<body>
 
    <h2>Gestión de Citas Médicas</h2>
 
    <?php if (isset($mensaje)): ?>
        <p style="color: green; font-weight: bold;"><?php echo $mensaje; ?></p>
    <?php endif; ?>
 
    <h3>Citas Registradas en esta Sesión (Total: <?php echo count($_SESSION['lista_citas']); ?>)</h3>
 
    <!-- PASO E: Renderizar la tabla si existen citas guardadas -->
    <?php if (!empty($_SESSION['lista_citas'])): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Especialidad</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['lista_citas'] as $indice => $cita): ?>
                    <tr>
                        <td><?php echo $cita['paciente'] ?></td>
                        <td><?php echo $cita['especialidad'] ?></td>
                        <td><?php echo $cita['fecha_hora'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
 
        <!-- Formulario para vaciar la sesión -->
        <form action="gestionar_citas.php" method="post">
            <input type="hidden" name="accion" value="borrar_todas">
            <button type="submit">
                Cancelar todas las citas
            </button>
        </form>
 
    <?php else: ?>
        <p>No hay citas agendadas actualmente.</p>
    <?php endif; ?>
 
    <br><hr>
    <a href="portal_citas.html"> +Pedir otra cita</a>
 
</body>
</html>