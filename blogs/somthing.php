<?php 
session_start();
require_once 'getthems.php';
require_once '../classes/blogsclass.php';

$blogs = new Blogs();
$article = null;
$comments = [];
$authorImage = 'path/to/default/author/image.jpg'; // Default author image path
$commenterImages = []; // Array to store commenter images

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['idsname'])) {
    $id = (int)$_GET['idsname'];
    $article = $blogs->getArticleById($id);
    $comments = $blogs->getCommentsByArticleId($id);

    if (!empty($article['media_path'])) {
        $image_path = htmlspecialchars($article['media_path']);
    } else {
        $image_path = 'path/to/default/image.jpg';
    }

    // Fetch author image
    if (!empty($article['user_id'])) {
        $authorImage = $blogs->getProfileImage($article['user_id']) ?? $authorImage;
    }

    // Fetch commenter images
    foreach ($comments as $comment) {
        $commenterImages[$comment['user_id']] = $blogs->getProfileImage($comment['user_id']) ?? 'path/to/default/commenter/image.jpg';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article - <?= htmlspecialchars($article['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .blur-load {
            background-size: cover;
            background-position: center;
        }
        .blur-load.loaded > img {
            opacity: 1;
        }
        .blur-load > img {
            opacity: 0;
            transition: opacity 200ms ease-in-out;
        }
        .progress-bar {
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gray-200 z-50">
        <div class="progress-bar h-full bg-blue-600"></div>
    </div>

    <!-- Navigation -->
<nav class="bg-white/80 backdrop-blur-md shadow-lg fixed w-full z-40 transition-all duration-300">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center space-x-2 group">
                <svg class="w-8 h-8 text-blue-600 transform group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text text-transparent">AutoLoc</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="../index.php" class="nav-link relative text-gray-600 hover:text-blue-600 transition-colors duration-300">
                    Accueil
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="../blogs/blog.php" class="nav-link relative text-gray-600 hover:text-blue-600 transition-colors duration-300">Blog</a>
               
                <?php if(isset($_SESSION['user_id'])): ?> 
                <a href="../users/myreview.php" class="block text-gray-600 hover:text-blue-600">myreviesws</a>
                <a href="users/myreservation.php" class="block text-gray-600 hover:text-blue-600">Myreservations</a>
                        <button class="relative group">
                            <img src="https://img.freepik.com/vecteurs-premium/icone-profil-avatar-par-defaut-image-utilisateur-medias-sociaux-icone-avatar-gris-silhouette-profil-vierge-illustration-vectorielle_561158-3485.jpg?w=740" alt="Profile Image" class="w-10 h-10 rounded-full border-2 border-transparent group-hover:border-blue-600 transition-all duration-300">
                            <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right">
                                <div class="py-2">
                                    <a href="myprofile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Mon Profil</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Paramètres</a>
                                    <a href="../connexion/" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</a>
                                </div>
                            </div>
                        </button>
                    <?php else: ?>
                        <a href="signup.php" class="block text-gray-600 hover:text-blue-600">Sign Up</a>
                        <a href="login.php" class="block text-gray-600 hover:text-blue-600">Login</a>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

    <!-- Hero Section -->
    <div class="relative min-h-screen pt-20">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-600/10 to-white pointer-events-none"></div>
        <div class="relative container mx-auto px-4 pt-12">
            <!-- Article Header -->
            <div class="max-w-4xl mx-auto mb-12 opacity-0" id="articleHeader">
                <div class="flex flex-wrap gap-3 mb-6">
                    <?php if ($article['theme_name']): ?>
                        <span class="px-4 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <?php echo htmlspecialchars($article['theme_name']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($article['tags']): ?>
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                            <span class="px-4 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h1>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <img 
                            id="profileImage"
                            class="h-24 w-24 rounded-full object-cover border-2 border-blue-500" 
                            src="<?php echo $authorImage; ?>" alt="Author"> 
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($article['author_name']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($article['formatted_date']); ?></p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-300">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        </button>
                        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-300">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                        </button>
                        <?php if(isset($_SESSION['user_id'])): ?>
    <form class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-300" action="toggle_favorite.php" method="POST">
        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">     
        <button class="favorite-btn" data-article-id="<?php echo $article['id']; ?>">
            <svg class="w-6 h-6 <?php echo $blogs->isArticleFavorited($_SESSION['user_id'], $article['id']) ? 'text-red-500' : 'text-gray-400'; ?> hover:text-red-500 transition-colors duration-300" 
                fill="currentColor" 
                viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </button>
    </form>
<?php else: ?>
    <div class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-300">
    <svg class="heart w-6 h-6 text-gray-400 hover:text-red-500 transition-colors duration-300"
        fill="currentColor" 
        viewBox="0 0 24 24">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
    </svg>
</div>
<p class="favoris text-sm text-gray-500 mt-2 hidden">Vous devez avoir un compte pour ajouter aux favoris.</p>

<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto mb-12 rounded-2xl overflow-hidden shadow-2xl opacity-0" id="articleImage">
                <div class="blur-load" style="background-image: url('<?php echo $image_path ?>');">
                    <img src="<?php echo $image_path ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-[500px] object-cover">
                </div>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-12 opacity-0" id="articleContent">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        <?php echo htmlspecialchars($article['content']); ?>
                    </p>
                </div>
                <div class="mt-8 text-center">
                    <h3 class="text-2xl font-bold mb-4">Commentaires Précédents</h3>
                    <div id="commentsContainer">
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $index => $comment): ?>
                                <div class="comment mb-4 p-4 bg-gray-100 rounded-lg" style="<?php echo $index >= 5 ? 'display: none;' : ''; ?>">
                                    <div class="flex items-center">
                                        <img 
                                            class="h-12 w-12 rounded-full object-cover border-2 border-blue-500 mr-3" 
                                            src="<?php echo $commenterImages[$comment['user_id']]; ?>" 
                                            alt="<?php echo htmlspecialchars($comment['firstName']); ?>"> 
                                        <span class="text-lg font-semibold"><?php echo htmlspecialchars($comment['firstName'] . ' ' . $comment['lastName']); ?></span>
                                        <span class="ml-auto text-yellow-500">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <?php if ($i < $comment['rating']): ?>
                                                    ★
                                                <?php else: ?>
                                                    ☆
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <p class="mt-2 text-gray-700"><?php echo htmlspecialchars($comment['content']); ?></p>
                                    <p class="mt-1 text-gray-500 text-sm"><?php echo htmlspecialchars($comment['created_at']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-600">Aucun commentaire pour le moment.</p>
                        <?php endif; ?>
                    </div>
                    <button id="loadMore" class="text-blue-600 hover:underline mb-4 mt-4 mr-10">Voir Plus</button>
                    <button id="loadLess" class="text-blue-600 hover:underline mb-4 mt-4" style="display: none;">Voir Moins</button>
                </div>
                <div class="bg-white rounded-2xl shadow-xl p-8 opacity-0" id="commentSection">
                    <h3 class="text-2xl font-bold mb-8">Commentaires et Évaluations</h3>
                    <form id="commentForm" action="create_comment.php" method="POST" class="space-y-4">
                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                        <div class="mb-8">
                            <div class="flex gap-4 mb-6">
                                <div class="stars flex space-x-1">
                                    <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="1">★</span>
                                    <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="2">★</span>
                                    <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="3">★</span>
                                    <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="4">★</span>
                                    <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="5">★</span>
                                </div>
                                <input type="hidden" name="rating" id="rating">
                            </div>
                            <textarea 
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"
                                rows="4" name="content"
                                placeholder="Partagez votre avis..."
                            ></textarea>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-full hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-blue-300/50">Publier</button>
                            <?php else: ?>
                                <p class="text-gray-600">
                                    <a href="login.php" class="text-blue-600 hover:text-blue-800">Connectez-vous</a>
                                    pour ajouter un commentaire.
                                </p>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-gray-800 text-white  mt-8 py-8">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; 2023 AutoLoc. Tous droits réservés.</p>
            </div>
        </footer>
    <script>
        gsap.registerPlugin(ScrollTrigger);

        window.addEventListener('load', () => {
            gsap.to('#articleHeader', {
                opacity: 1,
                y: 0,
                duration: 1,
                ease: 'power4.out'
            });

            gsap.to('#articleImage', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.2,
                ease: 'power4.out'
            });

            gsap.to('#articleContent', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.4,
                ease: 'power4.out'
            });

            gsap.to('#commentSection', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.6,
                ease: 'power4.out'
            });
        });
       
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            document.querySelector('.progress-bar').style.transform = `scaleX(${scrolled / 100})`;
        });
        const stars = document.querySelectorAll('.star');
