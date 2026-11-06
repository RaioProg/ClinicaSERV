<?php
$host = 'localhost';
$db   = 'clinica';
$user = 'VictorBD';
$pass = 'miguelinnieto23';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error al conectar: " . $e->getMessage());
}
function PA_ListaDoctores() {
    global $pdo;
    $stmt = $pdo->prepare("CALL ListaDoctores");
    $stmt->execute();
   
}
function PA_AltaDoctor($licencia, $nombre, $apellidos) {
    global $pdo;
    $stmt = $pdo->prepare("CALL AltaDoctor(?, ?, ?, @res)");
    $stmt->execute([$licencia, $nombre, $apellidos]);
    $stmt->closeCursor();
 
    $res = $pdo->query("SELECT @res AS resultado")->fetch();
    return (int)$res['resultado'];
}

function PA_ExisteDoctor($licencia) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 1 FROM doctores WHERE n_licencia = ? LIMIT 1");
    $stmt->execute([$licencia]);
    return $stmt->fetchColumn() !== false;
}
 
function ejecutarAltaCita($paciente, $especialidad, $fecha, $licenciaDoctor) {
    global $pdo;
    $stmt = $pdo->prepare("CALL AltaCita(?, ?, ?, ?, @res)");
    $stmt->execute([$paciente, $especialidad, $fecha, $licenciaDoctor]);
    $stmt->closeCursor();

    $res = $pdo->query("SELECT @res AS resultado, LAST_INSERT_ID() AS id_cita")->fetch();
    return [
        "resultado" => (int)$res["resultado"],
        "id_cita" => (int)$res["id_cita"]
    ];
}

function PA_BorrarCita($idCita) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM citasMedicas WHERE id_cita = ?");
    $stmt->execute([$idCita]);
    return $stmt->rowCount() === 1;
}
