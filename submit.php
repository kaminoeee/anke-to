<?php
// パスワード「nikonikoch」のSHA-256ハッシュ値
define('TARGET_HASH', '9a56208bb94f1c99be383a81f3d8fdfdfb58f80cb5cda621b183610931cb9f3f');
define('LOG_FILE', 'logs.txt');

// 1. 隠しボタンからログ確認に来た場合
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pw'])) {
    $input_pw = $_GET['pw'];
    if (hash('sha256', $input_pw) === TARGET_HASH) {
        // 認証成功：ログファイルを表示
        echo "<!DOCTYPE html><html lang='ja'><head><meta charset='UTF-8'><title>管理者ログ確認</title>";
        echo "<style>body{font-family:sans-serif; background:#2d2d2d; color:#fff; padding:20px;} .log-item{background:#3e3e3e; padding:15px; margin-bottom:10px; border-radius:6px; white-space:pre-wrap; font-family:monospace;}</style></head><body>";
        echo "<h2>管理者用 全ユーザーの回答ログ一覧</h2>";
        
        if (file_exists(LOG_FILE)) {
            $logs = file_get_contents(LOG_FILE);
            if (trim($logs) === "") {
                echo "回答データはまだありません。";
            } else {
                echo $logs;
            }
        } else {
            echo "回答データはまだありません。";
        }
        echo "<br><br><a href='index.html' style='color:#529bf5;'>フォームに戻る</a></body></html>";
    } else {
        echo "<script>alert('パスワードが違います。'); window.location.href='index.html';</script>";
    }
    exit;
}

// 2. ユーザーがアンケートを送信（POST）してきた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purpose = isset($_POST['purpose']) ? htmlspecialchars($_POST['purpose']) : 'なし';
    $title = !empty($_POST['siteTitle']) ? htmlspecialchars($_POST['siteTitle']) : 'なし';
    $url = !empty($_POST['siteUrl']) ? htmlspecialchars($_POST['siteUrl']) : 'なし';
    $isgc = isset($_POST['isgc']) ? htmlspecialchars($_POST['isgc']) : 'なし';
    $message = !empty($_POST['message']) ? htmlspecialchars($_POST['message']) : 'なし';
    $timestamp = date("Y/m/d H:i:s");

    // ログに書き込むテキストの組み立て
    $logEntry = "<div class='log-item'>\n" .
                "■ 日時: {$timestamp}\n" .
                "■ 目的: {$purpose}\n" .
                "■ タイトル: {$title}\n" .
                "■ URL: {$url}\n" .
                "■ 規制回避状況: {$isgc}\n" .
                "■ メッセージ:\n{$message}\n" .
                "</div>\n\n";

    // サーバー上の logs.txt に追記保存
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);

    // 送信完了画面
    echo "<!DOCTYPE html><html lang='ja'><head><meta charset='UTF-8'><title>送信完了</title>";
    echo "<style>body{font-family:sans-serif; text-align:center; padding:50px; background:#eaeaea;} .msg{background:#fff; padding:30px; display:inline-block; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);}</style></head><body>";
    echo "<div class='msg'><h2>送信が完了しました！</h2><p>ご協力ありがとうございました。</p><br><a href='index.html' style='color:#4a90e2; text-decoration:none; font-weight:bold;'>← もどる</a></div>";
    echo "</body></html>";
    exit;
}

// 直接アクセスされた場合はフォームに戻す
header("Location: index.html");
exit;
?>
