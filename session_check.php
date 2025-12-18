<?php
/**
 * Session Check - Include di setiap halaman yang perlu auth
 */

session_start();

require_once 'config.php';

function checkAuth() {
    global $conn;
    
    // Cek apakah sudah login
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['session_id'])) {
        redirectToLogin();
        return false;
    }
    
    $session_id = $_SESSION['session_id'];
    
    try {
        // Verify session di database
        $stmt = $conn->prepare("
            SELECT * FROM sessions 
            WHERE session_id = :session_id 
            AND expires_at > datetime('now')
        ");
        $stmt->execute([':session_id' => $session_id]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            // Session tidak valid atau kadaluarsa
            session_destroy();
            redirectToLogin();
            return false;
        }
        
        return true;
        
    } catch (Exception $e) {
        session_destroy();
        redirectToLogin();
        return false;
    }
}

function redirectToLogin() {
    header('Location: request_access.php');
    exit;
}

function getUserInfo() {
    return [
        'telegram_chat_id' => $_SESSION['telegram_chat_id'] ?? null,
        'telegram_username' => $_SESSION['telegram_username'] ?? null,
        'session_id' => $_SESSION['session_id'] ?? null
    ];
}

// Auto check jika file ini di-include
if (basename($_SERVER['PHP_SELF']) !== 'session_check.php') {
    checkAuth();
}
?>
