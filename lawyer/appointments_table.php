<?php
function renderAppointmentsTable($result) {
    ?>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>ID</th><th>Client Name</th><th>Email</th><th>Phone</th>
          <th>Date</th><th>Time</th><th>Issue</th><th>Matter</th>
          <th>Status</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']); ?></td>
              <td><?= htmlspecialchars($row['full_name']); ?></td>
              <td><?= htmlspecialchars($row['email_address']); ?></td>
              <td><?= htmlspecialchars($row['phone_number']); ?></td>
              <td><?= htmlspecialchars($row['appointment_date']); ?></td>
              <td><?= htmlspecialchars($row['appointment_time']); ?></td>
              <td><?= htmlspecialchars($row['legal_issue']); ?></td>
              <td><?= htmlspecialchars($row['legal_matter']); ?></td>
              <td>
                <span class="badge 
                  <?php 
                    if ($row['status'] == 'pending') echo 'bg-warning';
                    elseif ($row['status'] == 'confirmed') echo 'bg-success';
                    elseif ($row['status'] == 'rejected') echo 'bg-danger';
                    else echo 'bg-secondary';
                  ?>">
                  <?= htmlspecialchars($row['status']); ?>
                </span>
              </td>
              <td>
                <form method="post" action="update_appointment_status.php" class="d-flex gap-1">
                  <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                  <button type="submit" name="status" value="confirmed" class="btn btn-success btn-sm">Accept</button>
                  <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                  <button type="submit" name="status" value="pending" class="btn btn-warning btn-sm">Pending</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center">No appointments found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php
}
