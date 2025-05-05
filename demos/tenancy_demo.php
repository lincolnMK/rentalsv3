<tbody>
    <?php foreach ($tenancies as $tenancy): ?>
        <tr>
            <td>
                <a href="tenancy_details.php?tenancy_id=<?= $tenancy['Tenancy_id']; ?>">
                    <?= htmlspecialchars($tenancy['Tenancy_id']); ?>
                </a>
            </td>
            <td><?= htmlspecialchars($tenancy['Property_id']); ?></td>
            <td><?= htmlspecialchars($tenancy['Occupant_id']); ?></td>
            <td><?= htmlspecialchars($tenancy['RCA']); ?></td>
            <td><?= htmlspecialchars($tenancy['Monthly_rent']); ?></td>
            <td><?= htmlspecialchars($tenancy['Termination_status']); ?></td>
            <td>
                <a href="tenancy_details.php?tenancy_id=<?= $tenancy['Tenancy_id']; ?>" class="btn btn-info">View Details</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
