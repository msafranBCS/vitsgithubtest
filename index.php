

<?php
include 'config/db.php';
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
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;

            background: #f3f5fb;
            color: #1e293b;
        }

        .glass {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 5px 25px rgba(15, 23, 42, 0.04);
     background: #0b0e13;
            color: #fff;
        }

        .glass {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);

        }

        .gold {
            color: #6366f1;
        }

        .gold-bg {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }


        input, select {
            color: #334155;
            color-scheme: light;
        }

        input::placeholder {
            color: #94a3b8;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.10);
        }

        /* Sidebar */
        #sidebar {
            width: 260px;
            min-width: 260px;
            background: #ffffff;
            border-right: 1px solid #e8edf5;
            transition: width 0.3s ease, min-width 0.3s ease,
                        padding 0.3s ease, transform 0.3s ease;
            overflow: hidden;
        }

        #sidebar.collapsed {
            width: 0;
            min-width: 0;
            padding-left: 0;
            padding-right: 0;
            border-right: 0;
        }

        .sidebar-link {
            display: block;
            padding: 13px;
            border-radius: 12px;
            color: #64748b;
            transition: 0.2s;
            white-space: nowrap;
        }

        .sidebar-link:hover {
            background: #f1f5f9;
            color: #6366f1;
        }

        .sidebar-active {
            background: #eef2ff;
            color: #6366f1;
            font-weight: 700;
        }

        #menuBtn {
            background: white;
            border: 1px solid #e2e8f0;
            color: #334155;
            padding: 10px 13px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 20px;
            transition: 0.2s;
        }

        #menuBtn:hover {
            background: #eef2ff;
            color: #6366f1;
        }

        .form-input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
        }

        .form-input:focus {
            background: white;
        }

        @media (max-width: 767px) {
            #sidebar {
                position: fixed;
                z-index: 100;
                top: 0;
                left: 0;
                bottom: 0;
                width: 260px;
                min-width: 260px;
                box-shadow: 10px 0 30px rgba(0,0,0,0.08);
            }

            #sidebar.collapsed {
                width: 0;
                min-width: 0;
            }

        .gold-border {
            border: 1px solid rgba(214, 179, 106, 0.35);
        }

        .gold-button {
            background: linear-gradient(135deg, #e4c47d, #a98238);
            color: #111;
            transition: all 0.3s ease;
        }

        .gold-button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 5px 18px rgba(214, 179, 106, 0.2);
        }

        .input-dark {
            width: 100%;
            margin-top: 8px;
            padding: 13px 15px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.10);
            color: #fff;
            outline: none;
            transition: 0.3s;
        }

        .input-dark:focus {
            border-color: #d6b36a;
            box-shadow: 0 0 0 2px rgba(214, 179, 106, 0.08);
        }

        .input-dark::placeholder {
            color: #6b7280;
        }

        select.input-dark {
            background-color: #171b22;
        }

        select.input-dark option {
            background: #171b22;
            color: white;
        }

        input[type="date"],
        input[type="number"] {
            color-scheme: dark;

        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(214, 179, 106, 0.3);
        }

        .data-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
