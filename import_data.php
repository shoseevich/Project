<?php
// Параметры подключения к БД
$dbHost = 'localhost';
$dbName = 'database';
$dbUser = 'user';
$dbPass = 'password';

try {
    // Подключение к БД
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Скачивание данных записей
    $postsUrl = 'https://jsonplaceholder.typicode.com/posts';
    $postsJson = file_get_contents($postsUrl);
    $posts = json_decode($postsJson, true);

    // Загрузка записей в БД
    $postsCount = 0;
    foreach ($posts as $post) {
        $stmt = $pdo->prepare("INSERT INTO posts (id, userId, title, body) VALUES (:id, :userId, :title, :body)");
        $stmt->execute([
            ':id' => $post['id'],
            ':userId' => $post['userId'],
            ':title' => $post['title'],
            ':body' => $post['body']
        ]);
        $postsCount++;
    }

    // Скачивание данных комментариев
    $commentsUrl = 'https://jsonplaceholder.typicode.com/comments';
    $commentsJson = file_get_contents($commentsUrl);
    $comments = json_decode($commentsJson, true);

    // Загрузка комментариев в БД
    $commentsCount = 0;
    foreach ($comments as $comment) {
        $stmt = $pdo->prepare("INSERT INTO comments (id, postId, name, email, body) VALUES (:id, :postId, :name, :email, :body)");
        $stmt->execute([
            ':id' => $comment['id'],
            ':postId' => $comment['postId'],
            ':name' => $comment['name'],
            ':email' => $comment['email'],
            ':body' => $comment['body']
        ]);
        $commentsCount++;
    }

    // Вывод результата
    echo "Загружено $postsCount записей и $commentsCount комментариев\n";

} catch (PDOException $e) {
    echo "Ошибка подключения к БД: " . $e->getMessage();
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>