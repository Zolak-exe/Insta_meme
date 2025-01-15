<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    // Vérifier si le pseudo existe déjà
    $stmt = $db->prepare('SELECT id FROM utilisateurs WHERE pseudo = ?');
    $stmt->execute([$pseudo]);
    if ($stmt->fetch()) {
        $error = "Ce pseudo est déjà utilisé";
    } else {
        try {
            $stmt = $db->prepare('INSERT INTO utilisateurs (pseudo, mot_de_passe, date_inscription) VALUES (?, ?, NOW())');
            $stmt->execute([$pseudo, $mot_de_passe]);
            
            // Connexion automatique après inscription
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['pseudo'] = $pseudo;
            
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Instameme</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'navigation.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h1>Inscription</h1>
            
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
                
                <button type="submit">S'inscrire</button>
            </form>
            
            <p>Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </div>
</body>
</html>
