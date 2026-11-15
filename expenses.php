
<?php
// =============================================
// DATABASE CONNECTION
// =============================================

$pdo = new PDO(
    "mysql:host=localhost;
    dbname=money;
    charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);

// =============================================
// CREATE EXPENSE TABLE
// =============================================

$pdo->exec("
    CREATE TABLE IF NOT EXISTS daily_expenses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        expense_date DATE NOT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_expense_date (expense_date),
        INDEX idx_expense_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// =============================================
// AJAX CRUD HANDLER
// =============================================

if (isset($_GET['action'])) {

    header('Content-Type: application/json');

    try {

        $action = $_GET['action'];

        // READ
        if ($action === 'read') {

            $stmt = $pdo->query("
                SELECT * FROM daily_expenses
                ORDER BY expense_date DESC, id DESC
            ");

            $expenses = $stmt->fetchAll();

            $today = date('Y-m-d');
            $month = date('Y-m');

            $todayStmt = $pdo->prepare("
                SELECT COALESCE(SUM(amount), 0)
                FROM daily_expenses
                WHERE expense_date = ?
            ");
            $todayStmt->execute([$today]);

            $monthStmt = $pdo->prepare("
                SELECT COALESCE(SUM(amount), 0)
                FROM daily_expenses
                WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?
            ");
            $monthStmt->execute([$month]);

            echo json_encode([
                'success' => true,
                'expenses' => $expenses,
                'total' => array_sum(array_column($expenses, 'amount')),
                'today' => $todayStmt->fetchColumn(),
                'monthly' => $monthStmt->fetchColumn(),
                'count' => count($expenses)
            ]);

            exit;
        }

        // RECEIVE JSON
        $data = json_decode(
            file_get_contents('php://input'),
            true
        ) ?? [];

        $title = trim($data['title'] ?? '');
        $amount = $data['amount'] ?? '';
        $category = trim($data['category'] ?? '');
        $date = $data['expense_date'] ?? '';
        $notes = trim($data['notes'] ?? '');

        // VALIDATION
        if (in_array($action, ['create', 'update'], true)) {

            $dateObj = DateTime::createFromFormat('!Y-m-d', $date);

            if (
                $title === '' ||
                strlen($title) > 150 ||
                $category === '' ||
                strlen($category) > 50 ||
                !is_numeric($amount) ||
                !is_finite((float)$amount) ||
                (float)$amount <= 0 ||
                (float)$amount > 9999999999.99 ||
                !$dateObj ||
                $dateObj->format('Y-m-d') !== $date
            ) {
                throw new Exception('Please enter valid expense details.');
            }
        }

        // CREATE
        if ($action === 'create') {

            $stmt = $pdo->prepare("
                INSERT INTO daily_expenses
                (title, amount, category, expense_date, notes)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $title,
                $amount,
                $category,
                $date,
                $notes ?: null
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Expense added successfully!'
            ]);

            exit;
        }

        // UPDATE
        if ($action === 'update') {

            $id = filter_var(
                $data['id'] ?? '',
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new Exception('Invalid expense ID.');
            }

            $stmt = $pdo->prepare("
                UPDATE daily_expenses
                SET title = ?,
                    amount = ?,
                    category = ?,
                    expense_date = ?,
                    notes = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $title,
                $amount,
                $category,
                $date,
                $notes ?: null,
                $id
            ]);

            if ($stmt->rowCount() === 0) {

                $check = $pdo->prepare("
                    SELECT id FROM daily_expenses WHERE id = ?
                ");

                $check->execute([$id]);

                if (!$check->fetch()) {
                    throw new Exception('Expense not found.');
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Expense updated successfully!'
            ]);

            exit;
        }

        // DELETE
        if ($action === 'delete') {

            $id = filter_var(
                $data['id'] ?? '',
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new Exception('Invalid expense ID.');
            }

            $stmt = $pdo->prepare("
                DELETE FROM daily_expenses WHERE id = ?
            ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Expense not found.');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Expense deleted successfully!'
            ]);

            exit;
        }

        throw new Exception('Invalid action.');

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => $e instanceof PDOException
                ? 'Database operation failed.'
                : $e->getMessage()
        ]);

        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Elite Daily | Expense & Tasks</title>

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

        input, select, textarea {
            color-scheme: dark;
        }

        .expense-item {
            border-bottom: 1px solid rgba(255,255,255,0.08);
            transition: background 0.2s ease;
        }

        .expense-item:hover {
            background: rgba(255,255,255,0.025);
        }

        .gold-btn {
            background: linear-gradient(135deg, #e4c47d, #a98238);
            color: #090b0f;
            transition: all .2s ease;
        }

        .gold-btn:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        #toast {
            transition: all .3s ease;
        }
        body {
    font-family: 'Inter', sans-serif;
    background: #0b0e13;
}

.glass {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
}

.gold {
    color: #d6b36a;
}

.gold-bg {
    background: linear-gradient(135deg,#e4c47d,#a98238);
}

.gold-border {
    border-color: rgba(214,179,106,0.35);
}

.input-dark {
    background: #141923;
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    outline: none;
}

.input-dark:focus {
    border-color: #d6b36a;
}
.input-dark option {
    background: #141923;
}

.gold-button {
    background: linear-gradient(135deg,#e4c47d,#a98238);
    color: #111;
    font-weight: 700;
    transition: 0.3s;
}
.gold-button:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

.sidebar-link {
    display: block;
    padding: 13px;
    border-radius: 12px;
    color: #9ca3af;
    transition: 0.2s;
}

.sidebar-link:hover,
.sidebar-link.select{
    background: linear-gradient(135deg,#e4c47d,#a98238);
    color: #111;
}

table {
    border-collapse: collapse;
    width: 100%;
    min-width: 750px;
}




    </style>

</head>


<body class="text-white min-h-screen">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->

    <?php include 'include/sidebar.php'; ?>


    <!-- MAIN CONTENT -->

    <main class="flex-1 p-5 md:p-10">


        <!-- HEADER -->

        <header class="flex justify-between items-center mb-10">

            <div>

                <p class="text-gray-500 text-sm uppercase">
                    <?= strtoupper(date('l, F d')) ?>
                </p>

                <h2 class="text-2xl md:text-3xl font-bold mt-2">
                    Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?> 👋
                </h2>

                <p class="text-gray-400 text-sm mt-1">
                    Welcome back. Here's your daily overview.
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


        <!-- SUMMARY CARDS -->

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">


            <!-- TODAY -->

            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">
                    Today's Expenses
                </p>

                <h2 id="todayExpenses"
                    class="text-2xl md:text-3xl font-bold mt-3">
                    Rs. 0.00
                </h2>

                <p class="text-red-400 text-xs mt-3">
                    ↑ Today's spending
                </p>

            </div>


            <!-- MONTHLY -->

            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">
                    Monthly Expenses
                </p>

                <h2 id="monthlyExpenses"
                    class="text-2xl md:text-3xl font-bold mt-3">
                    Rs. 0.00
                </h2>

                <p class="text-gray-500 text-xs mt-3">
                    <?= date('F Y') ?>
                </p>

            </div>


            <!-- ALL EXPENSES -->

            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">
                    Total Expenses
                </p>

                <h2 id="totalExpenses"
                    class="text-2xl md:text-3xl font-bold mt-3 gold">
                    Rs. 0.00
                </h2>

                <p class="text-green-400 text-xs mt-3">
                    All recorded expenses
                </p>

            </div>


            <!-- RECORD COUNT -->

            <div class="glass p-6 rounded-2xl">

                <p class="text-gray-400 text-sm">
                    Expense Records
                </p>

                <h2 id="expenseCount"
                    class="text-2xl md:text-3xl font-bold mt-3">
                    0
                </h2>

                <p class="text-gray-500 text-xs mt-3">
                    Total transactions
                </p>

            </div>


        </section>


        <!-- FORMS -->

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">


            <!-- EXPENSE FORM -->

            <div id="expenses" class="glass p-6 rounded-2xl">

                <div class="flex justify-between items-center mb-6">

                    <h3 id="expenseFormTitle" class="text-lg font-bold">
                        Add Daily Expense
                    </h3>

                    <span class="gold text-sm">
                        ✦ EXPENSE
                    </span>

                </div>


                <form id="expenseForm" class="space-y-4">

                    <input type="hidden" id="expenseId">


                    <!-- TITLE -->

                    <div>

                        <label class="text-xs text-gray-400">
                            Expense Title
                        </label>

                        <input
                            id="expenseTitle"
                            type="text"
                            maxlength="150"
                            required
                            placeholder="e.g. Lunch, Transport"
                            class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none focus:border-yellow-600"
                        >

                    </div>


                    <!-- AMOUNT AND CATEGORY -->

                    <div class="grid grid-cols-2 gap-4">

                        <div>

                            <label class="text-xs text-gray-400">
                                Amount (LKR)
                            </label>

                            <input
                                id="expenseAmount"
                                type="number"
                                min="0.01"
                                max="9999999999.99"
                                step="0.01"
                                required
                                placeholder="0.00"
                                class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none focus:border-yellow-600"
                            >

                        </div>


                        <div>

                            <label class="text-xs text-gray-400">
                                Category
                            </label>

                            <select
                                id="expenseCategory"
                                required
                                class="w-full mt-2 p-3 rounded-xl bg-[#171b22] border border-white/10 outline-none"
                            >

                                <option value="">Select</option>
                                <option value="Food">Food</option>
                                <option value="Transport">Transport</option>
                                <option value="Shopping">Shopping</option>
                                <option value="Bills">Bills</option>
                                <option value="Education">Education</option>
                                <option value="Health">Health</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Other">Other</option>

                            </select>

                        </div>

                    </div>


                    <!-- DATE -->

                    <div>

                        <label class="text-xs text-gray-400">
                            Expense Date
                        </label>

                        <input
                            id="expenseDate"
                            type="date"
                            required
                            class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none"
                        >

                    </div>


                    <!-- NOTES -->

                    <div>

                        <label class="text-xs text-gray-400">
                            Notes
                        </label>

                        <textarea
                            id="expenseNotes"
                            rows="2"
                            placeholder="Additional information..."
                            class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none focus:border-yellow-600"
                        ></textarea>

                    </div>


                    <!-- BUTTONS -->

                    <div class="flex gap-3">

                        <button
                            type="submit"
                            id="expenseSubmitBtn"
                            class="gold-bg text-black w-full p-3 rounded-xl font-bold hover:opacity-90"
                        >
                            + Add Expense
                        </button>

                        <button
                            type="button"
                            id="cancelEditBtn"
                            onclick="resetExpenseForm()"
                            class="hidden px-5 py-3 rounded-xl border border-white/10 text-gray-300 hover:bg-white/5"
                        >
                            Cancel
                        </button>

                    </div>

                </form>

            </div>


            <!-- TASK FORM -->

            <div id="tasks" class="glass p-6 rounded-2xl">

                <div class="flex justify-between mb-6">

                    <h3 class="text-lg font-bold">
                        Create New Task
                    </h3>

                    <span class="gold">
                        ✦ PRODUCTIVITY
                    </span>

                </div>


                <form id="taskForm" class="space-y-4">

                    <div>

                        <label class="text-xs text-gray-400">
                            Task Name
                        </label>

                        <input
                            id="taskTitle"
                            required
                            placeholder="What needs to be done?"
                            class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none"
                        >

                    </div>


                    <div class="grid grid-cols-2 gap-4">

                        <div>

                            <label class="text-xs text-gray-400">
                                Due Date
                            </label>

                            <input
                                id="taskDate"
                                type="date"
                                required
                                class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10"
                            >

                        </div>


                        <div>

                            <label class="text-xs text-gray-400">
                                Priority
                            </label>

                            <select
                                id="taskPriority"
                                class="w-full mt-2 p-3 rounded-xl bg-[#171b22] border border-white/10"
                            >

                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>

                            </select>

                        </div>

                    </div>


                    <button class="gold-bg text-black w-full p-3 rounded-xl font-bold hover:opacity-90">
                        + Create Task
                    </button>

                </form>

            </div>

        </section>


        <!-- RECENT DATA -->

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">


            <!-- RECENT EXPENSES -->

            <div id="transactions" class="glass p-6 rounded-2xl">

                <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">

                    <h3 class="font-bold text-lg">
                        Recent Expenses
                    </h3>

                    <input
                        id="expenseSearch"
                        type="text"
                        placeholder="Search expenses..."
                        class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-xs outline-none focus:border-yellow-600"
                    >

                </div>


                <div id="expenseList" class="space-y-4">

                    <p class="text-gray-500 text-sm">
                        Loading expenses...
                    </p>

                </div>

            </div>


            <!-- TASKS -->

            <div class="glass p-6 rounded-2xl">

                <h3 class="font-bold text-lg mb-6">
                    Today's Tasks
                </h3>

                <div id="taskList" class="space-y-4">

                    <p class="text-gray-500 text-sm">
                        Your tasks will appear here.
                    </p>

                </div>

            </div>


        </section>


        <!-- FOOTER -->

        <footer class="text-center text-gray-600 text-xs mt-12 pb-5">

            © <?= date('Y') ?> ELITE DAILY. Crafted for better living.

        </footer>


    </main>

</div>


<!-- TOAST NOTIFICATION -->

<div
    id="toast"
    role="status"
    aria-live="polite"
    class="hidden fixed top-5 right-5 px-6 py-4 rounded-xl text-black font-semibold shadow-lg z-50"
></div>


<script>

// =============================================
// EXPENSE CRUD JAVASCRIPT
// =============================================

const API = window.location.pathname;

let expenses = [];

let toastTimer;


// FORMAT CURRENCY

function formatMoney(value) {

    return 'Rs. ' + Number(value).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

}


// TOAST NOTIFICATION

function showToast(message, success = true) {

    const toast = document.getElementById('toast');

    clearTimeout(toastTimer);

    toast.textContent = message;

    toast.className = `
        fixed top-5 right-5 px-6 py-4 rounded-xl
        font-semibold shadow-lg z-50
        ${success
            ? 'bg-[#d6b36a] text-black'
            : 'bg-red-500 text-white'}
    `;

    toastTimer = setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);

}


// API REQUEST

async function request(action, data = {}) {

    const response = await fetch(
        API + '?action=' + encodeURIComponent(action),
        {
            method: action === 'read' ? 'GET' : 'POST',

            headers: {
                'Content-Type': 'application/json'
            },

            body: action === 'read'
                ? null
                : JSON.stringify(data)
        }
    );

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Request failed.');
    }

    return result;

}


// ESCAPE HTML

function escapeHTML(value) {

    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);

}


// READ EXPENSES

async function loadExpenses() {

    try {

        const result = await request('read');

        expenses = result.expenses;

        // UPDATE SUMMARY CARDS

        document.getElementById('todayExpenses').textContent =
            formatMoney(result.today);

        document.getElementById('monthlyExpenses').textContent =
            formatMoney(result.monthly);

        document.getElementById('totalExpenses').textContent =
            formatMoney(result.total);

        document.getElementById('expenseCount').textContent =
            result.count;


        // DISPLAY TABLE

        displayExpenses(expenses);

    } catch(error) {

        showToast(error.message, false);

    }

}


// DISPLAY EXPENSES

function displayExpenses(data) {

    const list = document.getElementById('expenseList');

    if (data.length === 0) {

        list.innerHTML = `
            <div class="text-center py-10">

                <p class="text-3xl mb-3">✦</p>

                <p class="text-gray-400 text-sm">
                    No expenses found.
                </p>

                <p class="text-gray-600 text-xs mt-2">
                    Add your first expense above.
                </p>

            </div>
        `;

        return;

    }


    list.innerHTML = data.map(expense => `

        <div class="expense-item flex justify-between items-center gap-3 py-4">

            <div class="min-w-0">

                <p class="font-semibold text-sm truncate">
                    ${escapeHTML(expense.title)}
                </p>

                <div class="flex flex-wrap gap-2 mt-2">

                    <span class="text-xs gold">
                        ${escapeHTML(expense.category)}
                    </span>

                    <span class="text-xs text-gray-500">
                        ${escapeHTML(expense.expense_date)}
                    </span>

                </div>

                ${expense.notes ? `
                    <p class="text-xs text-gray-500 mt-2 break-words">
                        ${escapeHTML(expense.notes)}
                    </p>
                ` : ''}

            </div>


            <div class="text-right flex-shrink-0">

                <p class="gold font-bold text-sm">
                    ${formatMoney(expense.amount)}
                </p>

                <div class="flex gap-3 justify-end mt-3">

                    <button
                        onclick="editExpense(${Number(expense.id)})"
                        class="text-blue-400 text-xs hover:text-blue-300"
                    >
                        Edit
                    </button>

                    <button
                        onclick="deleteExpense(${Number(expense.id)})"
                        class="text-red-400 text-xs hover:text-red-300"
                    >
                        Delete
                    </button>

                </div>

            </div>

        </div>

    `).join('');

}


// CREATE / UPDATE EXPENSE

document.getElementById('expenseForm').addEventListener(
    'submit',
    async function(e) {

        e.preventDefault();

        const id = document.getElementById('expenseId').value;

        const action = id ? 'update' : 'create';

        const data = {

            id: id,

            title: document.getElementById('expenseTitle').value.trim(),

            amount: document.getElementById('expenseAmount').value,

            category: document.getElementById('expenseCategory').value,

            expense_date: document.getElementById('expenseDate').value,

            notes: document.getElementById('expenseNotes').value.trim()

        };


        const button = document.getElementById('expenseSubmitBtn');

        button.disabled = true;

        button.textContent = 'Processing...';


        try {

            const result = await request(action, data);

            showToast(result.message);

            resetExpenseForm();

            await loadExpenses();

        } catch(error) {

            showToast(error.message, false);

        } finally {

            button.disabled = false;

            button.textContent = 'Add Expense';

        }

    }
);


// EDIT EXPENSE

function editExpense(id) {

    const expense = expenses.find(
        item => Number(item.id) === Number(id)
    );

    if (!expense) {

        showToast('Expense not found.', false);

        return;

    }


    document.getElementById('expenseId').value =
        expense.id;

    document.getElementById('expenseTitle').value =
        expense.title;

    document.getElementById('expenseAmount').value =
        expense.amount;

    document.getElementById('expenseCategory').value =
        expense.category;

    document.getElementById('expenseDate').value =
        expense.expense_date;

    document.getElementById('expenseNotes').value =
        expense.notes || '';


    document.getElementById('expenseFormTitle').textContent =
        'Update Daily Expense';

    document.getElementById('expenseSubmitBtn').textContent =
        'Update Expense';

    document.getElementById('cancelEditBtn').classList.remove('hidden');


    document.getElementById('expenses').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });

}


