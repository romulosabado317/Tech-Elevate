<?php
session_start();
require '../db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uid']) && isset($_POST['action'])) {
    $uid = (int)$_POST['uid'];
    $action = $_POST['action'];
    if ($action === 'deactivate') {
        $conn->query("UPDATE users SET status='inactive' WHERE id=$uid");
    } elseif ($action === 'reactivate') {
        $conn->query("UPDATE users SET status='active' WHERE id=$uid");
    }
    header('Location: users.php');
    exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

include '../includes/header.php';
?>
<div class="page-body">
    <div class="admin-table-container">
        <div class="admin-table-header">
            <h1 class="page-title">Manage Users</h1>
            <a class="btn-outline" href="dashboard.php">Back to Dashboard</a>
        </div>

        <table id="usersTable" class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <span class="status-pill status-pill-<?php echo $user['status'] === 'active' ? 'green' : 'red'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <form method="post" style="display: inline-block; margin-right: 0.5rem;">
                                <input type="hidden" name="uid" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-outline action-btn" name="action" value="<?php echo $user['status'] === 'active' ? 'deactivate' : 'reactivate'; ?>">
                                    <?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-danger action-btn delete-user-btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        "layout": {
            "topStart": {
                "buttons": [
                    {
                        "extend": 'pageLength'
                    }
                ]
            },
            "topEnd": {
                "search": {
                    "placeholder": 'Type to search...'
                }
            }
        }
    });

    $('#usersTable tbody').on('click', '.delete-user-btn', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!',
            background: 'var(--card-bg)',
            customClass: {
                popup: 'card',
                title: 'text-color',
                htmlContainer: 'text-color'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) {
        Swal.fire({
            title: 'Success!',
            text: decodeURIComponent(success),
            icon: 'success',
            confirmButtonColor: 'var(--primary-color)',
            background: 'var(--card-bg)',
            customClass: {
                popup: 'card',
                title: 'text-color',
                htmlContainer: 'text-color'
            }
        }).then(() => {
            history.replaceState(null, '', window.location.pathname);
        });
    }

    if (error) {
        Swal.fire({
            title: 'Error!',
            text: decodeURIComponent(error),
            icon: 'error',
            confirmButtonColor: '#EF4444',
            background: 'var(--card-bg)',
            customClass: {
                popup: 'card',
                title: 'text-color',
                htmlContainer: 'text-color'
            }
        }).then(() => {
            history.replaceState(null, '', window.location.pathname);
        });
    }
});
</script>