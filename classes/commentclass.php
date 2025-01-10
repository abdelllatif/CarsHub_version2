<?php
require_once 'connectiondatabase.php';

class Comment extends data {
    public $pdo;
    private $content;
    private $rating;
    private $userId;
    private $articleId;
    private $createdAt;
    
    public function __construct() {
        $this->pdo = $this->connextion();
        $this->createdAt = new DateTime();
    }
    public function insertComment($article_id, $user_id, $content, $rating) {
        try {
            $articleCheck = $this->pdo->prepare("SELECT id FROM Articles WHERE id = ?");
            $articleCheck->execute([$article_id]);
            if (!$articleCheck->fetch()) {
                echo "<br>";
                throw new Exception("Article does not exist");
            }
    
            $userCheck = $this->pdo->prepare("SELECT id FROM clients WHERE id = ?");
            $userCheck->execute([$user_id]);
            if (!$userCheck->fetch()) {
                echo "<br>";
                throw new Exception("User does not exist");
            }
    
           if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            echo "<br>";          
            throw new Exception("Rating must be between 1 and 5");
            echo "<br>";          
            echo count($rating);          
            }
    
            // Validate content
            if (empty(trim($content))) {
                throw new Exception("Comment cannot be empty");
                echo "<br>";
            }
    
            $this->content = htmlspecialchars(trim($content));
            $this->rating = (int)$rating;
            $this->userId = (int)$user_id;
            $this->articleId = (int)$article_id;
    
            $query = "INSERT INTO comments (article_id, user_id, content, rating) 
                     VALUES (:article_id, :user_id, :content, :rating)";
            
            $stmt = $this->pdo->prepare($query);
            
            return $stmt->execute([
                ':article_id' => $this->articleId,
                ':user_id' => $this->userId,
                ':content' => $this->content,
                ':rating' => $this->rating
            ]);
    
        } catch (Exception $e) {
            error_log("Error adding comment: " . $e->getMessage());
            throw $e; 
        }
    }
    
    public function getCommentsByArticle($article_id) {
        try {
            $query = "SELECT c.*, cl.name as user_name 
            FROM comments c 
            JOIN clients cl ON c.user_id = cl.id 
            WHERE c.article_id = :article_id 
            ORDER BY c.created_at DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':article_id' => $article_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error fetching comments: " . $e->getMessage());
            return false;
        }
    }

    public function getRating() {
        return $this->rating;
    }
    
    public function getContent() {
        return $this->content;
    }
    

    public function formatComment() {
        return [
            'content' => $this->content,
            'rating' => $this->rating,
            'userId' => $this->userId,
            'articleId' => $this->articleId,
            'date' => $this->createdAt->format('d/m/Y H:i'),
            'stars' => str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating)
        ];
    }
    
public function updateComment($commentId, $userId, $content, $rating) {
    $sql = "UPDATE comments 
            SET content = :content, 
                rating = :rating,
                created_at = CURRENT_TIMESTAMP 
            WHERE id = :commentId 
            AND user_id = :userId";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':content' => $content,
        ':rating' => $rating,
        ':commentId' => $commentId,
        ':userId' => $userId
    ]);
    return $stmt->rowCount() > 0;
}

public function deleteComment($commentId, $userId) {
    $sql = "DELETE FROM comments 
            WHERE id = :comment_id 
            AND user_id = :user_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':comment_id' => $commentId,
        ':user_id' => $userId
    ]);
    return $stmt->rowCount() > 0;
}
}