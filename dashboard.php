<?php
session_start(); // Pastikan sesi dimulai
include 'config.php';


// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect ke halaman login jika belum login
    exit;
}


// Jika tombol submit ditekan (untuk tambah atau update tugas)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = $_POST['task'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $subtasks = $_POST['subtask'];
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id']; // Ambil user_id dari sesi

    if (!empty($task_id)) {
        $query = "UPDATE tasks SET task='$task', due_date='$due_date', priority='$priority' WHERE id='$task_id' AND user_id='$user_id'";
        $result = mysqli_query($conn, $query);

        mysqli_query($conn, "DELETE FROM subtasks WHERE task_id='$task_id'");

        if ($result && !empty($subtasks)) {
            foreach ($subtasks as $subtask) {
                if (!empty($subtask)) {
                    $query_subtask = "INSERT INTO subtasks (task_id, subtask, status) VALUES ('$task_id', '$subtask', 'open')";
                    mysqli_query($conn, $query_subtask);
                }
            }
        }
    } else {
        $query = "INSERT INTO tasks (task, due_date, priority, taskstatus, user_id) VALUES ('$task', '$due_date', '$priority', 'open', '$user_id')";
        $result = mysqli_query($conn, $query);
        $task_id = mysqli_insert_id($conn);

        if ($result && !empty($subtasks)) {
            foreach ($subtasks as $subtask) {
                if (!empty($subtask)) {
                    $query_subtask = "INSERT INTO subtasks (task_id, subtask, status) VALUES ('$task_id', '$subtask', 'open')";
                    mysqli_query($conn, $query_subtask);
                }
            }
        }
    }
    header('Location: dashboard.php');
    exit;
}

// Proses update data (close or open) via GET parameter "done"
if (isset($_GET['done'])) {
    $taskid = $_GET['done'];
    $q_select = "SELECT taskstatus FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($q_select);
    $stmt->bind_param("i", $taskid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $r = $result->fetch_assoc();
        $status = ($r['taskstatus'] == 'open') ? 'close' : 'open';
        
        $q_update = "UPDATE tasks SET taskstatus = ? WHERE id = ?";
        $stmt = $conn->prepare($q_update);
        $stmt->bind_param("si", $status, $taskid);
        $stmt->execute();

        // Update semua subtugas terkait
        $query_subtasks = "UPDATE subtasks SET status = ? WHERE task_id = ?";
        $stmt = $conn->prepare($query_subtasks);
        $stmt->bind_param("si", $status, $taskid);
        $stmt->execute();
    }
    header('Location: dashboard.php'); // Redirect ke halaman dashboard
    exit;
}

// Hapus tugas
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM tasks WHERE id='$task_id' AND user_id='{$_SESSION['user_id']}'");
    mysqli_query($conn, "DELETE FROM subtasks WHERE task_id='$task_id'");
    header("Location: dashboard.php");
    exit;
}

// Ambil data tugas untuk keperluan edit
$edit_task = null;
if (isset($_GET['edit'])) {
    $task_id = $_GET['edit'];
    $query = "SELECT * FROM tasks WHERE id='$task_id'";
    $edit_task = mysqli_fetch_assoc(mysqli_query($conn, $query));
}

// Proses pencarian
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Ambil data tugas untuk ditampilkan
$query = "SELECT * FROM tasks WHERE user_id = '{$_SESSION['user_id']}' AND task LIKE '%$search_query%' ORDER BY due_date ASC";
$result = mysqli_query($conn, $query);

