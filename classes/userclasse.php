<?php
require_once 'connectiondatabase.php';  

class User extends Data {
    protected $lastName;
    protected $firstName;
    protected $email;
    protected $password;
    public $role;
    public $createdAt;
    public $phone;
    public $pdo;

    public function __construct() {
        $this->pdo = $this->connextion();  
        $this->role = "user"; 
        $this->createdAt = date("Y-m-d H:i:s");
    }
    
    public function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function register($lastName, $firstName, $email, $password, $phone) {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
       

        try {
            $query = "INSERT INTO clients (lastName, firstName, email, password, role, createdAt, phone)
                      VALUES (:lastName, :firstName, :email, :password, :role, :createdAt, :phone)";
            $stmt = $this->pdo->prepare($query);  
            $stmt->bindParam(":lastName", $this->lastName);
            $stmt->bindParam(":firstName", $this->firstName);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":createdAt", $this->createdAt);
            $stmt->bindParam(":phone", $this->phone);
            if ($stmt->execute()) {
                echo "Data sent successfully";
             

                //header('Location: ../connexion/signin.php');
                exit();
            } else {
                $errormessage = $stmt->errorInfo();
                echo "Error: " . $errormessage[2];
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
    //  login
    public function login($email2, $password2) {
        $query = "SELECT * FROM clients WHERE email = :email2";
        $stmt = $this->pdo->prepare($query);  
        $stmt->bindParam(":email2", $email2);
        $stmt->execute();
    
        if ($stmt->rowCount() == 0) {
            echo "This user not found";
            exit();
        }
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password2, $user['password'])) {
            
            
            if ($user['role'] === 'admin') {
                header("Location: ../Admin/dasheboredAdmin.php"); 
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
            } else {
                header("Location: ../index.php"); 
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
            }
            exit();
        } else {
            echo "Invalid password";
            echo "<br>";
            var_dump(password_verify($password2, $user['password']));
        }
    }

    //  logout
    public function logout() {
        session_start();
        session_unset(); // Clear all session variables
        session_destroy(); // Destroy the session
        header('Location: signin.php'); // Redirect to the sign-in page
        exit();
    }

    // Get user info
    public function getProfile($email) {
        $query = "SELECT * FROM clients WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            echo "No profile found";
            exit();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function archiveClient($clientId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE clients SET archived = TRUE WHERE id = ?");
            return $stmt->execute([$clientId]);
        } catch (PDOException $e) {
            error_log("Error archiving client: " . $e->getMessage());
            return false;
        }
    }

    public function unarchiveClient($clientId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE clients SET archived = FALSE WHERE id = ?");
            return $stmt->execute([$clientId]);
        } catch (PDOException $e) {
            error_log("Error unarchiving client: " . $e->getMessage());
            return false;
        }
    }
}
?>
