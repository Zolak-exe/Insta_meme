<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE pseudo = ?');
    $stmt->execute([$pseudo]);
    $utilisateur = $stmt->fetch();
    
    // Comme le mot de passe est stocké en texte simple dans la base
    if ($utilisateur && $mot_de_passe === $utilisateur['mot_de_passe']) {
        $_SESSION['user_id'] = $utilisateur['id'];
        $_SESSION['pseudo'] = $utilisateur['pseudo'];
        header('Location: index.php');
        exit();
    } else {
        $error = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Instameme</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'navigation.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h1>Connexion</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div>
                    <label>Pseudo</label>
                    <input type="text" name="pseudo" required>
                </div>
                
                <div>
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                
                <button type="submit">Se connecter</button>
            </form>
            
            <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
        </div>
    </div>
</body>
</html>
