<?php 
session_start();
require_once 'getthems.php';
require_once '../classes/blogsclass.php';

$blogs = new Blogs();
$article = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idsname'])) {
    $id = (int)$_POST['idsname'];
    $article = $blogs->getArticleById($id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article - L'Éco-conduite: Économisez du Carburant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .star {
            color: gray;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .star.selected {
            color: gold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-2xl font-bold text-blue-600">AutoLoc</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600">Accueil</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Blog</a>
                    <a href="myprofile.php" class="text-gray-600 hover:text-blue-600">Mon Profil</a>
                    <button id="addArticleBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Nouvel Article
                    </button>
                    <a href="logout.php" class="text-gray-600 hover:text-blue-600">Déconnexion</a>
                    <a href="myprofile.php" class="flex items-center">
                        <img src="https://img.freepik.com/vecteurs-premium/icone-profil-avatar-par-defaut-image-utilisateur-medias-sociaux-icone-avatar-gris-silhouette-profil-vierge-illustration-vectorielle_561158-3485.jpg?w=740" alt="Profile Image" class="w-10 h-10 rounded-full">
                        <span class="ml-2 text-gray-600">Jean Dupont</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 container mx-auto px-4">
        <?php if ($article): ?>
        <!-- Article Detail -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <img src="<?php echo $article['media_path']; ?>" 
                 alt="<?php echo htmlspecialchars($article['title']); ?>" 
                 class="w-full h-48 object-cover">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <?php if (isset($article['theme_name']) && $article['theme_name']): ?>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                            <?php echo htmlspecialchars($article['theme_name']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if (isset($article['tags']) && $article['tags']): ?>
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                            <span class="bg-gray-200 px-2 py-1 rounded text-sm">
                                <?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <h2 class="text-xl font-bold mb-2">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h2>
                <p class="text-gray-600 mb-4">
                    <?php 
                    $content = strip_tags($article['content']);
                    echo htmlspecialchars(substr($content, 0, 200)) . (strlen($content) > 200 ? '...' : '');
                    ?>
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <?php if (isset($article['author_name']) && $article['author_name']): ?>
                            <div class="flex items-center">
                                <img src="https://i.pinimg.com/736x/30/ae/26/30ae2638cd10d641e36fef8041f23752.jpg" alt="Author" class="h-10 w-10 rounded-full mr-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($article['author_name']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($article['formatted_date']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </article>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-xl font-bold mb-4">Ajouter un Commentaire et une Évaluation</h3>
            <form id="commentForm" action="create_comment.php" method="POST" class="space-y-4">
                <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Votre Commentaire</label>
                    <textarea name="content" rows="4" required class="w-full border rounded-lg px-4 py-2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Votre Évaluation</label>
                    <div class="stars flex space-x-1">
                        <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out" data-value="1">&#9733;</span>
                        <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out" data-value="2">&#9733;</span>
                        <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out" data-value="3">&#9733;</span>
                        <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out" data-value="4">&#9733;</span>
                        <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out" data-value="5">&#9733;</span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="">
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Publier</button>
                <?php else: ?>
                    <p class="text-gray-600">
                        <a href="login.php" class="text-blue-600 hover:text-blue-800">Connectez-vous</a>
                        pour ajouter un commentaire.
                    </p>
                <?php endif; ?>
            </form>
        </div>
        <?php else: ?>
        <div class="col-span-full text-center py-8">
            <p class="text-gray-600">Aucun article n'a été trouvé.</p>
        </div>
        <?php endif; ?>
    </div>
    <footer class="bg-gray-800 text-white  mt-8 py-8">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; 2023 AutoLoc. Tous droits réservés.</p>
            </div>
        </footer>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const stars = document.querySelectorAll('.star');
            let rating = 0;

            stars.forEach((star, index) => {
                star.addEventListener('mouseover', () => {
                    highlightStars(index);
                });

                star.addEventListener('mouseout', () => {
                    highlightStars(rating - 1);
                });

                star.addEventListener('click', () => {
                    rating = index + 1;
                    document.getElementById('rating').value = rating;
                    highlightStars(index);
                });
            });

            function highlightStars(index) {
                stars.forEach((star, i) => {
                    if (i <= index) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            }
        });
    </script>
</body>
</html>