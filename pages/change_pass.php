<?php
include_once __DIR__ . '/../auth_check.php';
// Handle password change if form is submitted



$id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
} elseif (isset($_GET['User_id']) && ctype_digit($_GET['User_id'])) {
    $id = (int)$_GET['User_id'];
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       
    // Check if password fields were submitted
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            // Hash the new password
            $password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
           $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $password, $id);

       

            if ($stmt->execute()) {
                  $changePassword = false; // Reset change password flag
                  echo "
                      <script>
                        setTimeout(function() {
                          // Redirect to same page without POST data, optionally keep params for tab
                          window.location.href = 'index.php?page=user_details&User_id={$id}&password_changed=1#security';
                        }, 1500);
                      </script>
                      ";
            } else {
                echo "<div class='alert alert-danger'>Failed to update password.</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='alert alert-warning'>Passwords do not match.</div>";
        }
    } else {
        echo "<div class='alert alert-info'>Password change skipped. Fields were left blank.</div>";
    }
}

?>