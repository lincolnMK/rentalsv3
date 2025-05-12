<?php 
include_once __DIR__ . '/../auth_check.php';
//pagenation code start 


//pagination code end







?>



 

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