let rating = 0;

stars.forEach((star, index) => {
    star.style.color = '#d1d5db'; // Default color for stars

    star.addEventListener('mouseover', () => {
        highlightStars(index);
    });

    star.addEventListener('mouseout', () => {
        highlightStars(rating - 1); // Highlight up to the current rating
    });

    // Set rating on click
    star.addEventListener('click', () => {
        rating = index + 1;
        highlightStars(index);
        document.getElementById('rating').value = rating; // Update hidden input value
    });
});

document.getElementById('commentForm').addEventListener('submit', function(e) {
    const ratingInput = document.getElementById('rating');
    const contentInput = document.querySelector('textarea[name="content"]');
    
    if (!ratingInput.value) {
        e.preventDefault();
        alert('Veuillez sélectionner une note');
        return false;
    }
    
    if (!contentInput.value.trim()) {
        e.preventDefault();
        alert('Veuillez ajouter un commentaire');
        return false;
    }
});

function highlightStars(index) {
    stars.forEach((star, i) => {
        star.style.color = i <= index ? '#fbbf24' : '#d1d5db'; // Highlight color for stars
        star.style.transform = i <= index ? 'scale(1.2)' : 'scale(1)'; // Scale effect for highlighted stars
    });
}
document.addEventListener('DOMContentLoaded', () => {
    const comments = document.querySelectorAll('#commentsContainer .comment');
    const loadMore = document.getElementById('loadMore');
    const loadLess = document.getElementById('loadLess');
    let visibleCount = 5;

    function updateComments() {
        comments.forEach((comment, index) => {
            comment.style.display = index < visibleCount ? 'block' : 'none';
        });

        loadMore.style.display = visibleCount < comments.length ? 'inline-block' : 'none';
        loadLess.style.display = visibleCount > 10 ? 'inline-block' : 'none';
    }

    loadMore.addEventListener('click', () => {
        visibleCount += 10;
        updateComments();
    });

    loadLess.addEventListener('click', () => {
        visibleCount = Math.max(10, visibleCount - 10);
        updateComments();
    });

    // Initial call to display the correct set of comments
    updateComments();
});
const favoris = document.querySelector('.favoris');
    const heart = document.querySelector('.heart');

    heart.addEventListener('mouseover', () => {
        favoris.classList.remove('hidden');
    });

    heart.addEventListener('mouseout', () => {
        favoris.classList.add('hidden');
    });
</script>
</body>
</html>