<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decretal Award Payment Vouchers</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
<div class="max-w-7xl w-full bg-white shadow-xl rounded-2xl overflow-hidden md:p-10 p-6">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-2 text-center">
        <i class="fa-solid fa-file-invoice-dollar text-indigo-600"></i>
        Payment Vouchers
    </h1>
    <p class="text-center text-gray-500 mb-8">
        Manage payment vouchers for decretal awards against the government.
    </p>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Voucher Creation Form -->
        <div class="bg-gray-50 p-6 rounded-xl shadow-inner">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Create New Voucher</h2>
            <p class="text-gray-500 mb-4">Fill in the details below to generate a new payment voucher for a client.</p>
            <form id="voucher-form" class="space-y-4">
                <div>
                    <label for="case-number" class="block text-sm font-medium text-gray-700">Case Number</label>
                    <input type="text" id="case-number" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="client-name" class="block text-sm font-medium text-gray-700">Client Name</label>
                    <input type="text" id="client-name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="award-amount" class="block text-sm font-medium text-gray-700">Decretal Award Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Ksh</span>
                        </div>
                        <input type="number" id="award-amount" required
                               class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fa-solid fa-plus-circle mr-2"></i>
                    Generate Voucher
                </button>
            </form>
        </div>

        <!-- Voucher List Display -->
        <div class="md:p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Voucher List</h2>
            <p class="text-gray-500 mb-4">Click the menu to update a voucher's status.</p>
            <div id="voucher-list" class="space-y-4">
                <!-- Vouchers will be dynamically added here -->
                <div id="no-vouchers-message" class="text-center text-gray-400 p-8 hidden">
                    <i class="fa-solid fa-receipt fa-3x mb-4"></i>
                    <p>No vouchers have been generated yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Box -->
<div id="message-box" class="fixed bottom-4 right-4 z-50"></div>

