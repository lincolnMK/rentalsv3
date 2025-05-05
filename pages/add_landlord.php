<?php
include_once __DIR__ . '/../auth_check.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Array of safe variables
    $safe_variables = [
        'name' => '',
        'address' => '',
        'phone_no' => '',
        'landlord_type_id' => '',
        'national_id' => '',
        'bank_name' => '',
        'bank_account_holder' => '',
        'bank_account_number' => '',
        'vendor_code' => ''
    ];

    // Populate the safe variables with form data
    foreach ($_POST as $key => $value) {
        $safe_variables[$key] = $value;
    }

    // Validate and sanitize data
    $name = $safe_variables['name'];
    $address = $safe_variables['address'];
    $phone_no = $safe_variables['phone_no'];
    $landlord_type_id = $safe_variables['landlord_type_id'];
    $national_id = $safe_variables['national_id'];
    $bank_name = $safe_variables['bank_name'];
    $bank_account_holder = $safe_variables['bank_account_holder'];
    $bank_account_number = $safe_variables['bank_account_number'];
    $vendor_code = $safe_variables['vendor_code'];

    // Validate landlord_type_id (ensure it's an integer)
    if (!is_numeric($landlord_type_id)) {
        $landlord_type_id = 1; // Default to Individual
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO landlord (name, address, phone_number, landlord_type_id,
national_id, bank_name, bank_account_holder, bank_account_number, vendor_code)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("sssisssis", $name, $address, $phone_no,
$landlord_type_id, $national_id, $bank_name, $bank_account_holder,
$bank_account_number, $vendor_code);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New landlord added successfully!";
        } else {
            echo "Error executing statement: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>





            <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Add Landlord</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Landlord Information</h5>
                            <form action="add_landlord.php" method="post">
    <div class="row g-2">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="col-md-6">
            <label for="landlord_type_id" class="form-label">Landlord Type</label>
            <select class="form-control" id="landlord_type_id" name="landlord_type_id" required>
                <option value="1">Individual</option>
                <option value="2">Private Organisation</option>
                <option value="3">Governmental</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="national_id" class="form-label">National ID</label>
            <input type="text" class="form-control" id="national_id" name="national_id" required>
        </div>
        <div class="col-md-6">
            <label for="phone_no" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone_no" name="phone_no" required>
        </div>

        <div class="col-12">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>

        <div class="col-12 mt-3">
            <h5>Payment Details</h5>
        </div>

        <div class="col-md-6">
            <label for="bank_name" class="form-label">Bank Name</label>
            <input type="text" class="form-control" id="bank_name" name="bank_name" required>
        </div>
        <div class="col-md-6">
            <label for="bank_account_name" class="form-label">Bank Account Name</label>
            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" required>
        </div>

        <div class="col-md-6">
            <label for="bank_account_number" class="form-label">Bank Account Number</label>
            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" required>
        </div>
        <div class="col-md-6">
            <label for="vendor_code" class="form-label">Vendor Code</label>
            <input type="text" class="form-control" id="vendor_code" name="vendor_code" required>
        </div>

        <div class="col-12 mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    
</form>

                        </div>
                    </div>
                </div>
            </div>
   