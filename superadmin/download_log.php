<?php
    session_save_path('../login/session');
    session_start();

    if (!isset($_SESSION['email'])) {
        die("Akses ditolak");
    }

    $filename = basename($_GET['file'] ?? '');

    if (empty($filename)) {
        die("File tidak ditemukan");
    }

    $filepath = "../logs/" . $filename;

    if (!file_exists($filepath) || !is_file($filepath)) {
        die("File tidak ditemukan");
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    ob_clean();
    flush();

    readfile($filepath);
    exit;
?>