<?php
require_once 'config.php';

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($filetype, $allowed)) {
            $newname = uniqid() . '.' . $filetype;

            if (move_uploaded_file($_FILES['image']['tmp_name'], 'memes/' . $newname)) {
                $stmt = $db->prepare('INSERT INTO contenus (id_utilisateur, chemin_image, description, date_publication) VALUES (?, ?, ?, NOW())');
                $stmt->execute([$_SESSION['user_id'], $newname, $_POST['description']]);

                header('Location: index.php');
                exit();
            }
        }
    }
    $error = "Une erreur s'est produite lors de l'upload.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Créer un post - Instameme</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navigation.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Créer un nouveau post</h1>

            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>

                <button type="submit">Publier</button>
            </form>
        </div>
    </div>
</body>

</html>