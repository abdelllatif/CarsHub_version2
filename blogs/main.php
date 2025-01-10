<?php
require_once '../classes/blogsclass.php';
require_once '../classes/commentclass.php';  

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/singin.php');
    exit();
}

$blogs = new blogs();
$userId = $_SESSION['user_id'];
$comments = $blogs->getUserComments($userId);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commentaires</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Mes Commentaires</h2>
            
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-box mb-6 p-4 bg-gray-50 rounded-lg" id="comment-<?php echo $comment['id']; ?>">
    <!-- Regular View -->
    <div class="comment-view">
             <!-- Display Stars (Read-only) -->
                <span class="text-yellow-500">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <?php if ($i < $comment['rating']): ?>
                            ★
                        <?php else: ?>
                            ☆
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>   
                 <div class="flex justify-between items-start">
            <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($comment['article_title']); ?></h3>
    
                <div class="flex items-center space-x-4">
               
                <!-- Action Buttons -->
                <div class="space-x-2">
                    <button onclick="showEditForm(<?php echo $comment['id']; ?>, <?php echo $comment['rating']; ?>)" 
                            class="text-blue-500 hover:text-blue-700">
                        Modifier
                    </button>
                    <form action="deletmain.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
    <button type="submit" class="text-red-500 hover:text-red-700">
        Supprimer
    </button>
</form>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <p class="text-gray-700"><?php echo htmlspecialchars($comment['content']); ?></p>
            <p class="text-sm text-gray-500 mt-2"><?php echo $comment['created_at']; ?></p>
        </div>
    </div>
    
    <!-- Edit Form (Hidden by default) -->
    <div class="comment-edit hidden" id="edit-<?php echo $comment['id']; ?>">
    <form action="editmain.php" method="POST">
        <div class="mb-4">
            <div class="stars flex space-x-1">
                <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="1">★</span>
                <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="2">★</span>
                <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="3">★</span>
                <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="4">★</span>
                <span class="star text-3xl cursor-pointer transition-colors duration-300" data-value="5">★</span>
            </div>
            <input type="hidden" name="commentID" id="commentID" value='<?php echo $comment['id']; ?>'>
            <input type="hidden" name="rating" id="rating">
            </div>
        <textarea  name="content" class="w-full p-2 border rounded-lg mb-2" rows="3"><?php echo htmlspecialchars($comment['content']); ?></textarea>
        <div class="flex space-x-2">
            <button type="submit" 
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Enregistrer
            </button>
            <button onclick="cancelEdit(<?php echo $comment['id']; ?>)" 
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Annuler
            </button>
        </div>
        </form>
    </div>
</div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600 text-center">Vous n'avez pas encore fait de commentaires.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
   function initializeStars(commentId) {
    const stars = document.querySelectorAll(`#edit-${commentId} .star`);
    const ratingInput = document.querySelector(`#edit-${commentId} input[name="rating"]`);
    
    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const value = this.getAttribute('data-value');
            highlightStars(stars, value);
        });

        star.addEventListener('mouseout', function() {
            const currentRating = ratingInput.value || 0;
            highlightStars(stars, currentRating);
        });

        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;
            highlightStars(stars, value);
        });
    });
}

function highlightStars(stars, value) {
    stars.forEach(star => {
        const starValue = star.getAttribute('data-value');
        star.style.color = starValue <= value ? '#FFD700' : '#D1D5DB';
    });
}

function showEditForm(commentId) {
    document.querySelector(`#comment-${commentId} .comment-view`).classList.add('hidden');
    document.querySelector(`#edit-${commentId}`).classList.remove('hidden');
    initializeStars(commentId);
    
    star.addEventListener('click', () => {
        rating = index + 1;
        highlightStars(index);
        document.getElementById('rating').value = rating;
    });
}

function saveComment(commentId) {
  
}
    function cancelEdit(commentId) {
            document.querySelector(`#comment-${commentId} .comment-view`).classList.remove('hidden');
            document.querySelector(`#edit-${commentId}`).classList.add('hidden');
        }

    </script>
</body>
</html>