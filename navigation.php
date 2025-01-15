<nav>
    <div class="nav-container">
        <a href="index.php" class="logo">Instameme</a>

        <form action="recherche.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Rechercher...">
        </form>

        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="creer.php">Créer</a>
                <a href="profil.php?id=<?php echo $_SESSION['user_id']; ?>">Profil</a>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>