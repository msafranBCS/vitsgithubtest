
<?php
session_start();

require_once 'config/db.php';
require_once 'auth.php';

function clean($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

 //ADD TASK
if (isset($_POST['add_task'])) {

    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    if ($title !== '' && $due_date !== '') {

        $stmt = $pdo->prepare("
            INSERT INTO tasks (title, due_date, priority)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$title, $due_date, $priority]);

        $_SESSION['message'] = "Task added successfully!";
    }

    header("Location: todo_crud.php");
    exit;
}

// UPDATE TASK
if (isset($_POST['update_task'])) {

    $id = $_POST['task_id'];
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    if ($title !== '' && $due_date !== '') {

        $stmt = $pdo->prepare("
            UPDATE tasks
            SET title = ?, due_date = ?, priority = ?
            WHERE id = ?
        ");

        $stmt->execute([$title, $due_date, $priority, $id]);

        $_SESSION['message'] = "Task updated successfully!";
    }

    header("Location: todo_crud.php");
    exit;
}

// CHANGE STATUS
if (isset($_POST['change_status'])) {

    $stmt = $pdo->prepare("
        UPDATE tasks
        SET status = IF(status = 'Pending', 'Completed', 'Pending')
        WHERE id = ?
    ");

    $stmt->execute([$_POST['task_id']]);

    $_SESSION['message'] = "Task status updated!";

    header("Location: todo_crud.php");
    exit;
}

// DELETE TASK
if (isset($_POST['delete_task'])) {

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$_POST['task_id']]);

    $_SESSION['message'] = "Task deleted successfully!";

    header("Location: todo_crud.php");
    exit;
}

// FLASH MESSAGE
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// EDIT TASK
$editTask = null;

if (isset($_GET['edit'])) {

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$_GET['edit']]);

    $editTask = $stmt->fetch(PDO::FETCH_ASSOC);
}

