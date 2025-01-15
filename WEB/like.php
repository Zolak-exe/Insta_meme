<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['content_id'])) {
    header('Location: index.php');
    exit();
}

$content_id = (int)$_POST['content_id'];
$user_id = $_SESSION['user_id'];

// Vérifier si déjà liké
$stmt = $db->prepare("SELECT id_contenu FROM likes WHERE id_contenu = ? AND id_utilisateur = ?");
$stmt->execute([$content_id, $user_id]);
$existing_like = $stmt->fetch();

if ($existing_like) {
    // Supprimer le like
    $stmt = $db->prepare("DELETE FROM likes WHERE id_contenu = ? AND id_utilisateur = ?");
    $stmt->execute([$content_id, $user_id]);
} else {
    // Ajouter le like
    $stmt = $db->prepare("INSERT INTO likes (id_contenu, id_utilisateur) VALUES (?, ?)");
    $stmt->execute([$content_id, $user_id]);
}

// Rediriger vers la page précédente
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
