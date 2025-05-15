<?php
include_once __DIR__ . '/../auth_check.php';


$result = $conn->query("SELECT * FROM login_audit ORDER BY login_time DESC");
?>
 <div class="row bg-white py-3 shadow-sm">
                    <div class="col">
                        <h4 class="text-primary">Login Audit Log</h4>
                        
                    </div>
                </div>

<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Status</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Login Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'success'): ?>
                                    <span class="badge bg-success">Success</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Failure</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['ip_address'] ?></td>
                            <td><?= substr(htmlspecialchars($row['user_agent']), 0, 80) ?><?= strlen($row['user_agent']) > 80 ? '...' : '' ?></td>
                            <td><?= date('Y-m-d H:i:s', strtotime($row['login_time'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No login attempts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
                    </div>
                </div>  
            </div>
        




