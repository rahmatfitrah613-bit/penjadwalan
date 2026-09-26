<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: " . base_url('login.php'));
    exit();
}
?>
