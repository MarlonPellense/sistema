<?php

$usuario = 'root';
$senha = '';
$databse = 'cantina';
$host = 'localhost';

$mysqli = new mysqli($host, $usuario, $senha, $databse);

if ($mysqli->connect_error) {
    die("Falha ao conectar ao banco de dados!". $mysqli->connect_error);
}
?>