// DELETE EXPENSE

async function deleteExpense(id) {

    if (!confirm('Are you sure you want to delete this expense?')) {
        return;
    }

    try {

        const result = await request('delete', {
            id: id
        });

        showToast(result.message);

        await loadExpenses();

    } catch(error) {

        showToast(error.message, false);

    }

}


// RESET FORM

function resetExpenseForm() {

    document.getElementById('expenseForm').reset();

    document.getElementById('expenseId').value = '';

    document.getElementById('expenseFormTitle').textContent =
        'Add Daily Expense';

    document.getElementById('expenseSubmitBtn').textContent =
        '+ Add Expense';

    document.getElementById('cancelEditBtn').classList.add('hidden');

    document.getElementById('expenseDate').value =
        new Date().toLocaleDateString('en-CA');

}


// SEARCH EXPENSES

document.getElementById('expenseSearch').addEventListener(
    'input',
    function() {

        const keyword = this.value.toLowerCase();

        const filtered = expenses.filter(expense =>

            expense.title.toLowerCase().includes(keyword) ||

            expense.category.toLowerCase().includes(keyword) ||

            (expense.notes || '').toLowerCase().includes(keyword)

        );

        displayExpenses(filtered);

    }
);


// =============================================
// TASK FRONTEND DEMO
// =============================================

