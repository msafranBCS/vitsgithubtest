
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
        }
    </style>
</head>

<body class="min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="flex flex-col p-6">

        <h1 class="text-2xl font-extrabold gold tracking-wide">
            ELITE<span class="text-slate-800">.</span>
        </h1>

        <p class="text-slate-400 text-xs mt-1">
            PERSONAL MANAGEMENT
        </p>

        <nav class="mt-12 space-y-3">

            <a href="#" class="sidebar-link sidebar-active">
                ◈ Dashboard
            </a>

            <a href="#expenses" class="sidebar-link">
                ◷ Daily Expenses
            </a>

            <a href="#tasks" class="sidebar-link">
                ✓ To-Do Tasks
            </a>

            <a href="#transactions" class="sidebar-link">
                ▤ Transactions
            </a>

        </nav>

        <div class="mt-auto glass p-4 rounded-2xl">
            <p class="text-xs text-slate-400">YOUR DAILY GOAL</p>

            <h2 class="text-xl font-bold gold mt-2">
                Stay Focused.
            </h2>

            <p class="text-xs text-slate-500 mt-2">
                Manage your money. Manage your time.
            </p>
        </div>

    </aside>

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

        const left = document.createElement('div');
        left.className = 'flex items-center gap-3';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'accent-indigo-500 w-4 h-4';

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

</body>
</html>