// READ TASKS
$stmt = $pdo->query("
    SELECT * FROM tasks
    ORDER BY created_at DESC
");

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// TASK COUNTS
$totalTasks = count($tasks);
$completedTasks = 0;
$pendingTasks = 0;

foreach ($tasks as $task) {

    if ($task['status'] === 'Completed') {
        $completedTasks++;
    } else {
        $pendingTasks++;
    }
}

$progress = $totalTasks > 0
    ? round(($completedTasks / $totalTasks) * 100)
    : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Elite Daily | To-Do Tasks</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        body {
            font-family: 'Inter', sans-serif;
            background: #0b0e13;
        }

        .glass {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
        }

        .gold {
            color: #d6b36a;
        }

        .gold-bg {
            background: linear-gradient(135deg, #e4c47d, #a98238);
        }

        input, select {
            color-scheme: dark;
        }

    </style>

</head>

<body class="text-white min-h-screen">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="hidden md:flex w-64 flex-col p-6 border-r border-white/10">

        <h1 class="text-2xl font-extrabold gold tracking-wide">
            ELITE<span class="text-white">.</span>
        </h1>

        <p class="text-gray-500 text-xs mt-1">
            PERSONAL MANAGEMENT
        </p>

        <nav class="mt-12 space-y-3">

            <a href="index.php"
               class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                ◈ Dashboard
            </a>

            <a href="expenses.php"
               class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                ◷ Daily Expenses
            </a>

            <a href="todo_crud.php"
               class="block p-3 rounded-xl gold-bg text-black font-semibold">
                ✓ To-Do Tasks
            </a>

            <a href="transection.php"
               class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                ▤ Transactions
            </a>

        </nav>

        <div class="mt-auto glass p-4 rounded-2xl">

            <p class="text-xs text-gray-500">YOUR DAILY GOAL</p>

            <h2 class="text-xl font-bold gold mt-2">
                Stay Focused.
            </h2>

            <p class="text-xs text-gray-400 mt-2">
                Manage your money. Manage your time.
            </p>

        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="flex-1 p-5 md:p-10">

        <!-- HEADER -->
        <header class="flex justify-between items-center mb-10">

            <div>

                <p class="text-gray-500 text-sm">
                    PERSONAL MANAGEMENT
                </p>

                <h2 class="text-2xl md:text-3xl font-bold mt-2">
                    To-Do Tasks ✓
                </h2>

                <p class="text-gray-400 text-sm mt-1">
                    Organize your daily activities and stay productive.
                </p>

            </div>

            <div class="hidden sm:block text-right">

                <p class="gold text-sm font-semibold">
                    PREMIUM EDITION
                </p>

                <p class="text-gray-500 text-xs mt-1">
                    PERSONAL DASHBOARD
                </p>

            </div>

        </header>


        <!-- SUCCESS MESSAGE -->
        <?php if ($message): ?>

            <div class="glass p-4 rounded-xl mb-6 border border-yellow-500/30 gold">
                ✓ <?= clean($message) ?>
            </div>

        <?php endif; ?>


        <!-- SUMMARY CARDS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">Total Tasks</p>

                <h2 class="text-3xl font-bold mt-3">
                    <?= $totalTasks ?>
                </h2>

                <p class="text-gray-500 text-xs mt-3">
                    All created tasks
                </p>

            </div>


            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">Completed Tasks</p>

                <h2 class="text-3xl font-bold mt-3 gold">
                    <?= $completedTasks ?>
                </h2>

                <p class="text-green-400 text-xs mt-3">
                    Tasks completed
                </p>

            </div>


            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">Pending Tasks</p>

                <h2 class="text-3xl font-bold mt-3">
                    <?= $pendingTasks ?>
                </h2>

                <p class="text-yellow-400 text-xs mt-3">
                    Tasks remaining
                </p>

            </div>


            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">Task Progress</p>

                <h2 class="text-3xl font-bold mt-3 gold">
                    <?= $progress ?>%
                </h2>

                <p class="text-green-400 text-xs mt-3">
                    Overall completion
                </p>

            </div>

        </section>


        <!-- PROGRESS -->
        <section class="glass p-6 rounded-2xl mb-8">

            <div class="flex justify-between mb-3">

                <h3 class="font-bold text-lg">
                    Task Completion Progress
                </h3>

                <span class="gold font-bold">
                    <?= $progress ?>%
                </span>

            </div>

            <div class="w-full bg-gray-800 rounded-full h-2">

                <div class="gold-bg h-2 rounded-full transition-all"
                     style="width: <?= $progress ?>%">
                </div>

            </div>

        </section>


        <!-- ADD / EDIT TASK FORM -->
        <section id="taskForm" class="glass p-6 rounded-2xl mb-8">

            <div class="flex justify-between mb-6">

                <h3 class="text-lg font-bold">

                    <?= $editTask ? 'Edit Task' : 'Create New Task' ?>

                </h3>

                <span class="gold">
                    ✦ PRODUCTIVITY
                </span>

            </div>


            <form method="POST" action="todo_crud.php"
                  class="space-y-4">

                <?php if ($editTask): ?>

                    <input type="hidden" name="task_id"
                           value="<?= clean($editTask['id']) ?>">

                <?php endif; ?>


                <div>

                    <label class="text-xs text-gray-400">
                        Task Name
                    </label>

                    <input type="text"
                           name="title"
                           required
                           placeholder="What needs to be done?"
                           value="<?= clean($editTask['title'] ?? '') ?>"
                           class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none focus:border-yellow-600">

                </div>


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>

                        <label class="text-xs text-gray-400">
                            Due Date
                        </label>

                        <input type="date"
                               name="due_date"
                               required
                               value="<?= clean($editTask['due_date'] ?? '') ?>"
                               class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none">

                    </div>


                    <div>

                        <label class="text-xs text-gray-400">
                            Priority
                        </label>

                        <select name="priority"
                                class="w-full mt-2 p-3 rounded-xl bg-[#171b22] border border-white/10">

                            <option value="Low"
                                <?= ($editTask['priority'] ?? '') === 'Low' ? 'selected' : '' ?>>
                                Low
                            </option>

                            <option value="Medium"
                                <?= ($editTask['priority'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>
                                Medium
                            </option>

                            <option value="High"
                                <?= ($editTask['priority'] ?? '') === 'High' ? 'selected' : '' ?>>
                                High
                            </option>

                        </select>

                    </div>

                </div>


                <div class="flex gap-3">

                    <?php if ($editTask): ?>

                        <button type="submit"
                                name="update_task"
                                class="gold-bg text-black w-full p-3 rounded-xl font-bold hover:opacity-90">

                            Update Task

                        </button>

                        <a href="todo_crud.php"
                           class="bg-white/10 text-white p-3 rounded-xl text-center">

                            Cancel

                        </a>

                    <?php else: ?>

                        <button type="submit"
                                name="add_task"
                                class="gold-bg text-black w-full p-3 rounded-xl font-bold hover:opacity-90">

                            + Create Task

                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </section>


        <!-- TASK LIST -->
        <section class="glass p-6 rounded-2xl">

            <h3 class="font-bold text-lg mb-6">
                My Tasks
            </h3>


            <?php if (count($tasks) > 0): ?>

                <div class="space-y-4">

                    <?php foreach ($tasks as $task): ?>

                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b border-white/10 pb-4">

                            <div class="flex items-start gap-3">

                                <!-- STATUS CHECKBOX -->
                                <form method="POST" action="todo_crud.php">

                                    <input type="hidden"
                                           name="task_id"
                                           value="<?= clean($task['id']) ?>">

                                    <input type="hidden"
                                           name="change_status"
                                           value="1">

                                    <input type="checkbox"
                                           <?= $task['status'] === 'Completed' ? 'checked' : '' ?>
                                           onchange="this.form.submit()"
                                           class="w-4 h-4 mt-1 accent-yellow-500 cursor-pointer">

                                </form>


                                <div>

                                    <p class="font-semibold text-sm
                                        <?= $task['status'] === 'Completed' ? 'line-through text-gray-500' : '' ?>">

                                        <?= clean($task['title']) ?>

                                    </p>

                                    <p class="text-xs text-gray-500 mt-2">

                                        Due: <?= clean($task['due_date']) ?>

                                    </p>

                                    <div class="flex flex-wrap gap-2 mt-3">

                                        <span class="text-xs gold">
                                            <?= clean($task['priority']) ?> Priority
                                        </span>

                                        <span class="text-xs
                                            <?= $task['status'] === 'Completed'
                                                ? 'text-green-400'
                                                : 'text-yellow-400' ?>">

                                            • <?= clean($task['status']) ?>

                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- ACTIONS -->
                            <div class="flex gap-3">

                                <a href="todo_crud.php?edit=<?= (int)$task['id'] ?>#taskForm"
                                   class="text-blue-400 text-sm hover:underline">

                                    Edit

                                </a>


                                <form method="POST"
                                      action="todo_crud.php"
                                      onsubmit="return confirm('Are you sure you want to delete this task?');">

                                    <input type="hidden"
                                           name="task_id"
                                           value="<?= clean($task['id']) ?>">

                                    <button type="submit"
                                            name="delete_task"
                                            class="text-red-400 text-sm hover:underline">

                                        Delete

                                    </button>

                                </form>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="text-center py-12">

                    <p class="text-gray-500 text-sm">
                        No tasks available yet.
                    </p>

                    <p class="text-gray-600 text-xs mt-2">
                        Create your first task above.
                    </p>

                </div>

            <?php endif; ?>

        </section>


        <!-- FOOTER -->
        <footer class="text-center text-gray-600 text-xs mt-12 pb-5">

            © 2026 ELITE DAILY. Crafted for better living.

        </footer>

    </main>

</div>

</body>
</html>