// Notifikasi untuk tugas yang mendekati deadline
$today = new DateTime();
$notification_days = 2; // Notifikasi 2 hari sebelum deadline

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            color: #0a0a0a;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #bbd6fa;
            padding: 20px;
            background: url('pink4.jpg') no-repeat;
            background-size: cover;
            background-position: center;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #f7a3c4;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: white;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #f7a3c4;
            border-radius: 5px;
        }
        .btn {
            display: block;
            background: #bbd6fa;
            color: black;
            font-size: 16px;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
        }
        .task-item {
            background: #e3efff; /* Warna lebih terang */
            padding: 12px; /* Tambah padding */
            border-radius: 12px; /* Membuat sudut lebih membulat */
            margin-bottom: 12px; /* Tambah jarak antar tugas */
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); /* Tambahkan efek bayangan */
            border: 1px solid #a2a9b3; /* Tambahkan border tipis */
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #a2a9b3;
            padding-bottom: 6px; /* Tambahkan padding bawah */
            margin-bottom: 6px; /* Tambahkan jarak */
        }
        .task-title {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 16px;
            padding: 0;
            margin: 0;
        }
        .due-date {
            font-size: 12px;
            color: #273240;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .subtask {
            display: flex;
            align-items: center;
            padding: 0;
            margin: 2px 0;
            font-size: 14px;
        }
        .btn-subtask {
            display: block;
            background: #bbd6fa;
            color: black;
            font-size: 14px;
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }
        /* CSS untuk mencoret teks */
        .completed {
            text-decoration: line-through;
            color: black;
        }
        .notification {
            background-color: #91999e;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #f28dd9;
            padding: 10px;
            border-radius: 10px;
            width: fit-content;
        }
        .search-form input {
            padding: 8px;
            border: 2px solid #f28dd9;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-search {
            background-color: #bbd6fa; 
            color: black;
            font-size: 16px;
            padding: 8px 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .btn-reset {
            background-color: #bbd6fa; 
            color: black;
            font-size: 16px;
            padding: 8px 15px;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
        }
        .btn-search:hover {
            background-color: #3ba9f7;
        }
        .btn-reset:hover {
            background-color: #3ba9f7;
        }
        .sub-task-container {
            margin-left: 0px;
            margin-top: 2px;
            padding: 0;
        }
        input[type="checkbox"] {
            margin: 0;
            padding: 0;
            width: 16px;
            height: 16px;
        }
        .subtask input[type="checkbox"] {
            margin-left: -1px; 
            margin-right: 1px;
        }
        .subtask span {
            margin: 0;
            padding: 0;
        }
        .header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #5a3d2b;
        }
        .pencarian-tugas {
            margin-bottom: 20px; /* Tambahkan jarak bawah */
        }

        .tambah-tugas {
            margin-top: 20px; /* Tambahkan jarak atas */
            margin-bottom: 20px; /* Tambahkan jarak bawah */
        }

        /* CSS untuk scroll pada daftar tugas */
        .task-list {
            max-height: 400px; /* Atur tinggi maksimum sesuai kebutuhan */
            overflow-y: auto; /* Tambahkan scroll vertikal jika konten melebihi tinggi */
            margin-top: 10px; /* Tambahkan jarak atas jika perlu */
            padding: 10px; /* Tambahkan padding jika perlu */
            background-color: #bbd6fa; /* Warna latar belakang untuk membedakan */
            border-radius: 5px; /* Tambahkan border-radius untuk estetika */
        }

    </style>
</head>
<body>
<!-- Form Pencarian -->
<div class="container pencarian-tugas">
    <h2>PENCARIAN TUGAS</h2>
    <form action="" method="get" class="search-form">
        <input type="text" name="search" placeholder="Cari Tugas..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn-search">Cari</button>
        <a href="dashboard.php" class="btn-reset">Reset</a>
    </form>
</div>

