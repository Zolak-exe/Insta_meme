<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['content_id']) || !isset($_POST['message'])) {
    header('Location: index.php');
    exit();
}

$stmt = $db->prepare('INSERT INTO commentaires (id_contenu, id_utilisateur, message, date_publication) VALUES (?, ?, ?, NOW())');
$stmt->execute([
    $_POST['content_id'],
    $_SESSION['user_id'],
    $_POST['message']
]);

header('Location: contenu.php?id=' . $_POST['content_id']);
?>
