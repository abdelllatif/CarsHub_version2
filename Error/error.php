<?php
// 404_error.php
header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
            text-align: center;
        }
        .container {
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 80px;
            color: #ff4d4d;
            margin-bottom: 20px;
        }
        p {
            font-size: 20px;
            margin-bottom: 20px;
        }
        a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #0056b3;
        }
        .home-link {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
        }
        .home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Oops! The page you're looking for can't be found.</p>
        <p><a href="/">Go back to Homepage</a></p>
        <p>Or visit the <a class="home-link" href="/">home page</a> for more information.</p>
    </div>
</body>
</html>