<!-- Form Tambah/Edit Tugas -->
<?php if (empty($search_query)) { ?>
<div class="container tambah-tugas">
    <h2><?= $edit_task ? 'Edit Tugas' : 'TAMBAH TUGAS' ?></h2>
    <form action="" method="POST">
        <input type="hidden" name="task_id" value="<?= $edit_task['id'] ?? '' ?>">
        <label>Tugas</label>
        <input type="text" name="task" value="<?= $edit_task['task'] ?? '' ?>" required>

        <label>Sub Tugas</label>
        <div id="sub-task-container">
            <?php
            if ($edit_task) {
                $task_id = $edit_task['id'];
                $query_subtasks = "SELECT * FROM subtasks WHERE task_id = '$task_id'";
                $result_subtasks = mysqli_query($conn, $query_subtasks);
                while ($subtask = mysqli_fetch_assoc($result_subtasks)) {
                    echo '<input type="text" name="subtask[]" value="' . htmlspecialchars($subtask['subtask']) . '" class="subtask-input">';
                }
            } else {
                echo '<input type="text" name="subtask[]" class="subtask-input">';
            }
            ?>
            <button type="button" class="btn-subtask" onclick="tambahSubtask()">Tambah Subtask</button>
        </div>

        <label>Tanggal</label>
        <input type="date" name="due_date" value="<?= $edit_task['due_date'] ?? '' ?>" required>

        <label>Priority</label>
        <select name="priority" required>
            <option value="Biasa" <?= ($edit_task['priority'] ?? '') == 'Biasa' ? 'selected' : '' ?>>Biasa</option>
            <option value="Cukup Penting" <?= ($edit_task['priority'] ?? '') == 'Cukup Penting' ? 'selected' : '' ?>>Cukup Penting</option>
            <option value="Sangat Penting" <?= ($edit_task['priority'] ?? '') == 'Sangat Penting' ? 'selected' : '' ?>>Sangat Penting</option>
        </select>

        <button type="submit" class="btn"><?= $edit_task ? 'Update Task' : 'Tambah Tugas' ?></button>
    </form>
</div>
<?php } ?>

    <!-- Daftar Tugas -->
    <div class="container">
        <h2>DAFTAR TUGAS</h2>
        <div class="task-list"> <!-- Tambahkan div ini untuk scroll -->
        <?php
        while ($r = mysqli_fetch_assoc($result)) {
            // Cek apakah tugas mendekati deadline
            $due_date = new DateTime($r['due_date']);
            $today = new DateTime(); // Tanggal hari ini
            $isOverdue = $due_date < $today; // Cek apakah tanggal jatuh tempo sudah terlewat

            // Notifikasi untuk tugas yang mendekati deadline
            $interval = $today->diff($due_date);
            if ($interval->days <= $notification_days && $interval->invert == 0) {
                echo '<div class="notification"> TUGAS "' . htmlspecialchars($r['task']) . '" MENDEKATI DEADLINE PADA ' . $due_date->format('d-m-Y') . '.</div>';
            }
        ?>
            
            <div class="task-item <?= $r['taskstatus'] == 'close' ? 'completed' : '' ?> <?= $isOverdue ? 'completed' : '' ?>">
                <div class="task-header">
                    <div class="task-title">
                        <input type="checkbox" onclick="toggleAllSubtasks(this, <?= $r['id'] ?>)" <?= ($r['taskstatus'] == 'close') ? 'checked' : '' ?>>
                        <b class="<?= ($r['taskstatus'] == 'close') ? 'completed' : '' ?>"><?= htmlspecialchars($r['task']) ?></b> (<?= ucfirst($r['priority']) ?>)
                    </div>
                    <div class="actions">
                        <span class="due-date">🗓️ <?= $r['due_date'] ?></span>
                        <a href="?edit=<?= $r['id'] ?>" class="text-orange" title="Edit">
                            <i class="bx bx-edit"></i>
                        </a>
                        <a href="?delete=<?= $r['id'] ?>" class="text-red" title="Remove" onclick="return confirm('Are you sure?')">
                            <i class="bx bx-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="sub-task-container">
                    <?php
                    $task_id = $r['id'];
                    $query_subtasks = "SELECT * FROM subtasks WHERE task_id = '$task_id'";
                    $result_subtasks = mysqli_query($conn, $query_subtasks);
                    while ($subtask = mysqli_fetch_assoc($result_subtasks)) {
                        echo '<div class="subtask ' . ($subtask['status'] == 'close' ? 'completed' : '') . '" data-task-id="' . $r['id'] . '">
                                <input type="checkbox" onchange="toggleSubtask(this, ' . $subtask['id'] . ')" ' . ($subtask['status'] == 'close' ? 'checked' : '') . ' /> 
                                <span class="' . ($subtask['status'] == 'close' ? 'completed' : '') . '">' . htmlspecialchars($subtask['subtask']) . '</span>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>

<script>
function tambahSubtask() {
    let container = document.getElementById("sub-task-container");

    // Buat elemen input untuk subtask baru
    let input = document.createElement("input");
    input.type = "text";
    input.name = "subtask[]";
    input.classList.add("subtask-input");

    // Ambil tombol tambah subtask saat ini
    let addButton = document.querySelector(".btn-subtask");

    // Sisipkan input sebelum tombol
    container.insertBefore(input, addButton);
}

function toggleAllSubtasks(checkbox, taskId) {
    const status = checkbox.checked ? 'close' : 'open';

    fetch(`update_task.php?id=${taskId}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const taskItem = checkbox.closest('.task-item');
                const titleElement = taskItem.querySelector('.task-title b');
                if (checkbox.checked) {
                    taskItem.classList.add('completed');
                    if (titleElement) titleElement.classList.add('completed');
                } else {
                    taskItem.classList.remove('completed');
                    if (titleElement) titleElement.classList.remove('completed');
                }
                const subtaskElements = taskItem.querySelectorAll('.subtask');
                subtaskElements.forEach(function(subtask) {
                    const subtaskCheckbox = subtask.querySelector('input[type="checkbox"]');
                    subtaskCheckbox.checked = checkbox.checked;
                    const span = subtask.querySelector('span');
                    if (checkbox.checked) {
                        subtask.classList.add('completed');
                        if (span) span.classList.add('completed');
                    } else {
                        subtask.classList.remove('completed');
                        if (span) span.classList.remove('completed');
                    }
                });
            } else {
                alert('Gagal memperbarui status tugas utama.');
            }
        });
}

function toggleSubtask(checkbox, subtaskId) {
    const status = checkbox.checked ? 'close' : 'open';

    fetch(`update_subtask.php?id=${subtaskId}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const subtaskElement = checkbox.closest('.subtask');
                const span = subtaskElement.querySelector('span');
                if (checkbox.checked) {
                    subtaskElement.classList.add('completed');
                    if (span) span.classList.add('completed');
                } else {
                    subtaskElement.classList.remove('completed');
                    if (span) span.classList.remove('completed');
                }
            } else {
                alert('Gagal memperbarui status subtugas.');
            }
        });
}
</script>

<div class="container">
    <a href="logout.php" onclick="return confirm('Anda yakin ingin logout?')" class="btn">Logout</a>
</div>
</body>
</html>