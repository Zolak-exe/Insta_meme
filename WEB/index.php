<?php
require_once 'config.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$nbContenusParPage = 9;
$offset = ($page - 1) * $nbContenusParPage;

$stmt = $db->query("SELECT COUNT(*) FROM contenus");
$nbTotal = $stmt->fetchColumn();
$nbPages = ceil($nbTotal / $nbContenusParPage);

$query = "SELECT c.*, u.pseudo, u.id AS id_utilisateur,
          (SELECT COUNT(*) FROM likes WHERE id_contenu = c.id) as nb_likes,
          (SELECT COUNT(*) FROM commentaires WHERE id_contenu = c.id) as nb_comments
          FROM contenus c
          LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
          ORDER BY c.date_publication DESC
          LIMIT :offset, :limit";

$stmt = $db->prepare($query);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $nbContenusParPage, PDO::PARAM_INT);
$stmt->execute();
$contenus = $stmt->fetchAll();

// Récupérer les commentaires pour chaque contenu
foreach ($contenus as &$contenu) {
    $stmt = $db->prepare("
        SELECT c.message, u.pseudo, u.id AS id_utilisateur 
        FROM commentaires c
        JOIN utilisateurs u ON c.id_utilisateur = u.id
        WHERE c.id_contenu = ?
        ORDER BY c.date_publication DESC
    ");
    $stmt->execute([$contenu['id']]);
    $contenu['commentaires'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Instameme</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php require_once 'navigation.php'; ?>

    <div class="container">
        <div class="posts-grid">
            <?php foreach ($contenus as $contenu): ?>
                <div class="post-card">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="contenu.php?id=<?= $contenu['id'] ?>">
                            <img src="memes/<?= htmlspecialchars($contenu['chemin_image']) ?>" alt="meme">
                        </a>
                        <div class="post-info">
                            <p class="post-author">
                                Partagé par <a href="profil.php?id=<?= $contenu['id_utilisateur'] ?>"><?= htmlspecialchars($contenu['pseudo']) ?></a>
                            </p>
                            <div class="post-actions">
                                <form method="post" action="like.php" class="like-btn">
                                    <input type="hidden" name="content_id" value="<?= $contenu['id'] ?>">
                                    <button type="submit">
                                        <i class="fas fa-heart"></i>
                                        <span><?= $contenu['nb_likes'] ?> J'aime</span>
                                    </button>
                                </form>
                                <button class="share-btn">
                                    <i class="fas fa-share-alt"></i>
                                    <span>Partager</span>
                                </button>
                            </div>
                            <div class="comments-section">
                                <?php foreach ($contenu['commentaires'] as $commentaire): ?>
                                    <div class="comment">
                                        <a href="profil.php?id=<?= $commentaire['id_utilisateur'] ?>">
                                            <span class="comment-author"><?= htmlspecialchars($commentaire['pseudo']) ?></span>
                                        </a>
                                        <span class="comment-text"><?= htmlspecialchars($commentaire['message']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <form method="post" action="commenter.php" class="comment-form">
                                    <input type="hidden" name="content_id" value="<?= $contenu['id'] ?>">
                                    <input type="text" name="message" placeholder="Commenter..." required>
                                    <button type="submit">Commenter</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="blurred-content">
                            <img src="memes/<?= htmlspecialchars($contenu['chemin_image']) ?>" alt="meme" class="blurred">
                            <div class="login-overlay">
                                <p>Connectez-vous pour voir le contenu</p>
                                <a href="connexion.php" class="btn-login">Se connecter</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $nbPages; $i++): ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.share-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                alert('Partager le contenu');
            });
        });
    </script>
</body>

</html>