.sidebar-link.dash{
    background: linear-gradient(135deg,#e4c47d,#a98238);
    color: #111;
}

table {
    border-collapse: collapse;
    width: 100%;
    min-width: 750px;
}


        .data-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .task-checkbox {
            accent-color: #d6b36a;
            width: 17px;
            height: 17px;
            cursor: pointer;
        }

        .task-completed {
            text-decoration: line-through;
            color: #6b7280;
        }

        .priority {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 20px;
            background: rgba(214, 179, 106, 0.12);
            color: #d6b36a;
        }

        .priority-high {
            color: #f87171;
            background: rgba(248, 113, 113, 0.10);
        }

        .priority-medium {
            color: #fbbf24;
            background: rgba(251, 191, 36, 0.10);
        }

        .priority-low {
            color: #4ade80;
            background: rgba(74, 222, 128, 0.10);
        }

        @media (max-width: 640px) {
            .dashboard-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

<body class="min-h-screen">

<div class="flex min-h-screen">

        <!-- Sidebar -->
        <?php include 'include/sidebar.php'; ?>


    <!-- Main Content -->
    <main class="flex-1 min-w-0 p-5 md:p-10">

        <!-- Header -->
        <header class="flex items-center gap-4 mb-10">

            <button id="menuBtn" type="button"
                    aria-label="Toggle sidebar"
                    aria-expanded="true">
                ☰
            </button>

            <div class="flex-1">
                <p class="text-slate-400 text-sm">
                    SATURDAY, NOVEMBER 14
                </p>

                <h2 class="text-2xl md:text-3xl font-bold mt-2">
                    Good Afternoon 👋
                </h2>

                <p class="text-slate-500 text-sm mt-1">
                    Welcome back. Here's your daily overview.
                </p>
            </div>

            <div class="hidden sm:block text-right">
                <p class="gold text-sm font-semibold">
                    PREMIUM EDITION
                </p>

                <p class="text-slate-400 text-xs mt-1">
                    PERSONAL DASHBOARD
                </p>
            </div>

        </header>

        <!-- Summary Cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="glass p-6 rounded-2xl">
                <p class="text-slate-500 text-sm">Today's Expenses</p>
                <h2 class="text-3xl font-bold mt-3">Rs. 2,450</h2>
                <p class="text-red-500 text-xs mt-3">↑ Daily spending</p>
            </div>

            <div class="glass p-6 rounded-2xl">
                <p class="text-slate-500 text-sm">Monthly Expenses</p>
                <h2 class="text-3xl font-bold mt-3">Rs. 35,800</h2>
                <p class="text-slate-400 text-xs mt-3">November 2026</p>
            </div>

            <div class="glass p-6 rounded-2xl">
                <p class="text-slate-500 text-sm">Remaining Budget</p>
                <h2 class="text-3xl font-bold mt-3 gold">Rs. 14,200</h2>
                <p class="text-green-600 text-xs mt-3">Budget available</p>
            </div>

            <div class="glass p-6 rounded-2xl">
                <p class="text-slate-500 text-sm">Tasks Completed</p>
                <h2 class="text-3xl font-bold mt-3">05 / 08</h2>
                <p class="text-green-600 text-xs mt-3">62.5% completed</p>
            </div>

        </section>

        <!-- Forms -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Expense Form -->
            <div id="expenses" class="glass p-6 rounded-2xl">

                <div class="flex justify-between mb-6">
                    <h3 class="text-lg font-bold">Add Daily Expense</h3>
                    <span class="gold">✦ EXPENSE</span>
                </div>

                <form id="expenseForm" class="space-y-4">

                    <div>
                        <label class="text-xs text-slate-500">Expense Title</label>

                        <input id="expenseTitle" required
                        placeholder="e.g. Lunch, Transport"
                        class="form-input w-full mt-2 p-3 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <label class="text-xs text-slate-500">Amount (LKR)</label>

                            <input id="expenseAmount" type="number"
                            min="1" required placeholder="0.00"
                            class="form-input w-full mt-2 p-3 rounded-xl">
                        </div>

                        <div>
                            <label class="text-xs text-slate-500">Category</label>

                            <select id="expenseCategory"
                            class="form-input w-full mt-2 p-3 rounded-xl">

                                <option>Food</option>
                                <option>Transport</option>
                                <option>Shopping</option>
                                <option>Bills</option>
                                <option>Education</option>
                                <option>Other</option>

                            </select>
                        </div>

                    </div>

                    <button class="gold-bg w-full p-3 rounded-xl font-bold hover:opacity-90">
                        + Add Expense
                    </button>

                </form>
            </div>

            <!-- Task Form -->
            <div id="tasks" class="glass p-6 rounded-2xl">

                <div class="flex justify-between mb-6">
                    <h3 class="text-lg font-bold">Create New Task</h3>
                    <span class="gold">✦ PRODUCTIVITY</span>
                </div>

                <form id="taskForm" class="space-y-4">

                    <div>
                        <label class="text-xs text-slate-500">Task Name</label>

                        <input id="taskTitle" required
                        placeholder="What needs to be done?"
                        class="form-input w-full mt-2 p-3 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <label class="text-xs text-slate-500">Due Date</label>

                            <input id="taskDate" type="date" required
                            class="form-input w-full mt-2 p-3 rounded-xl">
                        </div>

                        <div>
                            <label class="text-xs text-slate-500">Priority</label>

                            <select id="taskPriority"
                            class="form-input w-full mt-2 p-3 rounded-xl">

                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>

                            </select>
                        </div>

                    </div>

                    <button class="gold-bg w-full p-3 rounded-xl font-bold hover:opacity-90">
                        + Create Task
                    </button>

                </form>
            </div>

        </section>

        <!-- Recent Data -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Expenses -->
            <div id="transactions" class="glass p-6 rounded-2xl">

                <h3 class="font-bold text-lg mb-6">Recent Expenses</h3>

                <div id="expenseList" class="space-y-4">
                    <p class="text-slate-400 text-sm">
                        Your expenses will appear here.
                    </p>
                </div>

            </div>

            <!-- Tasks -->
            <div class="glass p-6 rounded-2xl">

                <h3 class="font-bold text-lg mb-6">Today's Tasks</h3>

                <div id="taskList" class="space-y-4">
                    <p class="text-slate-400 text-sm">
                        Your tasks will appear here.
                    </p>
                </div>

            </div>

        </section>

        <footer class="text-center text-slate-400 text-xs mt-12 pb-5">
            © 2026 ELITE DAILY. Crafted for better living.
        </footer>

    </main>
</div>

<script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');

    menuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');

        const isOpen = !sidebar.classList.contains('collapsed');
        menuBtn.setAttribute('aria-expanded', isOpen);
    });

    // Expense and Task Forms
    const expenseForm = document.getElementById('expenseForm');
    const taskForm = document.getElementById('taskForm');

    expenseForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const title = document.getElementById('expenseTitle').value;
        const amount = Number(document.getElementById('expenseAmount').value);
        const category = document.getElementById('expenseCategory').value;

        const item = document.createElement('div');
        item.className = 'flex justify-between items-center border-b border-slate-100 pb-3';

        const details = document.createElement('div');

        const name = document.createElement('p');
        name.className = 'font-semibold text-sm';
        name.textContent = title;

        const type = document.createElement('p');
        type.className = 'text-xs text-slate-400 mt-1';
        type.textContent = category;

        details.append(name, type);

        const price = document.createElement('span');
        price.className = 'gold font-bold text-sm';
        price.textContent = 'Rs. ' + amount.toLocaleString('en-LK');

        item.append(details, price);