<script>
    const voucherForm = document.getElementById('voucher-form');
    const voucherList = document.getElementById('voucher-list');
    const noVouchersMessage = document.getElementById('no-vouchers-message');
    const messageBox = document.getElementById('message-box');

    // Sample data to pre-populate the list
    let vouchers = [
        {
            id: 1001,
            caseNumber: 'HCCC 253/2023',
            clientName: 'Jane Mwangi',
            amount: 750000,
            status: 'Pending',
            dateGenerated: '2025-08-01',
            approvedBy: null,
        },
        {
            id: 1002,
            caseNumber: 'HCCC 112/2022',
            clientName: 'Peter Ochieng',
            amount: 125000,
            status: 'Approved',
            dateGenerated: '2025-07-28',
            approvedBy: 'Financial Controller',
        },
        {
            id: 1003,
            caseNumber: 'HCCC 089/2021',
            clientName: 'Sarah Kimani',
            amount: 320000,
            status: 'Paid',
            dateGenerated: '2025-07-15',
            approvedBy: 'Financial Controller',
        }
    ];

    // Function to show a message box instead of an alert
    function showMessage(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `p-4 rounded-xl shadow-lg mb-2 text-sm text-white transition-opacity duration-300 ease-in-out transform scale-95 opacity-0`;

        let bgColor = '';
        let icon = '';
        if (type === 'success') {
            bgColor = 'bg-green-500';
            icon = '<i class="fa-solid fa-check-circle mr-2"></i>';
        } else if (type === 'error') {
            bgColor = 'bg-red-500';
            icon = '<i class="fa-solid fa-times-circle mr-2"></i>';
        } else {
            bgColor = 'bg-gray-700';
            icon = '<i class="fa-solid fa-info-circle mr-2"></i>';
        }
        alert.classList.add(bgColor);
        alert.innerHTML = `${icon}${message}`;

        messageBox.appendChild(alert);

        setTimeout(() => {
            alert.classList.remove('opacity-0', 'scale-95');
            alert.classList.add('opacity-100', 'scale-100');
        }, 10);

        setTimeout(() => {
            alert.classList.remove('opacity-100', 'scale-100');
            alert.classList.add('opacity-0', 'scale-95');
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }

    function renderVouchers() {
        voucherList.innerHTML = '';
        if (vouchers.length === 0) {
            noVouchersMessage.classList.remove('hidden');
            voucherList.appendChild(noVouchersMessage);
        } else {
            noVouchersMessage.classList.add('hidden');
            vouchers.forEach(voucher => {
                const statusColors = {
                    'Pending': 'bg-yellow-100 text-yellow-800',
                    'Approved': 'bg-blue-100 text-blue-800',
                    'Paid': 'bg-green-100 text-green-800',
                };
                const statusText = statusColors[voucher.status];

                const voucherCard = document.createElement('div');
                voucherCard.className = 'bg-white p-6 rounded-xl shadow border border-gray-200 flex flex-col md:flex-row items-center justify-between transition-transform transform hover:scale-105';
                voucherCard.innerHTML = `
                        <div class="flex-grow mb-4 md:mb-0">
                            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                                <i class="fa-solid fa-file-alt mr-2 text-indigo-500"></i>
                                Voucher ID: <span class="ml-1 text-indigo-600">${voucher.id}</span>
                            </h3>
                            <p class="text-gray-600 mt-1">Case No: ${voucher.caseNumber}</p>
                            <p class="text-gray-600">Client: ${voucher.clientName}</p>
                            <p class="text-lg font-bold text-gray-800 mt-2">Ksh ${voucher.amount.toLocaleString()}</p>
                            ${voucher.dateGenerated ? `<p class="text-sm text-gray-500 mt-1">Generated: ${voucher.dateGenerated}</p>` : ''}
                            ${voucher.approvedBy ? `<p class="text-sm text-gray-500 mt-1">Approved by: ${voucher.approvedBy}</p>` : ''}
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold ${statusText}">${voucher.status}</span>
                            <div class="relative inline-block text-left">
                                <button type="button" class="inline-flex justify-center w-full rounded-full border border-gray-300 shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="menu-button-${voucher.id}" aria-expanded="true" aria-haspopup="true">
                                    <i class="fa-solid fa-ellipsis-h"></i>
                                </button>
                                <div class="hidden origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-${voucher.id}" tabindex="-1" id="dropdown-${voucher.id}">
                                    <div class="py-1" role="none">
                                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" onclick="updateStatus(${voucher.id}, 'Approved')">
                                            <i class="fa-solid fa-check-circle mr-2 text-blue-500"></i>
                                            Approve
                                        </button>
                                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" onclick="updateStatus(${voucher.id}, 'Paid')">
                                            <i class="fa-solid fa-wallet mr-2 text-green-500"></i>
                                            Mark as Paid
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                // Toggle dropdown menu
                const menuButton = voucherCard.querySelector(`#menu-button-${voucher.id}`);
                const dropdown = voucherCard.querySelector(`#dropdown-${voucher.id}`);
                menuButton.addEventListener('click', () => {
                    dropdown.classList.toggle('hidden');
                });

                voucherList.appendChild(voucherCard);
            });
        }
    }

    function updateStatus(id, newStatus) {
        const voucherIndex = vouchers.findIndex(v => v.id === id);
        if (voucherIndex !== -1) {
            vouchers[voucherIndex].status = newStatus;
            // Add the approved by field if the status is approved
            if (newStatus === 'Approved' && !vouchers[voucherIndex].approvedBy) {
                vouchers[voucherIndex].approvedBy = 'Financial Controller';
            } else if (newStatus === 'Paid' && !vouchers[voucherIndex].approvedBy) {
                // Assume approval before payment, but add a default if not present
                vouchers[voucherIndex].approvedBy = 'Financial Controller';
            }
            showMessage(`Voucher for Case No. ${vouchers[voucherIndex].caseNumber} status updated to ${newStatus}.`, 'info');
            renderVouchers();
        }
    }

    voucherForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const caseNumber = document.getElementById('case-number').value;
        const clientName = document.getElementById('client-name').value;
        const awardAmount = document.getElementById('award-amount').value;

        const newVoucher = {
            id: vouchers.length > 0 ? Math.max(...vouchers.map(v => v.id)) + 1 : 1001,
            caseNumber,
            clientName,
            amount: parseFloat(awardAmount),
            status: 'Pending',
            dateGenerated: new Date().toISOString().slice(0, 10), // Current date
            approvedBy: null,
        };

        vouchers.unshift(newVoucher); // Add to the beginning of the list
        voucherForm.reset();
        showMessage(`Voucher for Case No. ${caseNumber} generated successfully!`);
        renderVouchers();
    });

    // Initial render
    document.addEventListener('DOMContentLoaded', renderVouchers);
</script>
</body>
</html>
