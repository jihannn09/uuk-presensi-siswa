<?php
// Menghapus seluruh data session saat admin logout dan kembali ke login
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;