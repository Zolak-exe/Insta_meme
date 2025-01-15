<?php
try {
    $db = new PDO('mysql:host=127.0.0.1:3306;dbname=insta_meme;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : '.$e->getMessage());
}
session_start();
?>
