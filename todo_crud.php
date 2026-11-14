
<?php
session_start();

$host = "localhost";
$user = "root";
$password = "root123";
$database = "elite_daily";

try {
    $pdo = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database automatically
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    $pdo->exec("USE `$database`");

    // Create tasks table automatically
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            due_date DATE NOT NULL,
            priority ENUM('Low','Medium','High') DEFAULT 'Medium',
            status ENUM('Pending','Completed') DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}


// Escape output
function clean($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Flash message
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// ADD TASK
if (isset($_POST['add_task'])) {

    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    if ($title !== '' && $due_date !== '') {
        $sql = "INSERT INTO tasks (title, due_date, priority)
                VALUES (:title, :due_date, :priority)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':due_date' => $due_date,
            ':priority' => $priority
        ]);

        $_SESSION['message'] = "Task added successfully!";
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
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
        $sql = "UPDATE tasks
                SET title = :title,
                    due_date = :due_date,
                    priority = :priority
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':due_date' => $due_date,
            ':priority' => $priority,
            ':id' => $id
        ]);

        $_SESSION['message'] = "Task updated successfully!";
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }

    header("Location: todo_crud.php");
    exit;
}

// CHANGE TASK STATUS
if (isset($_POST['change_status'])) {

    $id = $_POST['task_id'];

    $stmt = $pdo->prepare("
        UPDATE tasks
        SET status = IF(status = 'Pending', 'Completed', 'Pending')
        WHERE id = :id
    ");

    $stmt->execute([':id' => $id]);

    $_SESSION['message'] = "Task status updated!";

    header("Location: todo_crud.php");
    exit;
}

// DELETE TASK
if (isset($_POST['delete_task'])) {

    $id = $_POST['task_id'];

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['message'] = "Task deleted successfully!";

    header("Location: todo_crud.php");
    exit;
}

// GET TASK FOR EDITING
$editTask = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editTask = $stmt->fetch(PDO::FETCH_ASSOC);
}

