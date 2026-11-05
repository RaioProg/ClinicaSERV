<?php
$host = 'localhost';
$db   = 'clinica';
$user = 'victordb';
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
 
    $res = $pdo->query("SELECT @res AS resultado")->fetch();
    return (int)$res['resultado'];
}
 
function ejecutarAltaCita($paciente, $especialidad, $fecha) {
    global $pdo;
    $stmt = $pdo->prepare("CALL AltaCita(?, ?, ?, @res)");
    $stmt->execute([$paciente, $especialidad, $fecha]);
 
    $res = $pdo->query("SELECT @res AS resultado")->fetch();
    return (int)$res['resultado'];
}
