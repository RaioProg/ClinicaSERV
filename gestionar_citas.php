<?php
require_once "AccesoDatos.php";
session_start();

if (!isset($_SESSION["lista_citas"])) {
    $_SESSION["lista_citas"] = [];
}

$mensaje = null;
$registroCorrecto = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["accion"])) {
        $_SESSION["lista_citas"] = [];
    } elseif (
        isset($_POST["paciente"], $_POST["especialidad"], $_POST["fecha_hora"], $_POST["licencia_doctor"]) &&
        is_string($_POST["paciente"]) &&
        is_string($_POST["especialidad"]) &&
        is_string($_POST["fecha_hora"]) &&
        is_string($_POST["licencia_doctor"])
    ) {
        $paciente = trim($_POST["paciente"]);
        $especialidad = $_POST["especialidad"];
        $fechaEntrada = $_POST["fecha_hora"];
        $licenciaDoctor = filter_var($_POST["licencia_doctor"], FILTER_VALIDATE_INT);
        $fecha = DateTime::createFromFormat("!Y-m-d\TH:i", $fechaEntrada);
        $erroresFecha = DateTime::getLastErrors();

        if ($paciente === "" || $especialidad === "" || $licenciaDoctor === false) {
            $mensaje = "Completa todos los campos con datos válidos.";
        } elseif (
            $fecha === false ||
            ($erroresFecha !== false && ($erroresFecha["warning_count"] > 0 || $erroresFecha["error_count"] > 0)) ||
            $fecha->format("Y-m-d\TH:i") !== $fechaEntrada
        ) {
            $mensaje = "La fecha y hora no son válidas.";
        } elseif (!PA_ExisteDoctor($licenciaDoctor)) {
            $mensaje = "No existe un doctor con ese número de licencia.";
        } else {
            $fechaHora = $fecha->format("Y-m-d H:i:s");
            $resultado = ejecutarAltaCita($paciente, $especialidad, $fechaHora, $licenciaDoctor);

            if ($resultado === 0) {
                $mensaje = "Cita registrada correctamente.";
                $registroCorrecto = true;
                $_SESSION["lista_citas"][] = [
                    "paciente" => $paciente,
                    "especialidad" => $especialidad,
                    "fecha_hora" => $fechaEntrada,
                    "licencia_doctor" => $licenciaDoctor
                ];
            } elseif ($resultado === -1) {
                $mensaje = "El nombre del paciente es obligatorio.";
            } elseif ($resultado === -2) {
                $mensaje = "La especialidad es obligatoria.";
            } elseif ($resultado === -3) {
                $mensaje = "La fecha y hora son obligatorias.";
            } elseif ($resultado === -4) {
                $mensaje = "La cita debe tener una fecha y hora futuras.";
            } elseif ($resultado === -5) {
                $mensaje = "No existe un doctor con ese número de licencia.";
            } else {
                $mensaje = "No se pudo registrar la cita (código $resultado).";
            }
        }
    } else {
        $mensaje = "Faltan datos para registrar la cita.";
    }
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
 
    <?php if ($mensaje !== null): ?>
        <p style="color: <?php echo $registroCorrecto ? 'green' : 'red'; ?>; font-weight: bold;">
            <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
        </p>
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
                    <th>Licencia del Doctor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['lista_citas'] as $indice => $cita): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cita['paciente'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($cita['especialidad'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($cita['fecha_hora'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$cita['licencia_doctor'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
 
        <!-- Formulario para vaciar la sesión -->
        <form action="gestionar_citas.php" method="post">
            <input type="hidden" name="accion" value="borrar_todas">
            <button type="submit">
                Vaciar citas de esta sesión
            </button>
        </form>
 
    <?php else: ?>
        <p>No hay citas agendadas actualmente.</p>
    <?php endif; ?>
 
    <br><hr>
    <a href="portal_citas.html"> +Pedir otra cita</a>
 
</body>
</html>