=======
        <!-- Main Content -->
        <main class="flex-1 min-w-0 p-5 md:p-10">

            <!-- Header -->
            <header class="dashboard-header flex justify-between items-center mb-10">

                <div>
                    <p class="text-gray-500 text-xs font-semibold tracking-widest">
                        <?= strtoupper(date('l, F d, Y')) ?>
                    </p>

                    <h2 class="text-2xl md:text-3xl font-bold mt-2">
                        Good Afternoon 👋
                    </h2>

                    <p class="text-gray-400 text-sm mt-2">
                        Welcome back. Here's your daily overview.
                    </p>
                </div>

                <div class="hidden sm:block text-right">
                    <p class="gold text-sm font-bold tracking-widest">
                        PREMIUM EDITION
                    </p>
                    <p class="text-gray-500 text-xs mt-2 tracking-wider">
                        PERSONAL DASHBOARD
                    </p>
                </div>

            </header>

            <!-- Summary Cards -->
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

                <div class="glass stat-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-400 text-sm">Today's Expenses</p>
                        <span class="gold text-xl">↗</span>
                    </div>

                    <h2 class="text-3xl font-bold mt-4">
                        Rs. 2,450
                    </h2>

                    <p class="text-red-400 text-xs mt-3">
                        Daily spending
                    </p>
                </div>

                <div class="glass stat-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-400 text-sm">Monthly Expenses</p>
                        <span class="gold text-xl">▣</span>
                    </div>

                    <h2 class="text-3xl font-bold mt-4">
                        Rs. 35,800
                    </h2>

                    <p class="text-gray-500 text-xs mt-3">
                        <?= date('F Y') ?>
                    </p>
                </div>

                <div class="glass stat-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-400 text-sm">Remaining Budget</p>
                        <span class="gold text-xl">◈</span>
                    </div>

                    <h2 class="text-3xl font-bold mt-4 gold">
                        Rs. 14,200
                    </h2>

                    <p class="text-green-400 text-xs mt-3">
                        Budget available
                    </p>
                </div>

                <div class="glass stat-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-400 text-sm">Tasks Completed</p>
                        <span class="gold text-xl">✓</span>
                    </div>

                    <h2 class="text-3xl font-bold mt-4" id="taskCount">
                        00 / 00
                    </h2>

                    <p class="text-green-400 text-xs mt-3" id="taskPercentage">
                        0% completed
                    </p>
                </div>

            </section>

            <!-- Forms -->
            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

                <!-- Expense Form -->
                <div id="expenses" class="glass p-6 rounded-2xl">

                    <div class="flex justify-between items-center mb-7">
                        <div>
                            <h3 class="section-title">Add Daily Expense</h3>
                            <p class="text-gray-500 text-xs mt-2">
                                Record your daily spending
                            </p>
                        </div>

                        <span class="gold text-xs font-bold tracking-widest">
                            ✦ EXPENSE
                        </span>
                    </div>

                    <form id="expenseForm" class="space-y-5">

                        <div>
                            <label class="text-xs text-gray-400 font-medium">
                                Expense Title
                            </label>

                            <input
                                id="expenseTitle"
                                type="text"
                                required
                                placeholder="e.g. Lunch, Transport"
                                class="input-dark">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label class="text-xs text-gray-400 font-medium">
                                    Amount (LKR)
                                </label>

                                <input
                                    id="expenseAmount"
                                    type="number"
                                    min="1"
                                    step="0.01"
                                    required
                                    placeholder="0.00"
                                    class="input-dark">
                            </div>

                            <div>
                                <label class="text-xs text-gray-400 font-medium">
                                    Category
                                </label>

                                <select id="expenseCategory" class="input-dark">
                                    <option value="Food">Food</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Shopping">Shopping</option>
                                    <option value="Bills">Bills</option>
                                    <option value="Education">Education</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                        </div>

                        <button
                            type="submit"
                            class="gold-button w-full p-3 rounded-xl font-bold">
                            + Add Expense
                        </button>

                    </form>
                </div>

                <!-- Task Form -->
                <div id="tasks" class="glass p-6 rounded-2xl">

                    <div class="flex justify-between items-center mb-7">
                        <div>
                            <h3 class="section-title">Create New Task</h3>
                            <p class="text-gray-500 text-xs mt-2">
                                Organize your daily activities
                            </p>
                        </div>

                        <span class="gold text-xs font-bold tracking-widest">
                            ✦ PRODUCTIVITY
                        </span>
                    </div>

                    <form id="taskForm" class="space-y-5">

                        <div>
                            <label class="text-xs text-gray-400 font-medium">
                                Task Name
                            </label>

                            <input
                                id="taskTitle"
                                type="text"
                                required
                                placeholder="What needs to be done?"
                                class="input-dark">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label class="text-xs text-gray-400 font-medium">
                                    Due Date
                                </label>

                                <input
                                    id="taskDate"
                                    type="date"
                                    required
                                    class="input-dark">
                            </div>

                            <div>
                                <label class="text-xs text-gray-400 font-medium">
                                    Priority
                                </label>

                                <select id="taskPriority" class="input-dark">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>

                        </div>

                        <button
                            type="submit"
                            class="gold-button w-full p-3 rounded-xl font-bold">
                            + Create Task
                        </button>

                    </form>
                </div>

            </section>

            <!-- Recent Data -->
            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                <!-- Expenses -->
                <div id="transactions" class="glass p-6 rounded-2xl">

                    <div class="flex justify-between items-center mb-7">
                        <div>
                            <h3 class="section-title">Recent Expenses</h3>
                            <p class="text-gray-500 text-xs mt-2">
                                Your latest expense records
                            </p>
                        </div>

                        <span class="gold text-xl">↗</span>
                    </div>

                    <div id="expenseList" class="space-y-4">
                        <p class="text-gray-500 text-sm">
                            Your expenses will appear here.
                        </p>
                    </div>

                </div>

                <!-- Tasks -->
                <div class="glass p-6 rounded-2xl">

                    <div class="flex justify-between items-center mb-7">
                        <div>
                            <h3 class="section-title">Today's Tasks</h3>
                            <p class="text-gray-500 text-xs mt-2">
                                Track your daily productivity
                            </p>
                        </div>

                        <span class="gold text-xl">✓</span>
                    </div>

                    <div id="taskList" class="space-y-4">
                        <p class="text-gray-500 text-sm">
                            Your tasks will appear here.
                        </p>
                    </div>

                </div>

            </section>

            <footer class="text-center text-gray-600 text-xs mt-12 pb-5">
                © <?= date('Y') ?> ELITE DAILY. Crafted for better living.
            </footer>


        const list = document.getElementById('expenseList');


        if (list.querySelector('p')) {
            list.innerHTML = '';
        }

        list.prepend(item);

        expenseForm.reset();
        alert('Expense added successfully!');
    });

    taskForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const title = document.getElementById('taskTitle').value;
        const priority = document.getElementById('taskPriority').value;

        const item = document.createElement('label');
        item.className = 'flex justify-between items-center border-b border-slate-100 pb-3 gap-3';
