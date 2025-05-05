<?php 
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the username from the session (adjust this depending on how user data is stored)
$username = $_SESSION['username'];  // Adjust as necessary
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';


// Database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//pagenation code start 


//pagination code end







?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>

     <!-- Load Bootstrap from CDN -->
     <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    
</head>
<body class="container-fluid">

   <!-- Top Bar -->
<div class="row bg-dark text-white py-2">
    <div class="col-md-6">
        <!-- Optional: Place your content here (e.g., logo, title, etc.) -->
    </div>
    <div class="col-md-6 text-end">
        <!-- User Dropdown -->
        <div class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($username); ?></span>
                <?php
                    // Assuming $profile_picture contains the path to the uploaded image
                    if (!empty($profile_picture)) {
                        // If the user has uploaded a profile picture, display it
                        echo '<img class="img-profile rounded-circle img-fluid" src="' . htmlspecialchars($profile_picture) . '" alt=" " style="width: 30px; height: 30px;">';
                    } else {
                        // If no profile picture, display a default image
                        echo '<img class="img-profile rounded-circle img-fluid" src="assets/images/default-avatar.png" alt="Default Profile" style="width: 30px; height: 30px;">';
                    }
                ?>
            </a>
            <!-- Dropdown - User Information -->
            <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Activity Log</a></li>
                <div class="dropdown-divider"></div>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</div>

    <!-- Sidebar and Main Content Wrapper -->
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-2 bg-light vh-100 d-md-block sidebar">
            <div class="d-flex flex-column align-items-start py-3">
            <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                
            <h3 class="ms-3">Rental System</h3>
                <ul class="nav flex-column w-100 mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="homepage.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_payment.php">
                            <i class="fas fa-money-bill-wave"></i> Add Payment
                        </a>
                    </li>
                    <!-- Add other nav items as needed -->
                </ul>
            </div>
        </nav>



        <main class="col-md-10 bg-light">

                       <!-- Button to toggle sidebar visibility -->
            <button id="sidebarToggle" class="btn btn-dark d-md-none">☰</button>

                <!-- Topbar (Inside Main Content) -->
                <div class="row bg-white py-3 shadow-sm">
                    <div class="col">
                        <h4 class="text-primary">Payments</h4>
                        
                    </div>
                </div>



    <div class="row p-4">
        <h2 class="mb-4"> Search for Payments</h2>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input
                    type="text"
                    id="searchInput"
                    class="form-control"
                    placeholder="Search..."
                    onkeyup="fetchPayments()"
                />
            </div>
            <div class="col-md-4">
                <select id="searchColumn" class="form-control" onchange="fetchPayments()">
                    <option value="">Search All Columns</option>
                    <!-- Dynamically populated column options -->
                </select>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-secondary me-2" onclick="exportToExcel()">Export to Excel</button>
                <button class="btn btn-secondary" onclick="exportToPDF()">Export to PDF</button>
            </div>
        </div>

        <!-- Column Selection -->
        <div class="mb-3">
            <label class="form-label">Columns to Display:</label>
            <div id="columnSelection">
                <!-- Dynamically generated checkboxes for each column -->
            </div>
        </div>

        <!-- Payments Table -->
        <table class="table table-bordered">
            <thead class="table-light">
                <tr id="tableHeaders">
                    <!-- Dynamically generated headers with sorting -->
                </tr>
            </thead>
            <tbody id="paymentsTable">
                <!-- Dynamically loaded data -->
            </tbody>
        </table>
                       <!-- Pagination -->
                        <nav>
                          <ul class="pagination justify-content-center">
                       <li class="page-item" > <div id="pagination"></div> <li>
                         </u>
                  </nav>

    </div>
                  </main>

 <script>
    let columns = [
        { key: "Payment_id", label: "Payment ID" },
        { key: "Tenancy_id", label: "Tenancy ID" },
        { key: "Payment_date", label: "Payment Date" },
        { key: "Rent_per_month", label: "Rent Per Month" },
        { key: "VAT", label: "VAT" },
        { key: "Total_payment", label: "Total Payment" },
        { key: "Annual_gross_rent", label: "Annual Gross Rent" },
        { key: "Approval_status", label: "Approval Status" },
        { key: "Approval_date", label: "Approval Date" },
        { key: "Payment_received_by_landlord", label: "Received by Landlord" },
        { key: "Balance_due", label: "Balance Due" },
        { key: "Comments", label: "Comments" },
    ];
    let sortColumn = null;
    let sortDirection = "asc";
    let selectedColumns = columns.map((col) => col.key);
    let currentPage = 1;
    let totalPages = 1;

    // Generate the column selection dropdown
    function generateColumnSelection() {
        const columnSelect = document.getElementById("searchColumn");
        columns.forEach((col) => {
            const option = document.createElement("option");
            option.value = col.key;
            option.textContent = col.label;
            columnSelect.appendChild(option);
        });
    }

    function generateColumnSelectionCheckboxes() {
        const container = document.getElementById("columnSelection");
        container.innerHTML = "";
        columns.forEach((col) => {
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.id = col.key;
            checkbox.checked = true;
            checkbox.onchange = toggleColumn;

            const label = document.createElement("label");
            label.textContent = col.label;
            label.setAttribute("for", col.key);

            const wrapper = document.createElement("div");
            wrapper.className = "form-check form-check-inline";
            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);

            container.appendChild(wrapper);
        });
    }

    function toggleColumn(event) {
        const columnKey = event.target.id;
        if (event.target.checked) {
            selectedColumns.push(columnKey);
        } else {
            selectedColumns = selectedColumns.filter((key) => key !== columnKey);
        }
        renderTableHeaders();
        fetchPayments();
    }

    function renderTableHeaders() {
        const headers = document.getElementById("tableHeaders");
        headers.innerHTML = "";
        selectedColumns.forEach((colKey) => {
            const col = columns.find((c) => c.key === colKey);
            const th = document.createElement("th");
            th.textContent = col.label;
            th.style.cursor = "pointer";
            th.onclick = () => sortTable(colKey);
            headers.appendChild(th);
        });
    }

    function fetchPayments() {
        const search = document.getElementById("searchInput").value;
        const searchColumn = document.getElementById("searchColumn").value;
        const payload = {
            search,
            searchColumn, // Send the selected column to the server
            sortColumn,
            sortDirection,
            selectedColumns,
            page: currentPage, // Send current page
        };

        fetch("fetch_payments.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        })
            .then((response) => response.json())
            .then((data) => {
                const tableBody = document.getElementById("paymentsTable");
                tableBody.innerHTML = "";

                // Render table rows
                data.data.forEach((row) => {
                    const tr = document.createElement("tr");
                    selectedColumns.forEach((colKey) => {
                        const td = document.createElement("td");
                        td.textContent = row[colKey];
                        tr.appendChild(td);
                    });
                    tableBody.appendChild(tr);
                });

                // Update total pages and current page
                totalPages = data.totalPages;
                currentPage = data.currentPage;

                renderPagination();
            });
    }

    function sortTable(columnKey) {
        if (sortColumn === columnKey) {
            sortDirection = sortDirection === "asc" ? "desc" : "asc";
        } else {
            sortColumn = columnKey;
            sortDirection = "asc";
        }
        fetchPayments();
    }

    function renderPagination() {
    const paginationContainer = document.getElementById("pagination");
    paginationContainer.innerHTML = "";

    // Create <nav> element for pagination
    const nav = document.createElement("nav");
    const ul = document.createElement("ul");
    ul.classList.add("pagination", "justify-content-center");

    // Previous Page Button
    const prevItem = document.createElement("li");
    prevItem.classList.add("page-item");
    if (currentPage <= 1) {
        prevItem.classList.add("disabled");
    }

    const prevLink = document.createElement("a");
    prevLink.classList.add("page-link");
    prevLink.href = "#";
    prevLink.textContent = "Previous";
    prevLink.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            fetchPayments();
        }
    };
    prevItem.appendChild(prevLink);
    ul.appendChild(prevItem);

    // Page Number Buttons
    for (let i = 1; i <= totalPages; i++) {
        const pageItem = document.createElement("li");
        pageItem.classList.add("page-item");
        if (i === currentPage) {
            pageItem.classList.add("active");
        }

        const pageLink = document.createElement("a");
        pageLink.classList.add("page-link");
        pageLink.href = "#";
        pageLink.textContent = i;
        pageLink.onclick = () => {
            currentPage = i;
            fetchPayments();
        };
        pageItem.appendChild(pageLink);
        ul.appendChild(pageItem);
    }

    // Next Page Button
    const nextItem = document.createElement("li");
    nextItem.classList.add("page-item");
    if (currentPage >= totalPages) {
        nextItem.classList.add("disabled");
    }

    const nextLink = document.createElement("a");
    nextLink.classList.add("page-link");
    nextLink.href = "#";
    nextLink.textContent = "Next";
    nextLink.onclick = () => {
        if (currentPage < totalPages) {
            currentPage++;
            fetchPayments();
        }
    };
    nextItem.appendChild(nextLink);
    ul.appendChild(nextItem);

    nav.appendChild(ul);
    paginationContainer.appendChild(nav);
}


    function exportToExcel() {
        window.location.href = "export_to_excel.php";
    }

    function exportToPDF() {
        window.location.href = "export_to_pdf.php";
    }

    document.addEventListener("DOMContentLoaded", () => {
        generateColumnSelection();
        generateColumnSelectionCheckboxes();
        renderTableHeaders();
        fetchPayments();
    });

</script>



</body>
</html>