// READ ALL TASKS
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dashboard counts
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

    <title>ELITE Daily | To-Do Tasks</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0e13;
            color: #fff;
        }

        .gold {
            color: #d6b36a;
        }

        .gold-bg {
            background: linear-gradient(135deg, #e4c47d, #a98238);
        }

        .glass {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(15px);
        }

        input, select {
            background: #151a22 !important;
            color: white !important;
            border: 1px solid #303744 !important;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #d6b36a !important;
        }
    </style>
</head>

<body>

<div class="min-h-screen flex flex-col md:flex-row">

    <!-- SIDEBAR -->
    <aside class="w-full md:w-64 glass p-6">

        <h1 class="text-2xl font-extrabold tracking-widest gold mb-2">
            ELITE.
        </h1>

        <p class="text-xs text-gray-500 mb-10">
            PERSONAL MANAGEMENT
        </p>

        <nav class="space-y-3">

            <a href="index.php"
               class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                Dashboard
            </a>

            <a href="index.php#expenses"
               class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                Daily Expenses
            </a>

            <a href="todo_crud.php"
               class="block p-3 rounded-xl bg-white/10 gold">
                ✓ To-Do Tasks
            </a>

            <a href="index.php#transactions"
               class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                Transactions
            </a>

        </nav>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-5 md:p-10">

        <div class="mb-8">
            <p class="text-sm text-gray-500">PERSONAL MANAGEMENT</p>
            <h2 class="text-3xl font-bold mt-2">
                To-Do <span class="gold">Tasks</span>
            </h2>
            <p class="text-gray-500 text-sm mt-2">
                Organize your daily activities and stay productive.
            </p>
        </div>

        <!-- MESSAGE -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-xl border border-yellow-600/30 bg-yellow-500/10 gold">
                <?= clean($message) ?>
            </div>
        <?php endif; ?>

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

            <div class="glass p-6 rounded-2xl">
                <p class="text-gray-500 text-sm">Total Tasks</p>
                <h3 class="text-3xl font-bold mt-3">
                    <?= $totalTasks ?>
                </h3>
            </div>

            <div class="glass p-6 rounded-2xl">
                <p class="text-gray-500 text-sm">Completed Tasks</p>
                <h3 class="text-3xl font-bold mt-3 gold">
                    <?= $completedTasks ?>
                </h3>
            </div>

            <div class="glass p-6 rounded-2xl">
                <p class="text-gray-500 text-sm">Pending Tasks</p>
                <h3 class="text-3xl font-bold mt-3">
                    <?= $pendingTasks ?>
                </h3>
            </div>

        </div>

        <!-- PROGRESS -->
        <div class="glass p-6 rounded-2xl mb-8">

            <div class="flex justify-between mb-3">
                <h3 class="font-semibold">Task Progress</h3>
                <span class="gold font-bold"><?= $progress ?>%</span>
            </div>

            <div class="w-full bg-gray-800 rounded-full h-2">
                <div class="gold-bg h-2 rounded-full"
                     style="width: <?= $progress ?>%">
                </div>
            </div>

        </div>

        <!-- ADD / EDIT TASK FORM -->
        <div class="glass p-6 rounded-2xl mb-8" id="taskForm">

            <h3 class="text-xl font-bold mb-6">
                <?= $editTask ? 'Edit Task' : 'Create New Task' ?>
            </h3>

            <form method="POST" action="todo_crud.php"
                  class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <?php if ($editTask): ?>
                    <input type="hidden" name="task_id"
                           value="<?= clean($editTask['id']) ?>">
                <?php endif; ?>

                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">
                        Task Title
                    </label>

                    <input type="text" name="title"
                           placeholder="Enter your task"
                           value="<?= clean($editTask['title'] ?? '') ?>"
                           class="w-full p-3 rounded-xl"
                           required>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">
                        Due Date
                    </label>

                    <input type="date" name="due_date"
                           value="<?= clean($editTask['due_date'] ?? '') ?>"
                           class="w-full p-3 rounded-xl"
                           required>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">
                        Priority
                    </label>

                    <select name="priority"
                            class="w-full p-3 rounded-xl">

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

                <div class="md:col-span-2 flex gap-3">

                    <?php if ($editTask): ?>

                        <button type="submit" name="update_task"
                                class="gold-bg text-black font-bold px-7 py-3 rounded-xl">
                            Update Task
                        </button>

                        <a href="todo_crud.php"
                           class="bg-gray-700 px-7 py-3 rounded-xl">
                            Cancel
                        </a>

                    <?php else: ?>

                        <button type="submit" name="add_task"
                                class="gold-bg text-black font-bold px-7 py-3 rounded-xl">
                            + Add Task
                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </div>

        <!-- TASK LIST -->
        <div class="glass p-6 rounded-2xl">

            <h3 class="text-xl font-bold mb-6">My Tasks</h3>

            <?php if (count($tasks) > 0): ?>

                <div class="space-y-4">

                    <?php foreach ($tasks as $task): ?>

                        <div class="bg-white/5 border border-white/5 rounded-xl p-4
                                    flex flex-col lg:flex-row lg:items-center
                                    justify-between gap-4">

                            <div class="flex items-start gap-3">

                                <!-- STATUS -->
                                <form method="POST" action="todo_crud.php">
                                    <input type="hidden" name="task_id"
                                           value="<?= clean($task['id']) ?>">

                                    <input type="hidden" name="change_status"
                                           value="1">

                                    <input type="checkbox"
                                           <?= $task['status'] === 'Completed' ? 'checked' : '' ?>
                                           onchange="this.form.submit()"
                                           class="w-5 h-5 accent-yellow-500 mt-1 cursor-pointer">
                                </form>

                                <div>
                                    <h4 class="font-semibold
                                        <?= $task['status'] === 'Completed' ? 'line-through text-gray-500' : '' ?>">
                                        <?= clean($task['title']) ?>
                                    </h4>

                                    <p class="text-xs text-gray-500 mt-2">
                                        Due: <?= clean($task['due_date']) ?>
                                    </p>

                                    <div class="flex gap-2 mt-3">

                                        <span class="text-xs px-3 py-1 rounded-full bg-white/10 gold">
                                            <?= clean($task['priority']) ?> Priority
                                        </span>

                                        <span class="text-xs px-3 py-1 rounded-full
                                            <?= $task['status'] === 'Completed'
                                                ? 'bg-green-500/10 text-green-400'
                                                : 'bg-yellow-500/10 text-yellow-400' ?>">
                                            <?= clean($task['status']) ?>
                                        </span>

                                    </div>
                                </div>

                            </div>

                            <!-- ACTION BUTTONS -->
                            <div class="flex gap-2">

                                <a href="todo_crud.php?edit=<?= $task['id'] ?>#taskForm"
                                   class="px-4 py-2 rounded-lg bg-blue-500/10 text-blue-400 text-sm">
                                    Edit
                                </a>

                                <form method="POST" action="todo_crud.php"
                                      onsubmit="return confirm('Are you sure you want to delete this task?');">

                                    <input type="hidden" name="task_id"
                                           value="<?= clean($task['id']) ?>">

                                    <button type="submit" name="delete_task"
                                            class="px-4 py-2 rounded-lg bg-red-500/10 text-red-400 text-sm">
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="text-center py-12">
                    <p class="text-gray-500">No tasks available yet.</p>
                    <p class="text-sm text-gray-600 mt-2">
                        Create your first task above.
                    </p>
                </div>

            <?php endif; ?>

        </div>

        <p class="text-center text-gray-600 text-xs mt-8">
            ELITE Daily | Personal Management System
        </p>

    </main>

</div>

</body>
</html>