=======
    <!-- JavaScript -->
    <script>
        const expenseForm = document.getElementById('expenseForm');
        const taskForm = document.getElementById('taskForm');

        let tasks = [];

        // Expense form
        expenseForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = document.getElementById('expenseTitle').value.trim();
            const amount = Number(document.getElementById('expenseAmount').value);
            const category = document.getElementById('expenseCategory').value;

            if (!title || amount <= 0 || !Number.isFinite(amount)) {
                alert('Please enter valid expense details.');
                return;
            }

            const item = document.createElement('div');
            item.className = 'data-item';

            const details = document.createElement('div');

            const name = document.createElement('p');
            name.className = 'font-semibold text-sm';
            name.textContent = title;

            const type = document.createElement('p');
            type.className = 'text-xs text-gray-500 mt-2';
            type.textContent = category;

            details.append(name, type);

            const price = document.createElement('span');
            price.className = 'gold font-bold text-sm whitespace-nowrap';
            price.textContent = 'Rs. ' + amount.toLocaleString('en-LK', {
                minimumFractionDigits: 2
            });


        const left = document.createElement('div');
        left.className = 'flex items-center gap-3';


        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'accent-indigo-500 w-4 h-4';

            const list = document.getElementById('expenseList');

            if (list.querySelector('p.text-gray-500')) {
                list.innerHTML = '';
            }

            list.prepend(item);


        const name = document.createElement('span');
        name.className = 'text-sm';
        name.textContent = title;

        checkbox.addEventListener('change', () => {
            name.classList.toggle('line-through', checkbox.checked);
            name.classList.toggle('text-slate-400', checkbox.checked);
        });


        left.append(checkbox, name);

        const badge = document.createElement('span');
        badge.className = 'text-xs gold';
        badge.textContent = priority;

        item.append(left, badge);

        const list = document.getElementById('taskList');

        if (list.querySelector('p')) {
            list.innerHTML = '';
        }

        list.prepend(item);

        taskForm.reset();
        alert('Task created successfully!');
    });
