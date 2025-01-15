<?php
require_once 'config.php';

if (isset($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE pseudo LIKE ?");
    $stmt->execute([$search]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur) {
        header('Location: profil.php?id=' . $utilisateur['id']);
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
}

header('Location: index.php');
exit();
