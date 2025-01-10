<?php
session_start();
require_once 'getthems.php';
require_once '../classes/blogsclass.php';

$blogs = new Blogs();

$blogs = new Blogs();
$theme_id = isset($_POST['theme_id']) ? (int)$_POST['theme_id'] : null;
$tag_id = isset($_POST['tag_id']) ? (int)$_POST['tag_id'] : null;
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$per_page = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 5;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

$total_pages = ceil($total_articles / $per_page);

$response = [
    'articles' => $articles,
    'total_pages' => $total_pages,
    'current_page' => $page
];


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - AutoLoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="text-gray-600 hover:text-blue-600">Mon Profil</a>
                        <button id="addArticleBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Nouvel Article
                        </button>
                        <a href="logout.php" class="text-gray-600 hover:text-blue-600">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-blue-600">Connexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 container mx-auto px-4">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form id="searchForm" class="flex flex-wrap gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Rechercher un article..." 
                    class="w-full border rounded-lg px-4 py-2">
                </div>
                <div class="w-full md:w-auto">
                    <select id="themeFilter" class="border rounded-lg px-4 py-2">
                        <option value="">Tous les thèmes</option>
                        <?php foreach($themes as $theme): ?>
                            <option value="<?= $theme['id'] ?>"><?= htmlspecialchars($theme['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                    <select id="tagFilter" class="border rounded-lg px-4 py-2">
                        <option value="">Tous les tags</option>
                        <?php foreach($tags as $tag): ?>
                            <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                    <select id="perPage" class="border rounded-lg px-4 py-2">
                        <option value="5">5 par page</option>
                        <option value="10">10 par page</option>
                        <option value="15">15 par page</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Articles List -->
        <div id="articlesList" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
         <!-- Regular Articles Grid -->
         <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if ($articles): 
        ?>
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
                        <div class="flex items-center space-x-4">
                            <?php if ($article['author_name']): ?>
                                <div class="flex items-center">
                            <img id="profileImage" class="h-12 w-12 mr-3 rounded-full object-cover border-2 border-blue-500" 
                                src="<?php echo $image_paths ?>" alt="Author" >
                            <div>
                                <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($article['author_name']); ?></p>
                                <p class="text-sm text-gray-500"> <?php echo htmlspecialchars($article['formatted_date']); ?>
                                </p>
                            </div>
                        </div>
                            <?php endif; ?>
                        </div>
                        <form action="somthing.php?idsname=<?php echo $article['id'];?>" method="GET">
                            <input type="hidden" name="idsname" value="<?php echo $article['id']; ?>">
                            <button class="text-blue-600 hover:text-blue-800">Lire la suite →</button>
                        </form>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="toggle_favorite.php" method="POST">
    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">     
    <button 
        class="favorite-btn"
        data-article-id="<?php echo $article['id']; ?>"
    >
        <svg class="w-6 h-6 <?php echo $blogs->isArticleFavorited($_SESSION['user_id'], $article['id']) ? 'text-red-500' : 'text-gray-400'; ?> hover:text-red-500 transition-colors duration-300" 
            fill="currentColor" 
            viewBox="0 0 24 24"
        >
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
    </button>
</form>
    <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-8">
            <p class="text-gray-600">Aucun article n'a été trouvé.</p>
        </div>
    <?php endif; ?>
</div>
        </div>
    
        <!-- Pagination -->
        <div class="mt-8 flex justify-center gap-2">
            <button class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">← Précédent</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">2</button>
            <button class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">3</button>
            <button class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">Suivant →</button>
        </div>
    </div>
    


    <script>
      
    </script>
</body>
</html>