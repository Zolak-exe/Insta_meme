<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$contentId = (int)$_GET['id'];

// Get image path
$stmt = $db->prepare("
    SELECT chemin_image 
    FROM contenus
    WHERE id = ?
");
$stmt->execute([$contentId]);
$imagePath = $stmt->fetchColumn();

// Get comments with user pseudos
$stmt = $db->prepare("
    SELECT c.message, u.pseudo
    FROM commentaires c
    JOIN utilisateurs u ON c.id_utilisateur = u.id
    WHERE c.id_contenu = ?
    ORDER BY c.date_publication DESC
");
$stmt->execute([$contentId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get like count
$stmt = $db->prepare("
    SELECT COUNT(*) 
    FROM likes
    WHERE id_contenu = ?
");
$stmt->execute([$contentId]);
$likeCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Instameme - Contenu</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php require_once 'navigation.php'; ?>
    <div class="container">
        <div class="content-view">
            <div class="content-image">
                <img src="memes/<?= htmlspecialchars($imagePath) ?>" alt="Meme">
            </div>
            <div class="content-info">
                <div class="likes">
                    <i class="fas fa-heart"></i> <?= $likeCount ?> J'aime
                </div>
            </div>
            <div class="comments-section">
                <div class="comments-header">Commentaires :</div>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <span class="comment-author"><?= htmlspecialchars($comment['pseudo']) ?></span>
                        <span class="comment-text"><?= htmlspecialchars($comment['message']) ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form class="comment-form" method="post" action="commenter.php">
                        <input type="hidden" name="content_id" value="<?= $contentId ?>">
                        <input type="text" name="message" placeholder="Commenter..." required>
                        <button type="submit">Commenter</button>
                    </form>
                <?php else: ?>
                    <p>Vous devez être connecté pour commenter.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>