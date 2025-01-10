<?php
require_once 'upload.php';
require_once '../classes/blogsclass.php';

// Check if the user is logged in

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/singin.phpp');
    exit();
}

$blogs = new Blogs();
$userId = $_SESSION['user_id'];
$comments = $blogs->getUserComments($userId);

$data = new blogs();
$userId = $_SESSION['user_id'];  // Example user ID
$imagePath = $data->getProfileImage($userId);
$coverImagePath = $data->getCoverImage($userId); // Assuming you have this method to get the cover image

if ($imagePath) {
} else {
}

if ($coverImagePath) {
} else {
}

require_once 'getthems.php';
require_once '../classes/clientclasse.php';

$USERS = new ClientID();

$id = $_SESSION['user_id'];
$user = $USERS->getUserById($id);
if (!empty($imagePath)) {
    $image_paths = htmlspecialchars($imagePath);
} else {
    $image_paths = 'https://i.pravatar.cc/300'; // default profile image
}

if (!empty($coverImagePath)) {
    $cover_image_paths = htmlspecialchars($coverImagePath);
} else {
    $cover_image_paths = 'https://via.placeholder.com/800x200'; // default cover image
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* Cover image background */
        .cover-background {
            background-image: url('<?php echo $cover_image_paths; ?>');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Cover background -->
            <div class="cover-background h-56  relative">
                <!-- Camera icon for changing cover image -->
                <div 
                    class="absolute bottom-2 right-2 h-10 w-10 bg-white rounded-full flex items-center justify-center border border-gray-300 cursor-pointer" 
                    onclick="document.getElementById('coverFileInput').click()"
                >
                    <i class="bx bx-camera text-blue-500 text-lg"></i>
                </div>
                <form id="coverUploadForm" action="uploadcover.php" method="POST" enctype="multipart/form-data">
                    <input 
                        id="coverFileInput" 
                        type="file" 
                        name="cover_image" 
                        class="hidden" 
                        accept="image/*" 
                        onchange="previewAndSubmitCover(this)"
                    >
                </form>
            </div>

            <div class="p-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Profile image -->
                        <div class="relative">
                            <img 
                                id="profileImage"
                                class="h-24 w-24 rounded-full object-cover border-2 border-blue-500" 
                                src="<?php echo $image_paths ?>" 
                                alt="User Avatar"
                            >
                            <div 
                                class="absolute bottom-0 right-0 h-8 w-8 bg-white rounded-full flex items-center justify-center border border-gray-300 cursor-pointer" 
                                onclick="document.getElementById('fileInput').click()"
                            >
                                <i class="bx bx-camera text-blue-500 text-lg"></i>
                            </div>
                            <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
                                <input 
                                    id="fileInput" 
                                    type="file" 
                                    name="profile_picture" 
                                    class="hidden" 
                                    accept="image/*" 
                                    onchange="previewAndSubmitProfile(this)"
                                >
                            </form>
                        </div>
                        <div class="ml-6">
                            <h2 class="text-3xl font-semibold text-gray-800"><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h2>
                            <p class="text-gray-600"><?php echo htmlspecialchars($user['role']); ?></p>
                        </div>
                    </div>
                    <div class="relative">
                        <i class="bx bx-menu text-2xl cursor-pointer" onclick="toggleMenu()"></i>
                        <div id="menuOptions" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden">
                            <div class="py-2">
                                <a href="main.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50" onclick="showCommentsModal()">Voir mes commentaires</a>
                                <a href="favorites.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
    Mes Favoris
</a>
                                <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Modifier le profil</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-xl font-semibold text-gray-700">Informations de contact</h3>
                    <p class="mt-2 text-gray-600">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="mt-2 text-gray-600">Téléphone: <?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Comments -->
    <div id="commentsModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-3/4 max-h-3/4 overflow-auto">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Mes Commentaires</h2>
                <button onclick="closeCommentsModal()" class="text-gray-600 hover:text-gray-900">&times;</button>
            </div>
            <div class="p-4" id="commentsList">
                <!-- Comments will be dynamically loaded here -->
            </div>
            <div class="p-4 flex justify-between">
                <button id="loadMoreComments" class="text-blue-600 hover:underline">Voir Plus</button>
                <button id="loadLessComments" class="text-blue-600 hover:underline hidden">Voir Moins</button>
            </div>
        </div>
    </div>

    <script>
        let commentsData = [];
        let visibleCommentsCount = 10; // Initial visible comments
        const commentsListElement = document.getElementById('commentsList');

        function toggleMenu() {
            const menuOptions = document.getElementById('menuOptions');
            menuOptions.classList.toggle('hidden');
        }

        function showCommentsModal() {
            fetch('get_user_comments.php') 
                .then(response => response.json())
                .then(data => {
                    commentsData = data;
                    visibleCommentsCount = 10;
                    displayComments();
                    document.getElementById('commentsModal').classList.remove('hidden');
                });
        }

        function closeCommentsModal() {
            document.getElementById('commentsModal').remove('hidden');
        }

        function displayComments() {
            commentsListElement.innerHTML = '';
            commentsData.slice(0, visibleCommentsCount).forEach(comment => {
                commentsListElement.innerHTML += `
                    <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold">${comment.article_title}</span>
                            <span class="text-sm text-gray-500">${comment.created_at}</span>
                        </div>
                        <p class="mt-2 text-gray-700">${comment.content}</p>
                    </div>
                `;
            });

            document.getElementById('loadMoreComments').style.display = visibleCommentsCount < commentsData.length ? 'inline-block' : 'none';
            document.getElementById('loadLessComments').style.display = visibleCommentsCount > 10 ? 'inline-block' : 'none';
        }

        document.getElementById('loadMoreComments').addEventListener('click', () => {
            visibleCommentsCount += 10;
            displayComments();
        });

        document.getElementById('loadLessComments').addEventListener('click', () => {
            visibleCommentsCount = Math.max(10, visibleCommentsCount - 10);
            displayComments();
        });

        function previewAndSubmitProfile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profileImage').src = e.target.result; // Preview the profile image
                };
                reader.readAsDataURL(input.files[0]);

                // Automatically submit the form after selecting the image
                document.getElementById('uploadForm').submit();
            }
        }

        function previewAndSubmitCover(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('.cover-background').style.backgroundImage = `url(${e.target.result})`; // Preview the cover image
                };
                reader.readAsDataURL(input.files[0]);

                // Automatically submit the form after selecting the image
                document.getElementById('coverUploadForm').submit();
            }
        }
        
    </script>
</body>
</html>