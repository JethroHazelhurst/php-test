<?php

use App\Models\Task;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Models/Task.php';

$taskModel = new Task();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'create':
            if (!empty($_POST['description'])) {
                $taskModel->create($_POST['description']);
            }
            break;

        case 'toggle':
            if (isset($_GET['id'])) {
                $taskModel->toggle($_GET['id']);
            }
            break;
    }

    $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    header("Location: " . rtrim($baseUrl, '/'));
    exit;
}

// Display tasks
$tasks = $taskModel->getAll();
require __DIR__ . '/../views/home.php';
