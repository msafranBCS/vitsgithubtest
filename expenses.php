<?php
/*
|--------------------------------------------------------------------------
| ELITE Daily - Daily Expenses CRUD
|--------------------------------------------------------------------------
| Update the database settings below to match your MySQL configuration.
| The daily_expenses table is created automatically if it does not exist.
*/

session_start();

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'elite_daily';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die('Database connection failed: ' . htmlspecialchars($conn->connect_error));
}
$conn->set_charset('utf8mb4');

$conn->query("
    CREATE TABLE IF NOT EXISTS daily_expenses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        expense_date DATE NOT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirectWithMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header('Location: expenses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        redirectWithMessage('Invalid request. Please try again.', 'error');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $title = trim($_POST['title'] ?? '');
        $amount = filter_var($_POST['amount'] ?? '', FILTER_VALIDATE_FLOAT);
        $category = trim($_POST['category'] ?? '');
        $expenseDate = $_POST['expense_date'] ?? '';
        $notes = trim($_POST['notes'] ?? '');

        $allowedCategories = ['Food', 'Transport', 'Shopping', 'Bills', 'Education', 'Other'];
        $dateObject = DateTime::createFromFormat('Y-m-d', $expenseDate);
        $validDate = $dateObject && $dateObject->format('Y-m-d') === $expenseDate;

        if ($title === '' || mb_strlen($title) > 150 || $amount === false || $amount <= 0 ||
            !in_array($category, $allowedCategories, true) || !$validDate) {
            redirectWithMessage('Please enter valid expense details.', 'error');
        }

        if ($action === 'create') {
            $stmt = $conn->prepare('INSERT INTO daily_expenses (title, amount, category, expense_date, notes) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sdsss', $title, $amount, $category, $expenseDate, $notes);
            $stmt->execute();
            $stmt->close();
            redirectWithMessage('Expense added successfully!');
        } else {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id || $id < 1) {
                redirectWithMessage('Invalid expense selected.', 'error');
            }
            $stmt = $conn->prepare('UPDATE daily_expenses SET title = ?, amount = ?, category = ?, expense_date = ?, notes = ? WHERE id = ?');
            $stmt->bind_param('sdsssi', $title, $amount, $category, $expenseDate, $notes, $id);
            $stmt->execute();
            $updated = $stmt->affected_rows;
            $stmt->close();
            redirectWithMessage('Expense updated successfully!');
        }
    }

    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            redirectWithMessage('Invalid expense selected.', 'error');
        }
        $stmt = $conn->prepare('DELETE FROM daily_expenses WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();
        redirectWithMessage($deleted ? 'Expense deleted successfully!' : 'Expense was not found.', $deleted ? 'success' : 'error');
    }
}

$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$stmt = $conn->prepare('SELECT COALESCE(SUM(amount), 0) AS total FROM daily_expenses WHERE expense_date = ?');
$stmt->bind_param('s', $today);
$stmt->execute();
$todayTotal = (float)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare('SELECT COALESCE(SUM(amount), 0) AS total FROM daily_expenses WHERE expense_date BETWEEN ? AND ?');
$stmt->bind_param('ss', $monthStart, $monthEnd);
$stmt->execute();
$monthTotal = (float)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$search = trim($_GET['search'] ?? '');
$filterCategory = trim($_GET['category'] ?? '');
$filterDate = trim($_GET['date'] ?? '');

$sql = 'SELECT id, title, amount, category, expense_date, notes FROM daily_expenses WHERE 1=1';
$params = [];
$types = '';

