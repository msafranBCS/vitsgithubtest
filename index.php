<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Daily | Expense & Tasks</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font -->
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

        input,
        select {
            color-scheme: dark;
        }
    </style>
</head>

<body class="text-white min-h-screen">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="hidden md:flex w-64 flex-col p-6 border-r border-white/10">
            <h1 class="text-2xl font-extrabold gold tracking-wide">
                ELITE<span class="text-white">.</span>
            </h1>
            <p class="text-gray-500 text-xs mt-1">PERSONAL MANAGEMENT</p>

            <nav class="mt-12 space-y-3">
                <a href="#" class="block p-3 rounded-xl gold-bg text-black font-semibold">
                    ◈ Dashboard
                </a>
                <a href="expenses.php" class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                    ◷ Daily Expenses
                </a>
                <a href="#tasks" class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                    ✓ To-Do Tasks
                </a>
                <a href="#transactions" class="block p-3 rounded-xl text-gray-400 hover:bg-white/5">
                    ▤ Transactions
                </a>
            </nav>

            <div class="mt-auto glass p-4 rounded-2xl">
                <p class="text-xs text-gray-500">YOUR DAILY GOAL</p>
                <h2 class="text-xl font-bold gold mt-2">Stay Focused.</h2>
                <p class="text-xs text-gray-400 mt-2">
                    Manage your money. Manage your time.
                </p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-5 md:p-10">

            <!-- Header -->
            <header class="flex justify-between items-center mb-10">
                <div>
                    <p class="text-gray-500 text-sm">SATURDAY, NOVEMBER 14</p>
                    <h2 class="text-2xl md:text-3xl font-bold mt-2">
                        Good Afternoon 👋
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">
                        Welcome back. Here's your daily overview.
                    </p>
                </div>

                <div class="hidden sm:block text-right">
                    <p class="gold text-sm font-semibold">PREMIUM EDITION</p>
                    <p class="text-gray-500 text-xs mt-1">PERSONAL DASHBOARD</p>
                </div>
            </header>

            <!-- Summary Cards -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <div class="glass p-6 rounded-2xl">
                    <p class="text-gray-400 text-sm">Today's Expenses</p>
                    <h2 class="text-3xl font-bold mt-3">Rs. 2,450</h2>
                    <p class="text-red-400 text-xs mt-3">↑ Daily spending</p>
                </div>

                <div class="glass p-6 rounded-2xl">
                    <p class="text-gray-400 text-sm">Monthly Expenses</p>
                    <h2 class="text-3xl font-bold mt-3">Rs. 35,800</h2>
                    <p class="text-gray-500 text-xs mt-3">November 2026</p>
                </div>

                <div class="glass p-6 rounded-2xl">
                    <p class="text-gray-400 text-sm">Remaining Budget</p>
                    <h2 class="text-3xl font-bold mt-3 gold">Rs. 14,200</h2>
                    <p class="text-green-400 text-xs mt-3">Budget available</p>
                </div>

                <div class="glass p-6 rounded-2xl">
                    <p class="text-gray-400 text-sm">Tasks Completed</p>
                    <h2 class="text-3xl font-bold mt-3">05 / 08</h2>
                    <p class="text-green-400 text-xs mt-3">62.5% completed</p>
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
                            <label class="text-xs text-gray-400">Expense Title</label>
                            <input id="expenseTitle" required placeholder="e.g. Lunch, Transport"
                                class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none focus:border-yellow-600">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-400">Amount (LKR)</label>
                                <input id="expenseAmount" type="number" min="1" required placeholder="0.00"
                                    class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none">
                            </div>

                            <div>
                                <label class="text-xs text-gray-400">Category</label>
                                <select id="expenseCategory" class="w-full mt-2 p-3 rounded-xl bg-[#171b22] border border-white/10">
                                    <option>Food</option>
                                    <option>Transport</option>
                                    <option>Shopping</option>
                                    <option>Bills</option>
                                    <option>Education</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>

                        <button class="gold-bg text-black w-full p-3 rounded-xl font-bold hover:opacity-90">
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
                            <label class="text-xs text-gray-400">Task Name</label>
                            <input id="taskTitle" required placeholder="What needs to be done?"
                                class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10 outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-400">Due Date</label>
                                <input id="taskDate" type="date" required
                                    class="w-full mt-2 p-3 rounded-xl bg-white/5 border border-white/10">
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Priority</label>
                                <select id="taskPriority" class="w-full mt-2 p-3 rounded-xl bg-[#171b22] border border-white/10">
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

            <!-- Recent Data -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Expenses -->
                <div id="transactions" class="glass p-6 rounded-2xl">
                    <h3 class="font-bold text-lg mb-6">Recent Expenses</h3>
                    <div id="expenseList" class="space-y-4">
                        <p class="text-gray-500 text-sm">Your expenses will appear here.</p>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="glass p-6 rounded-2xl">
                    <h3 class="font-bold text-lg mb-6">Today's Tasks</h3>
                    <div id="taskList" class="space-y-4">
                        <p class="text-gray-500 text-sm">Your tasks will appear here.</p>
                    </div>
                </div>
            </section>

            <footer class="text-center text-gray-600 text-xs mt-12 pb-5">
                © 2026 ELITE DAILY. Crafted for better living.
            </footer>

        </main>
    </div>

    <!-- JavaScript: Frontend UI demo only -->
    <script>
        const expenseForm = document.getElementById('expenseForm');
        const taskForm = document.getElementById('taskForm');

        expenseForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = document.getElementById('expenseTitle').value;
            const amount = Number(document.getElementById('expenseAmount').value);
            const category = document.getElementById('expenseCategory').value;

            const item = document.createElement('div');
            item.className = 'flex justify-between items-center border-b border-white/10 pb-3';

            const details = document.createElement('div');
            const name = document.createElement('p');
            name.className = 'font-semibold text-sm';
            name.textContent = title;
            const type = document.createElement('p');
            type.className = 'text-xs text-gray-500 mt-1';
            type.textContent = category;
            details.append(name, type);

            const price = document.createElement('span');
            price.className = 'gold font-bold text-sm';
            price.textContent = 'Rs. ' + amount.toLocaleString('en-LK');

            item.append(details, price);

            const list = document.getElementById('expenseList');
            if (list.querySelector('p.text-gray-500')) list.innerHTML = '';
            list.prepend(item);

            expenseForm.reset();
            alert('Expense added successfully!');
        });

        taskForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = document.getElementById('taskTitle').value;
            const priority = document.getElementById('taskPriority').value;

            const item = document.createElement('label');
            item.className = 'flex justify-between items-center border-b border-white/10 pb-3 gap-3';

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

            left.append(checkbox, name);

            const badge = document.createElement('span');
            badge.className = 'text-xs gold';
            badge.textContent = priority;

            item.append(left, badge);

            const list = document.getElementById('taskList');
            if (list.querySelector('p.text-gray-500')) list.innerHTML = '';
            list.prepend(item);

            taskForm.reset();
            alert('Task created successfully!');
        });
    </script>

</body>

</html>