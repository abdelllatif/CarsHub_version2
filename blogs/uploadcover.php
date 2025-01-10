<?php
session_start();
include '../classes/blogsclass.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cover_image'])) {
 // استبدل باسم ملف الفئة الخاص بك

    $userId = $_SESSION['user_id']; // قم بتحديد معرف المستخدم الخاص بك
    $yourClassInstance = new blogs(); // استبدل باسم الفئة الخاصة بك
    var_dump($_FILES['cover_image']);
    // تحميل الصورة وحفظها في قاعدة البيانات
    $result = $yourClassInstance->uploadCoverImage($_FILES['cover_image'], $userId);

    if ($result) {
        echo "Image uploaded and saved successfully!";
        
    } else {
        echo "There was an error.";
    }
}




?>