if ($search !== '') {
    $sql .= ' AND title LIKE ?';
    $params[] = '%' . $search . '%';
    $types .= 's';
}
$categories = ['Food', 'Transport', 'Shopping', 'Bills', 'Education', 'Other'];
if (in_array($filterCategory, $categories, true)) {
    $sql .= ' AND category = ?';
    $params[] = $filterCategory;
    $types .= 's';
}
if ($filterDate !== '' && DateTime::createFromFormat('Y-m-d', $filterDate)) {
    $sql .= ' AND expense_date = ?';
    $params[] = $filterDate;
    $types .= 's';
}
$sql .= ' ORDER BY expense_date DESC, id DESC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$expenses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$flash = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'success';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Expenses | ELITE Daily</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0b0e13; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); backdrop-filter: blur(15px); }
        .gold { color: #d6b36a; }
        .gold-bg { background: linear-gradient(135deg,#e4c47d,#a98238); }
        input, select, textarea { color-scheme: dark; }
        .field { width:100%; margin-top:8px; padding:12px; border-radius:12px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); outline:none; }
        .field:focus { border-color:#a98238; }
        .modal-backdrop { background:rgba(0,0,0,.75); }
    </style>
</head>
<body class="text-white min-h-screen">
<div class="flex min-h-screen">
    <aside class="hidden md:flex w-64 flex-col p-6 border-r border-white/10">
        <h1 class="text-2xl font-extrabold gold tracking-wide">ELITE<span class="text-white">.</span></h1>
        <p class="text-gray-500 text-xs mt-1">PERSONAL MANAGEMENT</p>
        <nav class="mt-12 space-y-3">
            <a href="index.php" class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">◈ Dashboard</a>
            <a href="expenses.php" class="block p-3 rounded-xl gold-bg text-black font-semibold">◷ Daily Expenses</a>
            <a href="index.php#tasks" class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">✓ To-Do Tasks</a>
            <a href="expenses.php" class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">▤ Transactions</a>
        </nav>
        <div class="mt-auto glass p-4 rounded-2xl">
            <p class="text-xs text-gray-500">YOUR DAILY GOAL</p>
            <h2 class="text-xl font-bold gold mt-2">Stay Focused.</h2>
            <p class="text-xs text-gray-400 mt-2">Manage your money. Manage your time.</p>
        </div>
    </aside>

    <main class="flex-1 p-5 md:p-10">
        <header class="flex justify-between items-center mb-8">
            <div>
                <p class="text-gray-500 text-sm"><?= strtoupper(date('l, F d')) ?></p>
                <h2 class="text-2xl md:text-3xl font-bold mt-2">Daily Expenses</h2>
                <p class="text-gray-400 text-sm mt-1">Track, manage and review your daily spending.</p>
            </div>
            <div class="hidden sm:block text-right">
                <p class="gold text-sm font-semibold">PREMIUM EDITION</p>
                <p class="text-gray-500 text-xs mt-1">PERSONAL DASHBOARD</p>
            </div>
        </header>

        <?php if ($flash): ?>
            <div id="flashMessage" class="mb-6 rounded-xl border px-4 py-3 text-sm <?= $flashType === 'error' ? 'border-red-500/30 bg-red-500/10 text-red-300' : 'border-green-500/30 bg-green-500/10 text-green-300' ?>">
                <?= h($flash) ?>
            </div>
        <?php endif; ?>

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            <div class="glass p-6 rounded-2xl">
                <p class="text-gray-400 text-sm">Today's Expenses</p>
                <h2 class="text-3xl font-bold mt-3">Rs. <?= number_format($todayTotal, 2) ?></h2>
                <p class="text-red-400 text-xs mt-3">Daily spending</p>
            </div>
            <div class="glass p-6 rounded-2xl">
                <p class="text-gray-400 text-sm">Monthly Expenses</p>
                <h2 class="text-3xl font-bold mt-3">Rs. <?= number_format($monthTotal, 2) ?></h2>
                <p class="text-gray-500 text-xs mt-3"><?= h(date('F Y')) ?></p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <div class="glass p-6 rounded-2xl xl:col-span-1">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Add Daily Expense</h3>
                    <span class="gold text-xs">✦ EXPENSE</span>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="create">
                    <div>
                        <label class="text-xs text-gray-400">Expense Title</label>
                        <input class="field" name="title" maxlength="150" required placeholder="e.g. Lunch, Transport">
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">Amount (LKR)</label>
                        <input class="field" name="amount" type="number" min="0.01" step="0.01" required placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">Category</label>
                        <select class="field" name="category" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= h($category) ?>"><?= h($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">Expense Date</label>
                        <input class="field" name="expense_date" type="date" value="<?= h($today) ?>" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">Notes (Optional)</label>
                        <textarea class="field" name="notes" rows="2" placeholder="Add a short note..."></textarea>
                    </div>
                    <button class="gold-bg text-black w-full p-3 rounded-xl font-bold hover:opacity-90 transition">+ Add Expense</button>
                </form>
            </div>

            <div class="glass p-6 rounded-2xl xl:col-span-2">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold">Expense History</h3>
                    <span class="gold text-xs">✦ TRANSACTIONS</span>
                </div>
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                    <input class="field mt-0" name="search" value="<?= h($search) ?>" placeholder="Search title...">
                    <select class="field mt-0" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= h($category) ?>" <?= $filterCategory === $category ? 'selected' : '' ?>><?= h($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input class="field mt-0" type="date" name="date" value="<?= h($filterDate) ?>">
                    <div class="sm:col-span-3 flex gap-2">
                        <button class="gold-bg text-black px-5 py-2 rounded-xl text-sm font-bold">Apply Filters</button>
                        <a href="expenses.php" class="border border-white/10 px-5 py-2 rounded-xl text-sm text-gray-300 hover:bg-white/5">Reset</a>
                    </div>
                </form>

                <div class="space-y-3">
                    <?php if (!$expenses): ?>
                        <div class="text-center py-12 text-gray-500">
                            <div class="text-3xl mb-3">◷</div>
                            <p class="text-sm">No expenses found. Add your first expense.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm"><?= h($expense['title']) ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?= h($expense['category']) ?> · <?= h(date('d M Y', strtotime($expense['expense_date']))) ?>
                                    </p>
                                    <?php if ($expense['notes'] !== ''): ?>
                                        <p class="text-xs text-gray-500 mt-1"><?= h($expense['notes']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-4">
                                    <span class="gold font-bold text-sm whitespace-nowrap">Rs. <?= number_format((float)$expense['amount'], 2) ?></span>
                                    <button type="button"
                                        class="edit-btn text-xs border border-white/10 rounded-lg px-3 py-2 hover:bg-white/10"
                                        data-id="<?= (int)$expense['id'] ?>"
                                        data-title="<?= h($expense['title']) ?>"
                                        data-amount="<?= h($expense['amount']) ?>"
                                        data-category="<?= h($expense['category']) ?>"
                                        data-date="<?= h($expense['expense_date']) ?>"
                                        data-notes="<?= h($expense['notes']) ?>">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$expense['id'] ?>">
                                        <button class="text-xs text-red-400 border border-red-400/20 rounded-lg px-3 py-2 hover:bg-red-500/10">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-600 mt-5">Showing <?= count($expenses) ?> expense(s).</p>
            </div>
        </section>

        <footer class="text-center text-gray-600 text-xs mt-12 pb-5">© <?= date('Y') ?> ELITE DAILY. Crafted for better living.</footer>
    </main>
</div>

<!-- Edit Expense Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 modal-backdrop items-center justify-center p-4">
    <div class="glass bg-[#11151c] w-full max-w-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold">Edit Expense</h3>
            <button type="button" id="closeModal" class="text-gray-400 hover:text-white text-2xl">&times;</button>
        </div>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editId">
            <div>
                <label class="text-xs text-gray-400">Expense Title</label>
                <input class="field" name="title" id="editTitle" maxlength="150" required>
            </div>
            <div>
                <label class="text-xs text-gray-400">Amount (LKR)</label>
                <input class="field" name="amount" id="editAmount" type="number" min="0.01" step="0.01" required>
            </div>
            <div>
                <label class="text-xs text-gray-400">Category</label>
                <select class="field" name="category" id="editCategory" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= h($category) ?>"><?= h($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-400">Expense Date</label>
                <input class="field" name="expense_date" id="editDate" type="date" required>
            </div>
            <div>
                <label class="text-xs text-gray-400">Notes (Optional)</label>
                <textarea class="field" name="notes" id="editNotes" rows="2"></textarea>
            </div>
            <div class="flex gap-3">
                <button class="gold-bg text-black flex-1 p-3 rounded-xl font-bold">Save Changes</button>
                <button type="button" id="cancelModal" class="border border-white/10 flex-1 p-3 rounded-xl text-gray-300">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('editModal');
function closeEditModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('editId').value = button.dataset.id;
        document.getElementById('editTitle').value = button.dataset.title;
        document.getElementById('editAmount').value = button.dataset.amount;
        document.getElementById('editCategory').value = button.dataset.category;
        document.getElementById('editDate').value = button.dataset.date;
        document.getElementById('editNotes').value = button.dataset.notes;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
});
document.getElementById('closeModal').addEventListener('click', closeEditModal);
document.getElementById('cancelModal').addEventListener('click', closeEditModal);
modal.addEventListener('click', event => { if (event.target === modal) closeEditModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeEditModal(); });
setTimeout(() => {
    const flash = document.getElementById('flashMessage');
    if (flash) flash.style.display = 'none';
}, 3500);
</script>
</body>
</html>
