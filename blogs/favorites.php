<?php
session_start();
require_once '../classes/blogsclass.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$blogs = new Blogs();
$articles = $blogs->getUserFavorites($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Articles Favoris</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Mes Articles Favoris</h1>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($articles): ?>
                <?php foreach ($articles as $article): ?>
                    <article class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?php echo $article['media_path']; ?>"
                             alt="<?php echo htmlspecialchars($article['title']); ?>"
                             class="w-full h-48 object-cover">
                        
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <?php if ($article['theme_name']): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                        <?php echo htmlspecialchars($article['theme_name']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($article['tags']): ?>
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
                                <div class="flex items-center">
                                    <img src="https://i.pinimg.com/736x/30/ae/26/30ae2638cd10d641e36fef8041f23752.jpg" alt="Author" class="h-10 w-10 rounded-full mr-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($article['author_name']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($article['formatted_date']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <form action="somthing.php?idsname=<?php echo $article['id'];?>" method="GET">
                                        <input type="hidden" name="idsname" value="<?php echo $article['id']; ?>">
                                        <button class="text-blue-600 hover:text-blue-800">Lire la suite →</button>
                                    </form>
                                    
                                    <button 
                                        class="favorite-btn"
                                        data-article-id="<?php echo $article['id']; ?>"
                                        onclick="toggleFavorite(<?php echo $article['id']; ?>, this)"
                                    >
                                        <svg class="w-6 h-6 text-red-500 hover:text-red-600 transition-colors duration-300" 
                                            fill="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-600">Aucun article favori n'a été trouvé.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleFavorite(articleId, button) {
        fetch('toggle_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `article_id=${articleId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const svg = button.querySelector('svg');
                if (data.isFavorited) {
                    svg.classList.remove('text-gray-400');
                    svg.classList.add('text-red-500');
                } else {
                    svg.classList.remove('text-red-500');
                    svg.classList.add('text-gray-400');
                    // If we're on the favorites page, remove the article card
                    if (window.location.pathname.includes('favorites.php')) {
                        button.closest('article').remove();
                        // Check if there are any articles left
                        if (document.querySelectorAll('article').length === 0) {
                            location.reload(); // Reload to show "No favorites" message
                        }
                    }
                }
            }
        });
    }
    </script>
</body>
</html>