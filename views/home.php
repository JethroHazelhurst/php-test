<?php ob_start() ?>
<h1>Todo List</h1>
<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?action=create">
    <input type="text" name="description" placeholder="New task..." required>
    <button type="submit">Add Task</button>
</form>

<ul class="task-list">
    <?php foreach ($tasks as $task): ?>
        <li class="<?= $task->completed ? 'completed' : '' ?>">
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>/?action=toggle&id=<?= $task->id ?>">
                <?= htmlspecialchars($task->description) ?>
                <button type="submit" class="toggle-btn">
                    <?= $task->completed ? '✅' : '⬜' ?>
                </button>
            </form>
        </li>
    <?php endforeach ?>
</ul>
<?php $content = ob_get_clean(); ?>
<?php include 'layout.php'; ?>