const taskForm = document.getElementById('taskForm');


taskForm.addEventListener('submit', function(e) {

    e.preventDefault();

    const title = document.getElementById('taskTitle').value;

    const priority = document.getElementById('taskPriority').value;

    const date = document.getElementById('taskDate').value;


    const item = document.createElement('label');

    item.className =
        'flex justify-between items-center border-b border-white/10 pb-3 gap-3';


    const left = document.createElement('div');

    left.className = 'flex items-center gap-3';


    const checkbox = document.createElement('input');

    checkbox.type = 'checkbox';

    checkbox.className = 'accent-yellow-500 w-4 h-4';


    const name = document.createElement('span');

    name.className = 'text-sm';

    name.textContent = title;


    checkbox.addEventListener('change', () => {

        name.classList.toggle('line-through', checkbox.checked);

        name.classList.toggle('text-gray-500', checkbox.checked);

    });


    const taskDetails = document.createElement('div');

    taskDetails.className = 'flex flex-col gap-1';

    taskDetails.append(name);


    const dueDate = document.createElement('span');

    dueDate.className = 'text-xs text-gray-500';

    dueDate.textContent = date;

    taskDetails.append(dueDate);


    left.append(checkbox, taskDetails);


    const badge = document.createElement('span');

    badge.className = 'text-xs gold';

    badge.textContent = priority;


    item.append(left, badge);


    const list = document.getElementById('taskList');

    const emptyMessage = list.querySelector('p');

    if (emptyMessage) {
        list.innerHTML = '';
    }

    list.prepend(item);


    taskForm.reset();

    showToast('Task created successfully!');

});


// INITIALIZE

document.getElementById('expenseDate').value =
    new Date().toLocaleDateString('en-CA');

loadExpenses();

</script>

</body>
</html>