</script>
=======
        // Task form
        taskForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = document.getElementById('taskTitle').value.trim();
            const dueDate = document.getElementById('taskDate').value;
            const priority = document.getElementById('taskPriority').value;

            if (!title || !dueDate) {
                alert('Please enter task details.');
                return;
            }

            const task = {
                id: Date.now(),
                title,
                dueDate,
                priority,
                completed: false
            };

            tasks.unshift(task);

            taskForm.reset();
            displayTasks();
        });

        // Display tasks
        function displayTasks() {
            const list = document.getElementById('taskList');
            list.innerHTML = '';

            if (tasks.length === 0) {
                list.innerHTML = '<p class="text-gray-500 text-sm">No tasks available.</p>';
                updateTaskSummary();
                return;
            }

            tasks.forEach(task => {
                const item = document.createElement('div');
                item.className = 'data-item';

                const left = document.createElement('div');
                left.className = 'flex items-center gap-3 min-w-0';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = task.completed;
                checkbox.className = 'task-checkbox shrink-0';

                const details = document.createElement('div');

                const name = document.createElement('p');
                name.className = 'text-sm font-medium';
                name.textContent = task.title;

                if (task.completed) {
                    name.classList.add('task-completed');
                }

                const date = document.createElement('p');
                date.className = 'text-xs text-gray-500 mt-1';
                date.textContent = 'Due: ' + task.dueDate;

                details.append(name, date);
                left.append(checkbox, details);

                const badge = document.createElement('span');
                badge.className = 'priority shrink-0';

                if (task.priority === 'High') {
                    badge.classList.add('priority-high');
                } else if (task.priority === 'Medium') {
                    badge.classList.add('priority-medium');
                } else {
                    badge.classList.add('priority-low');
                }

                badge.textContent = task.priority;

                checkbox.addEventListener('change', () => {
                    task.completed = checkbox.checked;
                    displayTasks();
                });

                item.append(left, badge);
                list.append(item);
            });

            updateTaskSummary();
        }

        // Update task summary
        function updateTaskSummary() {
            const total = tasks.length;
            const completed = tasks.filter(task => task.completed).length;
            const percentage = total ? Math.round((completed / total) * 100) : 0;

            document.getElementById('taskCount').textContent =
                String(completed).padStart(2, '0') + ' / ' +
                String(total).padStart(2, '0');

            document.getElementById('taskPercentage').textContent =
                percentage + '% completed';
        }
    </script>


</body>
</html>
