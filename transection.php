
<?php
// =====================================
// DATABASE CONNECTION
// =====================================

$pdo = new PDO(
    "mysql:host=localhost;
    dbname=money;
    charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

// =====================================
// AJAX CRUD API
// =====================================

if (isset($_GET['api'])) {

    header("Content-Type: application/json");

    function sendResponse($success, $message, $data = null) {
        echo json_encode([
            "success" => $success,
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    try {

        // READ
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $search = trim($_GET['search'] ?? '');
            $type = $_GET['type'] ?? '';

            $sql = "SELECT * FROM transactions WHERE 1=1";
            $params = [];

            if ($search !== '') {
                $sql .= " AND (title LIKE ? OR category LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            if (in_array($type, ['income', 'expense'], true)) {
                $sql .= " AND type = ?";
                $params[] = $type;
            }

            $sql .= " ORDER BY transaction_date DESC, id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            sendResponse(true, "Loaded successfully", $stmt->fetchAll());
        }

        $input = json_decode(file_get_contents("php://input"), true) ?? [];
        $action = $input['action'] ?? '';

        // DELETE
        if ($action === 'delete') {

            $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id) sendResponse(false, "Invalid transaction ID.");

            $stmt = $pdo->prepare(
                "DELETE FROM transactions WHERE id = ?"
            );

            $stmt->execute([$id]);

            if (!$stmt->rowCount()) {
                sendResponse(false, "Transaction not found.");
            }

            sendResponse(true, "Transaction deleted successfully!");
        }

        // VALIDATION
        $title = trim($input['title'] ?? '');
        $amount = $input['amount'] ?? '';
        $type = $input['type'] ?? '';
        $category = trim($input['category'] ?? '');
        $date = $input['transaction_date'] ?? '';
        $description = trim($input['description'] ?? '');

        $dateObj = DateTime::createFromFormat('!Y-m-d', $date);

        if (
            $title === '' ||
            !is_numeric($amount) ||
            (float)$amount <= 0 ||
            !in_array($type, ['income', 'expense'], true) ||
            $category === '' ||
            !$dateObj ||
            $dateObj->format('Y-m-d') !== $date
        ) {
            sendResponse(false, "Please enter valid details.");
        }

        // CREATE
        if ($action === 'create') {

            $stmt = $pdo->prepare(
                "INSERT INTO transactions
                (title, amount, type, category, transaction_date, description)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $title,
                $amount,
                $type,
                $category,
                $date,
                $description
            ]);

            sendResponse(true, "Transaction added successfully!");
        }

        // UPDATE
        if ($action === 'update') {

            $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id) sendResponse(false, "Invalid transaction ID.");

            $stmt = $pdo->prepare(
                "UPDATE transactions SET
                title=?, amount=?, type=?, category=?,
                transaction_date=?, description=?
                WHERE id=?"
            );

            $stmt->execute([
                $title,
                $amount,
                $type,
                $category,
                $date,
                $description,
                $id
            ]);

            if (!$stmt->rowCount()) {
                $check = $pdo->prepare(
                    "SELECT id FROM transactions WHERE id=?"
                );
                $check->execute([$id]);

                if (!$check->fetch()) {
                    sendResponse(false, "Transaction not found.");
                }
            }

            sendResponse(true, "Transaction updated successfully!");
        }

        sendResponse(false, "Invalid action.");

    } catch (PDOException $e) {

        http_response_code(500);
        sendResponse(false, "Database operation failed.");

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Elite Daily | Transactions</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

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
.sidebar-link.active {
    background: linear-gradient(135deg,#e4c47d,#a98238);
    color: #111;
}

table {
    border-collapse: collapse;
    width: 100%;
    min-width: 750px;
}

th, td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

th {
    color: #d6b36a;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

tbody tr:hover {
    background: rgba(255,255,255,0.025);
}

</style>
</head>

<body class="text-white min-h-screen">

<div class="flex min-h-screen">

<!-- SIDEBAR -->

<aside class="hidden md:flex w-64 flex-col p-6 border-r border-white/10 shrink-0">

    <h1 class="text-2xl font-extrabold gold tracking-wide">
        ELITE<span class="text-white">.</span>
    </h1>

    <p class="text-gray-500 text-xs mt-1">
        PERSONAL MANAGEMENT
    </p>

    <nav class="mt-12 space-y-3">

        <a href="index.php" class="sidebar-link">
            ◈ Dashboard
        </a>

        <a href="index.php#expenses" class="sidebar-link">
            ◷ Daily Expenses
        </a>

        <a href="index.php#tasks" class="sidebar-link">
            ✓ To-Do Tasks
        </a>

        <a href="transection.php" class="sidebar-link active">
            ▤ Transactions
        </a>

    </nav>

    <div class="mt-auto glass p-4 rounded-2xl">

        <p class="text-xs text-gray-500">
            YOUR DAILY GOAL
        </p>

        <h2 class="text-xl font-bold gold mt-2">
            Stay Focused.
        </h2>

        <p class="text-xs text-gray-400 mt-2">
            Manage your money. Manage your time.
        </p>

    </div>

</aside>


<!-- MAIN CONTENT -->

<main class="flex-1 p-5 md:p-10 overflow-hidden">

    <!-- HEADER -->

    <div class="flex flex-wrap justify-between items-center gap-4 mb-10">

        <div>

            <p class="text-xs gold tracking-widest mb-2">
                PERSONAL FINANCE / MANAGEMENT
            </p>

            <h1 class="text-3xl md:text-4xl font-extrabold">
                Transactions<span class="gold">.</span>
            </h1>

            <p class="text-gray-500 text-sm mt-2">
                Track every income and expense.
            </p>

        </div>

        <button onclick="openForm()"
            class="gold-button px-6 py-3 rounded-xl">

            + Add Transaction

        </button>

    </div>


    <!-- SUMMARY CARDS -->

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="glass p-6 rounded-2xl">

            <p class="text-gray-400 text-sm">
                TOTAL INCOME
            </p>

            <h2 id="income"
                class="text-2xl font-bold text-green-400 mt-3">
                LKR 0.00
            </h2>

            <p class="text-xs text-gray-500 mt-2">
                Money received
            </p>

        </div>

        <div class="glass p-6 rounded-2xl">

            <p class="text-gray-400 text-sm">
                TOTAL EXPENSES
            </p>

            <h2 id="expense"
                class="text-2xl font-bold text-red-400 mt-3">
                LKR 0.00
            </h2>

            <p class="text-xs text-gray-500 mt-2">
                Money spent
            </p>

        </div>

        <div class="glass p-6 rounded-2xl">

            <p class="text-gray-400 text-sm">
                AVAILABLE BALANCE
            </p>

            <h2 id="balance"
                class="text-2xl font-bold gold mt-3">
                LKR 0.00
            </h2>

            <p class="text-xs text-gray-500 mt-2">
                Income minus expenses
            </p>

        </div>

    </div>


    <!-- ADD / EDIT FORM -->

    <div id="formBox"
        class="hidden glass rounded-2xl p-6 mb-8">

        <div class="flex justify-between items-center mb-6">

            <h2 id="formHeading"
                class="text-xl font-bold gold">
                Add New Transaction
            </h2>

            <button onclick="closeForm()"
                class="text-gray-400 hover:text-white text-xl">
                ✕
            </button>

        </div>

        <form id="transactionForm"
            class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <input type="hidden" id="id">

            <div>

                <label class="block text-sm text-gray-400 mb-2">
                    Transaction Title
                </label>

                <input id="title" type="text"
                    class="input-dark"
                    placeholder="Enter transaction title"
                    required>

            </div>

            <div>

                <label class="block text-sm text-gray-400 mb-2">
                    Amount (LKR)
                </label>

                <input id="amount" type="number"
                    min="0.01" step="0.01"
                    class="input-dark"
                    placeholder="0.00"
                    required>

            </div>

            <div>

                <label class="block text-sm text-gray-400 mb-2">
                    Transaction Type
                </label>

                <select id="type" class="input-dark">

                    <option value="expense">Expense</option>
                    <option value="income">Income</option>

                </select>

            </div>

            <div>

                <label class="block text-sm text-gray-400 mb-2">
                    Category
                </label>

                <select id="category" class="input-dark" required>

                    <option value="">Select Category</option>
                    <option value="Food">Food</option>
                    <option value="Transport">Transport</option>
                    <option value="Shopping">Shopping</option>
                    <option value="Salary">Salary</option>
                    <option value="Education">Education</option>
                    <option value="Bills">Bills</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Other">Other</option>

                </select>

            </div>

            <div>

                <label class="block text-sm text-gray-400 mb-2">
                    Transaction Date
                </label>

                <input id="date" type="date"
                    class="input-dark" required>

            </div>

            <div>

                <label class="block text-sm text-gray-400 mb-2">
                    Description
                </label>

                <input id="description" type="text"
                    class="input-dark"
                    placeholder="Optional description">

            </div>

            <div class="md:col-span-2 flex gap-3">

                <button type="submit" id="saveBtn"
                    class="gold-button px-7 py-3 rounded-xl">

                    Save Transaction

                </button>

                <button type="button" onclick="closeForm()"
                    class="bg-white/10 hover:bg-white/20 px-7 py-3 rounded-xl">

                    Cancel

                </button>

            </div>

        </form>

    </div>


    <!-- TRANSACTION TABLE -->

    <div class="glass rounded-2xl p-6">

        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">

            <div>

                <h2 class="text-xl font-bold">
                    Transaction History
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    View and manage all your transactions
                </p>

            </div>

            <div class="flex flex-wrap gap-3">

                <input id="search" type="search"
                    placeholder="Search transactions..."
                    class="input-dark md:w-64">

                <select id="filterType"
                    class="input-dark md:w-36">

                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>

                </select>

            </div>

        </div>

        <div class="overflow-x-auto">

            <table>

                <thead>

                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody id="tableBody">

                    <tr>
                        <td colspan="6"
                            class="text-center text-gray-500">
                            Loading transactions...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <p class="text-center text-gray-600 text-xs mt-8">
        ELITE DAILY · PERSONAL MANAGEMENT SYSTEM
    </p>

</main>

</div>


<!-- JAVASCRIPT -->

<script>

const API = window.location.pathname + "?api=1";

let transactions = [];

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 2500,
    background: "#171d27",
    color: "#fff"
});

function notify(message, icon = "success") {
    Toast.fire({icon, title: message});
}

function escapeHTML(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    })[char]);
}

