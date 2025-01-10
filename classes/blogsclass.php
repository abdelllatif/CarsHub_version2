<?php 
require_once 'connectiondatabase.php';  
  

class blogs extends data {
    public $pdo;
    public $name;
    public $description;

    public function __construct() {
      $this->pdo=$this->connextion();
    }

    //*************************************************************************** */
        //*************************************************************************** */

 public function createtheme($name,$description){
try{
$query="INSERT INTO themes(name,description)
 VALUES(:name,:description)";
 $stm=$this->pdo->prepare($query);
 $stm->bindParam(":name",$name);
 $stm->bindParam(":description",$description);
 if($stm->execute()){
    echo "success!";
    exit();
 }
}
catch(PDOException $e){
    echo "Database error: " .$e->getmessage();
}
}




public function getUserComments($userId) {
    $sql = "SELECT c.*, a.title as article_title 
            FROM comments c 
            JOIN articles a ON c.article_id = a.id 
            WHERE c.user_id = :user_id 
            ORDER BY c.created_at DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function toggleFavorite($userId, $articleId) {
    $stmt = $this->pdo->prepare("SELECT user_id FROM Favorites WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$userId, $articleId]);
    
    if ($stmt->fetch()) {
        // Remove favorite if it exists
        $stmt = $this->pdo->prepare("DELETE FROM Favorites WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$userId, $articleId]);
        return false;
    } else {
        // Add favorite if it doesn't exist
        $stmt = $this->pdo->prepare("INSERT INTO Favorites (user_id, article_id) VALUES (?, ?)");
        $stmt->execute([$userId, $articleId]);
        return true;
    }
}

public function isArticleFavorited($userId, $articleId) {
    $stmt = $this->pdo->prepare("SELECT user_id FROM Favorites WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$userId, $articleId]);
    return (bool)$stmt->fetch();
}

public function getUserFavorites($userId) {
    $stmt = $this->pdo->prepare("
        SELECT a.*, c.firstName as author_name, t.name as theme_name,
               DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date,
               GROUP_CONCAT(tg.name) as tags
        FROM Articles a
        LEFT JOIN clients c ON a.user_id = c.id
        LEFT JOIN Themes t ON a.theme_id = t.id
        LEFT JOIN ArticleTags at ON at.article_id = a.id
        LEFT JOIN Tags tg ON at.tag_id = tg.id
        INNER JOIN Favorites f ON f.article_id = a.id
        WHERE f.user_id = ?
        GROUP BY a.id
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    //*************************************************************************** */
    //*************************************************************************** */

public function insertArticleTag($article_id, $tag_id) {
    $query = "INSERT INTO ArticleTags(article_id, tag_id) VALUES(:article_id, :tag_id)";
    try {
        $stm = $this->pdo->prepare($query);
        $stm->bindParam(":article_id", $article_id);
        $stm->bindParam(":tag_id", $tag_id);
        return $stm->execute();
    } catch (PDOException $e) {
        echo "Error inserting article-tag relationship: " . $e->getMessage();
        return false;
    }
}
    //*************************************************************************** */
    //*************************************************************************** */

public function createtags($tag) {
    try {
        $query = "SELECT id FROM tags WHERE name = :name";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $tag);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['id'];
        } else {
            $query = "INSERT INTO tags (name) VALUES (:tag)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':tag', $tag);
            $stmt->execute();
            return $this->pdo->lastInsertId(); 
        }
    } catch (PDOException $e) {
        echo "Error adding tag: " . $e->getMessage();
        return false;
    }
}
    //*************************************************************************** */
    //*************************************************************************** */

public function gettheme(){
    $query="SELECT * FROM themes";
    try{
    $stm=$this->pdo->prepare($query);
    if($stm->execute()){
        $themes=$stm->fetchAll(PDO::FETCH_ASSOC);
        return $themes;
    }
}
    catch(PDOException $e){
        echo "cant take data".$e->getmessage();
    }
}
    //*************************************************************************** */
    //*************************************************************************** */

public function uploadImage($image){
$targetdiroctory ="../Admin/uploads/";
$targetfile =$targetdiroctory .basename($image['name']);
$imagefiletype=strtolower(pathinfo($targetfile,PATHINFO_EXTENSION));
if(getimagesize($image["tmp_name"])===false){
echo "this is not an image hhh";
return false;
}
if($image['size']>8000000){
    echo "this is to large image ";
    return false;
}
$imagetype=array('jpg','png','jpeg');
if(!in_array($imagefiletype,$imagetype)){
    echo "no image only 'jpg','png','jpeg'";
    return false;
}
if(move_uploaded_file($image["tmp_name"],$targetfile)){
    echo "the file".basename($image['name'])."has been uploded";
    return $targetfile; 
}
else{
    echo "sory we can't upload your file try with another one";
    return false;
}
}

public function updateArticleStatus($articleId, $status) {
    try {
        $query = "UPDATE articles SET approved = :status WHERE id = :article_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error updating article status: " . $e->getMessage());
        return false;
    }
}
public function uploadImageAndSaveTopdo($image, $userId)
{
    $targetDirectory = "../Admin/uploads/";
    $targetFile = $targetDirectory . basename($image['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validate if it's an image
    if (getimagesize($image["tmp_name"]) === false) {
        echo "This is not an image!";
        return false;
    }

    // Validate file size
    if ($image['size'] > 8000000) {
        echo "This image is too large.";
        return false;
    }

    // Allowed image formats
    $allowedTypes = ['jpg', 'png', 'jpeg'];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Only 'jpg', 'png', 'jpeg' formats are allowed.";
        return false;
    }

    // Upload file
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        echo "The file " . basename($image['name']) . " has been uploaded.";

        try {
            // Update the client's profile image in the database
            $stmt = $this->pdo->prepare("UPDATE clients SET profile_image = :profile_image WHERE id = :userId");
            $stmt->bindParam(':profile_image', $targetFile, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            echo "Image path saved to the database successfully!";
            return $targetFile;

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    } else {
        echo "Sorry, we couldn't upload your file. Please try again.";
        return false;
    }
}
public function getProfileImage($userId) {
    try {
        // Prepare SQL query to select the profile image based on the user ID
        $stmt = $this->pdo->prepare("SELECT profile_image FROM clients WHERE id = :userId");
        $stmt->execute([':userId' => $userId]);
        
        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['profile_image'];  // Return the profile image path
        } else {
            return null;  // Return null if no profile image is found for the user
        }
    } catch (PDOException $e) {
        // Handle any database errors
        error_log("Error fetching profile image: " . $e->getMessage());
        return null;  // Return null in case of error
    }
}
 // Method to upload and set the cover image
    public function uploadCoverImage($image, $userId) {
        $targetDirectory = "../Admin/uploads/";
        $targetFile = $targetDirectory . basename($image['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        // Validate if it's an image
        if (getimagesize($image["tmp_name"]) === false) {
            echo "This is not an image!";
            return false;
        }
    
        // Validate file size
        if ($image['size'] > 8000000) {
            echo "This image is too large.";
            return false;
        }
    
        // Allowed image formats
        $allowedTypes = ['jpg', 'png', 'jpeg'];
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Only 'jpg', 'png', 'jpeg' formats are allowed.";
            return false;
        }
    
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            echo "The file " . basename($image['name']) . " has been uploaded.";
    
            try {
                // Update the client's COVER image in the database
                $stmt = $this->pdo->prepare("UPDATE clients SET cover_image = :cover_image WHERE id = :userId");
                $stmt->bindParam(':cover_image', $targetFile, PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();
    
                echo "Cover image path saved to the database successfully!";
                return $targetFile;
    
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
                return false;
            }
        } else {
            echo "Sorry, we couldn't upload your file. Please try again.";
            return false;
        }
    }
    

// Method to get the cover image path
public function getCoverImage($userId) {
    $stmt = $this->pdo->prepare('SELECT cover_image FROM clients WHERE id = :id');
    $stmt->execute(['id' => $userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['cover_image'] : null;
}


    //*************************************************************************** */
    //*************************************************************************** */

public function insertarticle($user_id, $theme_id, $title, $content, $media_path) {
    $imagepath = $this->uploadImage($media_path);

    $query = "INSERT INTO articles(user_id, theme_id, title, content, media_path) 
              VALUES(:user_id, :theme_id, :title, :content, :media_path)";
    try {
        $stm = $this->pdo->prepare($query);
        $stm->bindParam(":user_id", $user_id);
        $stm->bindParam(":theme_id", $theme_id);
        $stm->bindParam(":title", $title);
        $stm->bindParam(":content", $content);
        $stm->bindParam(":media_path", $imagepath);

        if ($stm->execute()) {
            echo "Article inserted successfully!";
            return $this->pdo->lastInsertId(); 
        }
    } catch (PDOException $e) {
        echo "Error inserting article: " . $e->getMessage();
        return false;
    }
}
//************************************************************************************************************************ */

public function createcomment($tag) {
    try {
       
            $query = "INSERT INTO comments (name) VALUES (:tag)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':tag', $tag);
            $stmt->execute();
            return $this->pdo->lastInsertId(); 
        }
    catch (PDOException $e) {
        echo "Error adding tag: " . $e->getMessage();
        return false;
    }
}








//***************************************************************************************************************** */

public function addComment($article_id, $user_id, $content, $rating) {
    try {
        $sql = "INSERT INTO comments (article_id, user_id, content, rating, created_at) 
                VALUES (:article_id, :user_id, :content, :rating, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error adding comment: " . $e->getMessage());
        throw new Exception("Failed to add comment");
    }
}

public function getCommentsByArticleId($article_id) {
    $sql = "SELECT c.*, u.firstName, u.lastName 
            FROM Comments c
            JOIN clients u ON c.user_id = u.id
            WHERE c.article_id = :article_id
            ORDER BY c.created_at DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getArticleById($id) {
    $sql = "SELECT a.*, t.name as theme_name, 
                  GROUP_CONCAT(tags.name) as tags,
                  u.firstName as author_name,
                  DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
                  FROM Articles a
                  LEFT JOIN Themes t ON a.theme_id = t.id
                  LEFT JOIN ArticleTags at ON a.id = at.article_id
                  LEFT JOIN Tags tags ON at.tag_id = tags.id
                  LEFT JOIN clients u ON a.user_id = u.id
                  WHERE a.id = :id
                  GROUP BY a.id
                  ORDER BY a.created_at DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

 

   


public function getTags() {
    $stmt = $this->pdo->prepare("SELECT * FROM Tags");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
private function truncateWords($text, $limit = 40) {
    $words = str_word_count($text, 2);
    if (count($words) > $limit) {
        $words = array_slice($words, 0, $limit);
        $lastKey = array_keys($words);
        $lastKey = end($lastKey);
        return substr($text, 0, $lastKey) . '...';
    }
    return $text;
}
public function getAllArticlesapproved() {
    try {
        $query = "SELECT a.*, t.name as theme_name, 
                  GROUP_CONCAT(tags.name) as tags,
                  u.firstname as author_name,
                  DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
                  FROM articles a
                  LEFT JOIN themes t ON a.theme_id = t.id
                  LEFT JOIN ArticleTags at ON a.id = at.article_id
                  LEFT JOIN tags ON at.tag_id = tags.id
                  LEFT JOIN clients u ON a.user_id = u.id
                  WHERE a.approved = 1
                  GROUP BY a.id
                  ORDER BY a.created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    
        
        return $articles;
    } catch (PDOException $e) {
        error_log("Error fetching articles: " . $e->getMessage());
        return [];
    }
}
public function getAllArticles() {
    try {
        $query = "SELECT a.*, t.name as theme_name, 
                  GROUP_CONCAT(tags.name) as tags,
                  u.firstname as author_name,
                  DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
                  FROM articles a
                  LEFT JOIN themes t ON a.theme_id = t.id
                  LEFT JOIN ArticleTags at ON a.id = at.article_id
                  LEFT JOIN tags ON at.tag_id = tags.id
                  LEFT JOIN clients u ON a.user_id = u.id
                  GROUP BY a.id
                  ORDER BY a.created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
      
        
        return $articles;
    } catch (PDOException $e) {
        error_log("Error fetching articles: " . $e->getMessage());
        return [];
    }
}

public function deleteTheme($themeId) {
    try {
        $query = "DELETE FROM themes WHERE id = :theme_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':theme_id', $themeId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting theme: " . $e->getMessage());
        return false;
    }
}

public function updateTheme($themeId, $name, $description) {
    try {
        $query = "UPDATE themes SET name = :name, description = :description WHERE id = :theme_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':theme_id', $themeId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error updating theme: " . $e->getMessage());
        return false;
    }
}
// Get total articles count
public function getTotalArticles() {
    $sql = "SELECT COUNT(*) as total FROM articles";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}
// public function getArticleById($id) {
//     $sql = "SELECT a.*, t.name as theme_name, 
//                   GROUP_CONCAT(tags.name) as tags,
//                   u.firstName as author_name,
//                   DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
//                   FROM Articles a
//                   LEFT JOIN Themes t ON a.theme_id = t.id
//                   LEFT JOIN ArticleTags at ON a.id = at.article_id
//                   LEFT JOIN Tags tags ON at.tag_id = tags.id
//                   LEFT JOIN clients u ON a.user_id = u.id
//                   WHERE a.id = :id
//                   GROUP BY a.id
//                   ORDER BY a.created_at DESC";
//     $stmt = $this->pdo->prepare($sql);
//     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
//     $stmt->execute();
//     return $stmt->fetch(PDO::FETCH_ASSOC);
// }

public function getAllArticlesWithPagination($page = 1, $itemsPerPage = 10) {
    try {
        // Calculate the offset
        $offset = ($page - 1) * $itemsPerPage;
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) FROM articles WHERE approved = 1";
        $totalItems = $this->pdo->query($countQuery)->fetchColumn();
        
        // Main query with pagination
        $query = "SELECT a.*, t.name as theme_name, 
                  GROUP_CONCAT(tags.name) as tags,
                  u.firstname as author_name,
                  DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
                  FROM articles a
                  LEFT JOIN themes t ON a.theme_id = t.id
                  LEFT JOIN ArticleTags at ON a.id = at.article_id
                  LEFT JOIN tags ON at.tag_id = tags.id
                  LEFT JOIN clients u ON a.user_id = u.id
                  WHERE a.approved = 1
                  GROUP BY a.id
                  ORDER BY a.created_at DESC
                  LIMIT :offset, :limit";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate total pages
        $totalPages = ceil($totalItems / $itemsPerPage);
        
        return [
            'articles' => $articles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'itemsPerPage' => $itemsPerPage
        ];
    } catch (PDOException $e) {
        error_log("Error fetching paginated articles: " . $e->getMessage());
        return [
            'articles' => [],
            'currentPage' => 1,
            'totalPages' => 0,
            'totalItems' => 0,
            'itemsPerPage' => $itemsPerPage
        ];
    }
}
// public function getFilteredArticlesWithPagination($theme_id = null, $tag_id = null, $search = null, $page = 1, $itemsPerPage = 10) {
//     try {
//         $params = [];
//         $whereConditions = ['a.approved = 1'];
        
//         // Build where conditions
//         if ($theme_id) {
//             $whereConditions[] = "a.theme_id = :theme_id";
//             $params[':theme_id'] = $theme_id;
//         }
        
//         if ($tag_id) {
//             $whereConditions[] = "at.tag_id = :tag_id";
//             $params[':tag_id'] = $tag_id;
//         }
        
//         if ($search) {
//             $whereConditions[] = "(a.title LIKE :search OR a.content LIKE :search)";
//             $params[':search'] = '%' . $search . '%';
//         }
        
//         $whereClause = implode(' AND ', $whereConditions);
        
//         // Count total filtered items
//         $countQuery = "SELECT COUNT(DISTINCT a.id) 
//                       FROM articles a
//                       LEFT JOIN ArticleTags at ON a.id = at.article_id
//                       WHERE " . $whereClause;
        
//         $countStmt = $this->pdo->prepare($countQuery);
//         foreach ($params as $key => $value) {
//             $countStmt->bindValue($key, $value);
//         }
//         $countStmt->execute();
//         $totalItems = $countStmt->fetchColumn();
        
//         // Calculate pagination
//         $offset = ($page - 1) * $itemsPerPage;
        
//         // Main query with filters and pagination
//         $query = "SELECT a.*, t.name as theme_name, 
//                   GROUP_CONCAT(tags.name) as tags,
//                   u.firstname as author_name,
//                   DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
//                   FROM articles a
//                   LEFT JOIN themes t ON a.theme_id = t.id
//                   LEFT JOIN ArticleTags at ON a.id = at.article_id
//                   LEFT JOIN tags ON at.tag_id = tags.id
//                   LEFT JOIN clients u ON a.user_id = u.id
//                   WHERE " . $whereClause . "
//                   GROUP BY a.id
//                   ORDER BY a.created_at DESC
//                   LIMIT :offset, :limit";
        
//         $stmt = $this->pdo->prepare($query);
//         foreach ($params as $key => $value) {
//             $stmt->bindValue($key, $value);
//         }
//         $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//         $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
//         $stmt->execute();
        
//         $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         $totalPages = ceil($totalItems / $itemsPerPage);
        
//         return [
//             'articles' => $articles,
//             'currentPage' => $page,
//             'totalPages' => $totalPages,
//             'totalItems' => $totalItems,
//             'itemsPerPage' => $itemsPerPage
//         ];
//     } catch (PDOException $e) {
//         error_log("Error fetching filtered paginated articles: " . $e->getMessage());
//         return [
//             'articles' => [],
//             'currentPage' => 1,
//             'totalPages' => 0,
//             'totalItems' => 0,
//             'itemsPerPage' => $itemsPerPage
//         ];
//     }
// }
public function getFilteredArticlesWithPagination($theme_id = null, $tag_id = null, $search = null, $page = 1, $itemsPerPage = 10) {
    try {
        $params = [];
        $whereConditions = ['a.approved = 1'];
        
        if ($theme_id) {
            $whereConditions[] = "a.theme_id = :theme_id";
            $params[':theme_id'] = $theme_id;
        }
        
        if ($tag_id) {
            $whereConditions[] = "at.tag_id = :tag_id";
            $params[':tag_id'] = $tag_id;
        }
        
        if ($search) {
            $whereConditions[] = "(a.title LIKE :search OR a.content LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $countQuery = "SELECT COUNT(DISTINCT a.id) 
                      FROM articles a
                      LEFT JOIN ArticleTags at ON a.id = at.article_id
                      WHERE " . $whereClause;
        
        $countStmt = $this->pdo->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $totalItems = $countStmt->fetchColumn();
        
        $offset = ($page - 1) * $itemsPerPage;
        
        $query = "SELECT a.*, t.name as theme_name, 
                  GROUP_CONCAT(tags.name) as tags,
                  u.firstname as author_name,
                  DATE_FORMAT(a.created_at, '%d %b %Y') as formatted_date
                  FROM articles a
                  LEFT JOIN themes t ON a.theme_id = t.id
                  LEFT JOIN ArticleTags at ON a.id = at.article_id
                  LEFT JOIN tags ON at.tag_id = tags.id
                  LEFT JOIN clients u ON a.user_id = u.id
                  WHERE " . $whereClause . "
                  GROUP BY a.id
                  ORDER BY a.created_at DESC
                  LIMIT :offset, :limit";
        
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalPages = ceil($totalItems / $itemsPerPage);
        
        return [
            'articles' => $articles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'itemsPerPage' => $itemsPerPage
        ];
    } catch (PDOException $e) {
        error_log("Error fetching filtered paginated articles: " . $e->getMessage());
        return [
            'articles' => [],
            'currentPage' => 1,
            'totalPages' => 0,
            'totalItems' => 0,
            'itemsPerPage' => $itemsPerPage
        ];
    }
}

}
?>
