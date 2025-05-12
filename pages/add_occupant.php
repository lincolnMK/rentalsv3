<?php
include_once __DIR__ . '/../auth_check.php';


// Handle form submission for Occupants
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $mda_id = $_POST['MDA_ID'];
    $name = $_POST['Name'];
    $contact = $_POST['Contact'];
    $email_address = $_POST['Email'];
    $description = $_POST['Description'];

    $sql = "INSERT INTO occupants (MDA_ID, Name, Contact, Email, Description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $mda_id, $name, $contact, $email_address, $description);

    if ($stmt->execute()) {
        echo "New occupant added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>


            <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Add Occupant</h4>
                </div>
            </div>

            <!-- Content Goes Here -->




    <div class="row p-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Occupants Information</h5>
                    <form action="add_occupant.php" method="post">
                       
                    <div class="mb-3">
    <label for="MDA_ID" class="form-label">MDA</label>
    <select class="form-select" id="MDA_ID" name="MDA_ID" required>
        <option value="">-- Select MDA --</option>
        <option value="1">Ministry of Information</option>
        <option value="2">MEC</option>
        <option value="3">Various offices</option>
        <option value="4">Ministry of Financy</option>
        <option value="5">Department of Mining</option>
        <option value="6">National Audit</option>
        <option value="7">IMMIGRATION</option>
        <option value="8">Ombudsman</option>
        <option value="9">Homeland Security</option>
        <option value="10">OPC</option>
        <option value="11">Education</option>
        <option value="12">Lands</option>
        <option value="13">Registrar General</option>
        <option value="14">MDF</option>
        <option value="15">OVOP</option>
        <option value="16">Dept of Legal aid & Regional Elections Office</option>
        <option value="17">Department of Antiquities</option>
        <option value="18">Ministry of Justice</option>
        <option value="19">Dept of Immigration</option>
        <option value="20">MPS</option>
        <option value="21">Ombusdsman</option>
        <option value="22">Finance</option>
        <option value="23">Education</option>
        <option value="24">MEC & OVOP</option>
        <option value="25">MDF Veterans</option>
    </select>
</div>

                        


                        
                        
                        
                        
                        <div class="mb-3">
                            <label for="Name" class="form-label">Name of Occupant</label>
                            <input type="text" class="form-control" id="Name" name="Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="Contact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="Contact" name="Contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="Email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="Email" name="Email" required>
                        </div>
                       
                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control" id="Description" name="Description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Occupant</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

            <!-- End of Content -->
        </main>
