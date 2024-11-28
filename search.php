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

    // Получение запроса из формы
    $query = $_GET['query'] ?? '';

    // Проверка длины запроса
    if (strlen($query) < 3) {
        echo "Запрос должен содержать минимум 3 символа.";
        exit;
    }

    // Подготовка SQL-запроса
    $stmt = $pdo->prepare("
        SELECT p.title, c.body
        FROM posts p
        JOIN comments c ON p.id = c.postId
        WHERE c.body LIKE :query
    ");

    // Выполнение запроса
    $stmt->execute([':query' => "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Вывод результатов
    if (count($results) > 0) {
        echo "<h2>Результаты поиска:</h2>";
        echo "<ul>";
        foreach ($results as $result) {
            echo "<li>";
            echo "<strong>Заголовок записи:</strong> " . htmlspecialchars($result['title']) . "<br>";
            echo "<strong>Комментарий:</strong> " . htmlspecialchars($result['body']);
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "По вашему запросу ничего не найдено.";
    }

} catch (PDOException $e) {
    echo "Ошибка подключения к БД: " . $e->getMessage();
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>