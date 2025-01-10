<?php 
session_start();
require_once 'getthems.php';
require_once '../classes/blogsclass.php';
$blogs = new blogs();
$articles=$blogs->getAllArticlesapproved(); 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
$currentPage = $page; 
$totalPages = 1; 
$totalItems = 0; 

$theme_id = isset($_GET['theme']) ? $_GET['theme'] : null;
$tag_id = isset($_GET['tag']) ? $_GET['tag'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

try {
    $result = $blogs->getFilteredArticlesWithPagination($theme_id, $tag_id, $search, $page, $itemsPerPage);
    $articles = $result['articles'];
    $totalPages = $result['totalPages'];
    $currentPage = $result['currentPage'];
    $totalItems = $result['totalItems'];
} catch (Exception $e) {
    $articles = [];
    error_log("Error fetching articles: " . $e->getMessage());
}

$recentArticle = null;
if (!empty($articles)) {
    $recentArticle = $articles[0];
    unset($articles[0]);
}

$userId = $_SESSION['user_id'] ?? null;
$imageProfile = $userId ? $blogs->getProfileImage($userId) : null;
if (empty($imageProfile)) {
    $imageProfile = 'https://img.freepik.com/vecteurs-premium/icone-profil-avatar-par-defaut-image-utilisateur-medias-sociaux-icone-avatar-gris-silhouette-profil-vierge-illustration-vectorielle_561158-3485.jpg?w=740';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - AutoLoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <a href="../blogs/blog.php" class="text-gray-600 hover:text-blue-600">Blog</a>
                    <?php if(isset($_SESSION['user_id'])): ?> 
                <a href="../users/myreview.php" class="block text-gray-600 hover:text-blue-600">myreviesws</a>
                <a href="users/myreservation.php" class="block text-gray-600 hover:text-blue-600">Myreservations</a>
                        <button class="relative group">
                            <img src=" <?php echo  $imageProfile?>  " alt="Profile Image" class="w-10 h-10 rounded-full border-2 border-transparent group-hover:border-blue-600 transition-all duration-300">
                            <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right">
                                <div class="py-2">
                                    <a href="myprofile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Mon Profil</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Paramètres</a>
                                    <a href="../connexion/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</a>
                                </div>
                            </div>
                        </button>
                    <?php else: ?>
                        <a href="../connexion/singin.php" class="block text-gray-600 hover:text-blue-600">Sign Up</a>
                        <a href="../connexion/singin.php" class="block text-gray-600 hover:text-blue-600">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="pt-24 container mx-auto px-4">
        <!-- Search and Filters -->
        <form method="GET" action="index.php" class="flex flex-wrap gap-4">
    <div class="flex-1">
        <input type="text" name="search" placeholder="Rechercher un article..." class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
    </div>
    <div class="w-full md:w-auto">
        <select name="theme" class="border rounded-lg px-4 py-2">
            <option value="">Tous les thèmes</option>
            <?php foreach($themes as $theme): ?>
                <option value="<?= $theme['id'] ?>" <?php if(isset($_GET['theme']) && $_GET['theme'] == $theme['id']) echo 'selected'; ?>>
                    <?= htmlspecialchars($theme['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        Rechercher
    </button>
</form>
        <div class="bg-white shadow rounded-lg p-4 mb-6">
        <?php if(isset($_SESSION['user_id'])): ?>
    <div class="flex items-center gap-3">
        <img id="userAvatar" src="<?php echo $imageProfile?> "alt="Profile" class="w-10 h-10 rounded-full">
        <button id="createPostBtn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-500 text-left px-4 py-2.5 rounded-full cursor-pointer transition-colors duration-200">
            Créer votre article...
        </button>
    </div>
    <?php else: ?>
        <div class="flex items-center gap-3">
        <img  src="https://i.pinimg.com/736x/30/ae/26/30ae2638cd10d641e36fef8041f23752.jpg" alt="Profile" class="w-10 h-10 rounded-full">
        <button id="closenPostBtn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-500 text-left px-4 py-2.5 rounded-full cursor-pointer transition-colors duration-200">
            Créer votre article...
        </button>
    </div>
    <p class="favoris text-sm text-red-500 mt-2 hidden ">Vous devez avoir un compte pour ajouter une article!.</p>
    <?php endif; ?>
</div>

<!-- Create Post Modal -->
<div id="createPostModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white rounded-lg w-full max-w-lg mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Créer un article</h2>
            <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="createPostForm" class="space-y-4"  action="setarticle.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                <input type="text" id="title" name="title" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="theme" class="block text-sm font-medium text-gray-700 mb-1">Thème</label>
                <select id="theme" name="theme" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les thèmes</option>
                        <?php foreach($themes as $theme): ?>
                            <option name="theme" value="<?= $theme['id'] ?>"><?= htmlspecialchars($theme['name']) ?></option>
                        <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Contenu</label>
                <textarea id="content" name="content" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div>
                <label for="media" class="block text-sm font-medium text-gray-700 mb-1">Image (optionnel)</label>
                <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-gray-300 border-dashed rounded-lg">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Télécharger un fichier</span>
                                <input id="media" name="image" type="file"    class="sr-only" require>
                            </label>
                            <p class="pl-1">ou glisser-déposer</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4 mt-4">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-medium text-gray-700">Tags</label>
                    <button type="button" id="addTagBtn" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Ajouter un tag
                    </button>
                </div>
    
    <div id="tagsContainer" class="space-y-2">
    </div>
</div>
            <div class="flex justify-end gap-4">
                <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Publier
                </button>
            </div>
        </form>

    </div>
</div>
        <!-- Articles List -->
        <div id="articlesList" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template id="articleTemplate">
                <article class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="" alt="" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="theme-badge px-2 py-1 rounded text-sm"></span>
                            <div class="tags space-x-1"></div>
                        </div>
                        <h2 class="text-xl font-bold mb-2 article-title"></h2>
                        <p class="text-gray-600 mb-4 article-excerpt"></p>
                        <div class="flex items-center justify-between">
                            <button class="read-more text-blue-600 hover:text-blue-800">Lire la suite</button>
                            <button class="favorite-btn">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>
            </template>
        </div>
        <div class="container mx-auto px-4 py-8">
        <article class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="md:flex">
    <!-- Featured Article -->
    <?php if ($recentArticle): ?>
    <div class="container mx-auto px-4 py-8">
    <h1 class="text-5xl font-bold text-center">New Article</h1>

        <article class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="md:flex">
                <div class="md:flex-shrink-0">
                    <img src="<?php echo htmlspecialchars($recentArticle['media_path']); ?>" alt="<?php echo htmlspecialchars($recentArticle['title']); ?>" class="h-48 w-full object-contain md:w-80">
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><?php echo htmlspecialchars($recentArticle['theme_name']); ?></span>
                        <?php if ($recentArticle['tags']): ?>
                            <?php foreach (explode(',', $recentArticle['tags']) as $tag): ?>
                                <span class="bg-gray-200 px-2 py-1 rounded text-sm"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($recentArticle['title']); ?></h2>
                    <p class="text-gray-600 mb-4"><?php 
                        $content = strip_tags($recentArticle['content']);
                        echo htmlspecialchars(substr($content, 0, 200)) . (strlen($content) > 200 ? '...' : '');
                    ?></p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <?php $profileImage = $blogs->getProfileImage($recentArticle['user_id']); ?>
                            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Author" class="h-10 w-10 rounded-full mr-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($recentArticle['author_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($recentArticle['formatted_date']); ?></p>
                            </div>
                        </div>
                        <a href="article.php?id=<?php echo htmlspecialchars($recentArticle['id']); ?>" class="text-blue-600 hover:text-blue-800">Lire la suite →</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </article>
    </div>
            </div>
        </article>

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
                        echo htmlspecialchars(substr($content, 0, 200)) . (strlen($content) > 10 ? '...' : '');
                        ?>
                    </p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <?php if ($article['author_name']): ?>
                                <div class="flex items-center">
                                <?php    $profileImage = $blogs->getProfileImage($article['user_id']);
                                if ($profileImage): ?>
                                    <img id="profileImage" class="h-12 w-12 mr-3 rounded-full object-cover border-2 border-blue-500" 
                                         src="<?php echo htmlspecialchars($profileImage); ?>" alt="Author">
                                <?php endif; ?>
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
    
        <div class="mt-8 flex justify-center gap-2">
    <?php if ($currentPage > 1): ?>
        <a href="?page=<?php echo ($currentPage - 1); ?>&perPage=<?php echo htmlspecialchars($itemsPerPage); ?><?php 
            echo $theme_id ? '&theme=' . htmlspecialchars($theme_id) : ''; 
            echo $tag_id ? '&tag=' . htmlspecialchars($tag_id) : ''; 
            echo $search ? '&search=' . htmlspecialchars(urlencode($search)) : ''; 
        ?>" 
        class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">
            ← Précédent
        </a>
    <?php endif; ?>

    <?php
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1): ?>
        <a href="?page=1&perPage=<?php echo htmlspecialchars($itemsPerPage); ?><?php 
            echo $theme_id ? '&theme=' . htmlspecialchars($theme_id) : ''; 
            echo $tag_id ? '&tag=' . htmlspecialchars($tag_id) : ''; 
            echo $search ? '&search=' . htmlspecialchars(urlencode($search)) : ''; 
        ?>" 
        class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">1</a>
        <?php if ($startPage > 2): ?>
            <span class="px-4 py-2">...</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <a href="?page=<?php echo $i; ?>&perPage=<?php echo htmlspecialchars($itemsPerPage); ?><?php 
            echo $theme_id ? '&theme=' . htmlspecialchars($theme_id) : ''; 
            echo $tag_id ? '&tag=' . htmlspecialchars($tag_id) : ''; 
            echo $search ? '&search=' . htmlspecialchars(urlencode($search)) : ''; 
        ?>" 
        class="px-4 py-2 <?php echo $i === $currentPage ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 hover:bg-blue-50'; ?> rounded-lg">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($endPage < $totalPages): ?>
        <?php if ($endPage < $totalPages - 1): ?>
            <span class="px-4 py-2">...</span>
        <?php endif; ?>
        <a href="?page=<?php echo $totalPages; ?>&perPage=<?php echo htmlspecialchars($itemsPerPage); ?><?php 
            echo $theme_id ? '&theme=' . htmlspecialchars($theme_id) : ''; 
            echo $tag_id ? '&tag=' . htmlspecialchars($tag_id) : ''; 
            echo $search ? '&search=' . htmlspecialchars(urlencode($search)) : ''; 
        ?>" 
        class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50"><?php echo $totalPages; ?></a>
    <?php endif; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="?page=<?php echo ($currentPage + 1); ?>&perPage=<?php echo htmlspecialchars($itemsPerPage); ?><?php 
            echo $theme_id ? '&theme=' . htmlspecialchars($theme_id) : ''; 
            echo $tag_id ? '&tag=' . htmlspecialchars($tag_id) : ''; 
            echo $search ? '&search=' . htmlspecialchars(urlencode($search)) : ''; 
        ?>" 
        class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50">
            Suivant →
        </a>
    <?php endif; ?>
</div>


<!-- Footer -->
<footer class="bg-gray-800 text-white  mt-8 py-8">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; 2023 AutoLoc. Tous droits réservés.</p>
            </div>
        </footer>


    

   

    <script>

document.addEventListener('DOMContentLoaded', () => {
    const favoris = document.querySelector('.favoris');
    const heart = document.querySelector('#closenPostBtn');

    heart.addEventListener('mouseover', () => {
        favoris.classList.remove('hidden');
    });

    heart.addEventListener('mouseout', () => {
        setTimeout(() => {
            favoris.classList.add('hidden');
        }, 500); 
    });
});


const modal = document.getElementById('createPostModal');
const createPostBtn = document.getElementById('createPostBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');
const createPostForm = document.getElementById('createPostForm');

createPostBtn.addEventListener('click', () => {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; 
});

const closeModal = () => {
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    createPostForm.reset(); 
};

closeModalBtn.addEventListener('click', closeModal);
cancelBtn.addEventListener('click', closeModal);

modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeModal();
    }
});
const addTagBtn = document.getElementById('addTagBtn');
    const tagsContainer = document.getElementById('tagsContainer');
    
    function isLastInputEmpty() {
        const tagInputs = tagsContainer.querySelectorAll('input[name="tags[]"]');
        if (tagInputs.length === 0) return false;
        const lastInput = tagInputs[tagInputs.length - 1];
        return !lastInput.value.trim();
    }

    function showError(input) {
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) existingError.remove();

        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message text-red-500 text-sm mt-1';
        errorMessage.textContent = 'Veuillez remplir ce tag avant d\'en ajouter un nouveau';
        input.parentElement.appendChild(errorMessage);

        input.classList.add('border-red-500');

        setTimeout(() => {
            errorMessage.remove();
            input.classList.remove('border-red-500');
        }, 3000);
    }
    
    function createTagInput() {
    if (isLastInputEmpty()) {
        const tagInputs = tagsContainer.querySelectorAll('input[name="tags[]"]');
        const lastInput = tagInputs[tagInputs.length - 1];
        showError(lastInput);
        lastInput.focus();
        return;
    }

    const tagInputGroup = document.createElement('div');
    tagInputGroup.className = 'flex items-center gap-2';
    
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'tags[]'; 
    input.className = 'flex-1 border rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500';
    input.placeholder = 'Entrez un tag';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'inline-flex items-center p-1 border border-transparent rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
    removeBtn.innerHTML = `
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    `;
    
    removeBtn.addEventListener('click', () => {
        tagInputGroup.remove();
    });
    
    input.addEventListener('input', (e) => {
        const words = e.target.value.trim().split(/\s+/);
        if (words.length > 1) {
            e.target.value = words[0];
        }
    });
    
    tagInputGroup.appendChild(input);
    tagInputGroup.appendChild(removeBtn);
    tagsContainer.appendChild(tagInputGroup);
    
    input.focus();
}
    
    if (tagsContainer.children.length === 0) {
        createTagInput();
    }
    
    addTagBtn.addEventListener('click', createTagInput);
   
        </script>
</body>
</html>

