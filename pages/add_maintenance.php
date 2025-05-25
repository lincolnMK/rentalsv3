<?php
include_once __DIR__ . '/../auth_check.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $property_id = $_POST['property_id'];
    $occupant_id = $_POST['occupant_id'];
    $maintenance_type = $_POST['maintenance_type'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $assigned_to = $_POST['assigned_to'];

    // Prepare the SQL statement
    $sql = "INSERT INTO maintenance_requests (Property_ID, Occupant_ID, Maintenance_Type, Description, Status, Assigned_To) VALUES (?, ?, ?, ?, ?, ?)";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("iissss", $property_id, $occupant_id, $maintenance_type, $description, $status, $assigned_to);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Maintenance request added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        
        // Close the statement
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
    }
}



?>


<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Maintenance Request Information</h5>
                <form action="add_maintenance.php" method="post">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="property_id" class="form-label">Property</label>
                            <select class="form-control" id="property_id" name="property_id" required>
                                <option value="">Select Property</option>
                                <?php
                                $sql = "SELECT * FROM property";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['Property_ID']}'>{$row['Plot_number']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="occupant_id" class="form-label">Occupant</label>
                            <select class="form-control" id="occupant_id" name="occupant_id" required>
                                <?php
                                $occupant_options = '<option value="">Select Occupant</option>';

                                if ($selected_property_id) {
                                    $sql = "SELECT o.Occupant_ID, o.Name AS Name
                                            FROM tenancy t
                                            INNER JOIN occupants o ON t.Occupant_ID = o.Occupant_ID
                                            INNER JOIN property p ON t.Property_ID = p.Property_ID
                                            WHERE p.Property_ID = ? 
                                            AND (t.Termination_status = 'active' OR t.Termination_status IS NULL)
                                            ORDER BY t.Start_Date DESC
                                            LIMIT 1";

                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $selected_property_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($row = $result->fetch_assoc()) {
                                        $occupant_options .= "<option value='{$row['Occupant_ID']}' selected>" . htmlspecialchars($row['Name']) . "</option>";
                                    }
                                    $stmt->close();
                                } else {
                                    $sql = "SELECT Occupant_ID, Name FROM occupants";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $occupant_options .= "<option value='{$row['Occupant_ID']}'>" . htmlspecialchars($row['Name']) . "</option>";
                                    }
                                }
                                echo $occupant_options;
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="maintenance_type" class="form-label">Maintenance Type</label>
                            <select class="form-control" id="maintenance_type" name="maintenance_type" required>
                                <option value="">Select Maintenance Type</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Plumbing">Plumbing</option>
                                <option value="General Repair">General Repair</option>
                                <option value="Cleaning">Cleaning</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="assigned_to" class="form-label">Assigned To</label>
                            <select class="form-control" id="assigned_to" name="assigned_to" required>
                                <option value="">Select User</option>
                                <?php
                                $sql = "SELECT * FROM users";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['User_ID']}'>{$row['First_Name']} {$row['Last_Name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Hidden Fields -->
                        <input type="hidden" name="requested_by" value="<?= $_SESSION['user_id']; ?>">
                        <input type="hidden" name="created_by" value="<?= $_SESSION['user_id']; ?>">

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
