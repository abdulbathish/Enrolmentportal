<?php
session_start();
require_once 'connection.php';
require_once 'helpers/user-session.php';
require_once 'constants.php';

// Check if user is logged in
$loggedInUser = getLoggedInUser($db_conn);
if (!$loggedInUser) {
    header("Location: login.php");
    exit;
}

// Only show logs in debug mode
if (!JWT_DEBUG_MODE) {
    header("HTTP/1.1 403 Forbidden");
    echo "Debug mode is disabled.";
    exit;
}

// Function to get recent log entries
function getRecentLogs($maxLines = 50) {
    $logFile = ini_get('error_log');
    if (!$logFile || !file_exists($logFile)) {
        return ["No log file found or accessible."];
    }
    
    $lines = file($logFile);
    if ($lines === false) {
        return ["Unable to read log file."];
    }
    
    // Filter for JWT Debug entries and get recent ones
    $jwtLogs = [];
    foreach ($lines as $line) {
        if (strpos($line, 'JWT Debug:') !== false) {
            $jwtLogs[] = trim($line);
        }
    }
    
    return array_slice($jwtLogs, -$maxLines);
}

$logs = getRecentLogs();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Debug Log Viewer</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .header h1 {
            color: #333;
            margin: 0;
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .log-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .log-entry {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .log-entry.success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
        }
        
        .log-entry.error {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        
        .log-entry.warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .log-entry.info {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
        }
        
        .log-entry.default {
            background-color: #e2e3e5;
            border-left: 4px solid #6c757d;
        }
        
        .empty-logs {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
        }
        
        .refresh-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .timestamp {
            color: #6c757d;
            font-weight: bold;
        }
        
        .log-level {
            color: #495057;
            font-weight: bold;
        }
        
        .log-message {
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>System Debug Log Viewer</h1>
            <div>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <a href="?refresh=1" class="btn">Refresh Logs</a>
            </div>
        </div>
        
        <div class="refresh-info">
            <strong>Information:</strong> This page shows the recent JWT verification debug logs. 
            Refresh the page to see new entries after performing authentication actions.
            <br><strong>Last Updated:</strong> <?= date('Y-m-d H:i:s') ?>
        </div>
        
        <div class="log-container">
            <?php if (empty($logs)): ?>
                <div class="empty-logs">
                    <p>No JWT debug logs found.</p>
                    <p>Try logging in again or performing actions that trigger JWT verification.</p>
                </div>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <?php
                    // Determine log entry type based on content
                    $logClass = 'default';
                    if (strpos($log, 'verification successful') !== false || strpos($log, 'SUCCESS') !== false) {
                        $logClass = 'success';
                    } elseif (strpos($log, 'FAILED') !== false || strpos($log, 'error') !== false || strpos($log, 'Error') !== false) {
                        $logClass = 'error';
                    } elseif (strpos($log, 'warning') !== false || strpos($log, 'Warning') !== false || strpos($log, 'fallback') !== false) {
                        $logClass = 'warning';
                    } elseif (strpos($log, 'INFO') !== false || strpos($log, 'Starting') !== false || strpos($log, 'Request') !== false) {
                        $logClass = 'info';
                    }
                    
                    // Parse log entry (basic parsing)
                    $parts = explode('] JWT Debug: ', $log, 2);
                    $timestamp = isset($parts[0]) ? str_replace('[', '', $parts[0]) : '';
                    $message = isset($parts[1]) ? $parts[1] : $log;
                    ?>
                    <div class="log-entry <?= $logClass ?>">
                        <?php if ($timestamp): ?>
                            <span class="timestamp">[<?= htmlspecialchars($timestamp) ?>]</span>
                        <?php endif; ?>
                        <span class="log-level">JWT Debug:</span>
                        <span class="log-message"><?= htmlspecialchars($message) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d;">
            <strong>Legend:</strong>
            <span style="color: #28a745;">Success</span> |
            <span style="color: #dc3545;">Error</span> |
            <span style="color: #ffc107;">Warning</span> |
            <span style="color: #17a2b8;">Info</span> |
            <span style="color: #6c757d;">General</span>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds if user is viewing logs
        setTimeout(function() {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 30000);
    </script>
</body>
</html> 