function money(value) {
    return "LKR " + Number(value).toLocaleString("en-LK", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


// LOAD TRANSACTIONS

async function loadData() {

    const search = document.getElementById("search").value;
    const type = document.getElementById("filterType").value;

    try {

        const response = await fetch(
            API +
            "&search=" + encodeURIComponent(search) +
            "&type=" + encodeURIComponent(type)
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message);
        }

        transactions = result.data;

        displayData();
        updateSummary();

    } catch (error) {

        notify(error.message, "error");

    }

}


// DISPLAY TRANSACTIONS

function displayData() {

    const tbody = document.getElementById("tableBody");

    if (!transactions.length) {

        tbody.innerHTML = `
            <tr>
                <td colspan="6"
                    class="text-center text-gray-500 py-10">
                    No transactions found.
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = transactions.map(item => {

        const income = item.type === "income";

        return `
            <tr>

                <td class="font-semibold">
                    ${escapeHTML(item.title)}
                </td>

                <td class="text-gray-400">
                    ${escapeHTML(item.category)}
                </td>

                <td class="text-gray-400">
                    ${escapeHTML(item.transaction_date)}
                </td>

                <td>
                    <span class="${income
                        ? 'text-green-400 bg-green-400/10'
                        : 'text-red-400 bg-red-400/10'}
                        px-3 py-1 rounded-full text-xs">
                        ${income ? 'Income' : 'Expense'}
                    </span>
                </td>

                <td class="${income ? 'text-green-400' : 'text-red-400'}
                    font-semibold whitespace-nowrap">
                    ${income ? '+' : '-'} ${money(item.amount)}
                </td>

                <td class="whitespace-nowrap">

                    <button onclick="editData(${Number(item.id)})"
                        class="text-blue-400 hover:text-blue-300 mr-3">
                        Edit
                    </button>

                    <button onclick="deleteData(${Number(item.id)})"
                        class="text-red-400 hover:text-red-300">
                        Delete
                    </button>

                </td>

            </tr>
        `;

    }).join("");

}


// SUMMARY

function updateSummary() {

    let income = 0;
    let expense = 0;

    transactions.forEach(item => {

        if (item.type === "income") {
            income += Number(item.amount);
        } else {
            expense += Number(item.amount);
        }

    });

    document.getElementById("income").textContent = money(income);

    document.getElementById("expense").textContent = money(expense);

    document.getElementById("balance").textContent =
        money(income - expense);

}


// OPEN FORM

function openForm() {

    document.getElementById("transactionForm").reset();

    document.getElementById("id").value = "";

    document.getElementById("date").value =
        new Date().toLocaleDateString("en-CA");

    document.getElementById("formHeading").textContent =
        "Add New Transaction";

    document.getElementById("saveBtn").textContent =
        "Save Transaction";

    document.getElementById("formBox").classList.remove("hidden");

    document.getElementById("formBox").scrollIntoView({
        behavior: "smooth"
    });

}


// CLOSE FORM

function closeForm() {

    document.getElementById("formBox").classList.add("hidden");

    document.getElementById("transactionForm").reset();

}


// CREATE / UPDATE

document.getElementById("transactionForm")
.addEventListener("submit", async function(e) {

    e.preventDefault();

    const id = document.getElementById("id").value;

    const data = {

        action: id ? "update" : "create",
        id: id,
        title: document.getElementById("title").value.trim(),
        amount: document.getElementById("amount").value,
        type: document.getElementById("type").value,
        category: document.getElementById("category").value,
        transaction_date: document.getElementById("date").value,
        description: document.getElementById("description").value.trim()

    };

    const button = document.getElementById("saveBtn");

    button.disabled = true;
    button.textContent = "Processing...";

    try {

        const response = await fetch(API, {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify(data)

        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message);
        }

        closeForm();

        await loadData();

        notify(result.message);

    } catch (error) {

        notify(error.message, "error");

    } finally {

        button.disabled = false;

        button.textContent = id
            ? "Update Transaction"
            : "Save Transaction";

    }

});


// EDIT

function editData(id) {

    const item = transactions.find(
        t => Number(t.id) === Number(id)
    );

    if (!item) return;

    document.getElementById("id").value = item.id;
    document.getElementById("title").value = item.title;
    document.getElementById("amount").value = item.amount;
    document.getElementById("type").value = item.type;
    document.getElementById("category").value = item.category;
    document.getElementById("date").value = item.transaction_date;
    document.getElementById("description").value =
        item.description || "";

    document.getElementById("formHeading").textContent =
        "Update Transaction";

    document.getElementById("saveBtn").textContent =
        "Update Transaction";

    document.getElementById("formBox").classList.remove("hidden");

    document.getElementById("formBox").scrollIntoView({
        behavior: "smooth"
    });

}


// DELETE

async function deleteData(id) {

    const confirm = await Swal.fire({

        title: "Delete Transaction?",

        text: "This action cannot be undone.",

        icon: "warning",

        background: "#171d27",

        color: "#fff",

        showCancelButton: true,

        confirmButtonColor: "#a98238",

        cancelButtonColor: "#374151",

        confirmButtonText: "Yes, Delete"

    });

    if (!confirm.isConfirmed) return;

    try {

        const response = await fetch(API, {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                action: "delete",
                id: id
            })

        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message);
        }

        await loadData();

        notify(result.message);

    } catch (error) {

        notify(error.message, "error");

    }

}


// SEARCH AND FILTER

let searchTimer;

document.getElementById("search").addEventListener("input", function() {

    clearTimeout(searchTimer);

    searchTimer = setTimeout(loadData, 300);

});

document.getElementById("filterType").addEventListener("change", loadData);


// INITIAL LOAD

loadData();

</script>

</body>
</html>
