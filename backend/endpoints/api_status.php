<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate, no-store, max-age=0');

$API_SECRET_KEY = '/';

function send_json_error($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$provided_key = $_GET['api_key'] ?? '';
if (empty($provided_key) || $provided_key !== $API_SECRET_KEY) {
    send_json_error('Chave de API inválida ou ausente.', 403);
}

if (isset($_GET['ping'])) {
    echo json_encode(['status' => 'pong']);
    exit;
}

$page_start_time = microtime(true);

function checkExternalApi($url, $timeout_ms = 2000)
{
    if (!function_exists('curl_init')) {
        return ['status' => 'offline', 'http_code' => 0, 'latency_ms' => 0, 'error' => 'cURL não habilitado'];
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout_ms);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout_ms / 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'RPA-Health-Check/1.0');
    curl_exec($ch);
    $latency = curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000;
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    $is_ok = ($http_code >= 200 && $http_code < 400);
    return [
        'status' => $is_ok ? 'online' : 'offline',
        'http_code' => $http_code,
        'latency_ms' => round($latency),
        'error' => $error ?: null
    ];
}

$response = [
    'success' => true,
    'data' => [
        'status' => 'online',
        'timestamp' => date('Y-m-d H:i:s'),
        'metrics' => [
            'php_generation_ms' => 0,
            'server_load_avg' => null,
            'disk_usage_percent' => 0,
        ],
        'db_metrics' => [
            'db_connected' => false,
            'db_latency_ms' => 0,
            'threads_connected' => 0,
            'slow_queries' => [],
        ],
        'app_metrics' => ['opcache' => null],
        'deployment' => [
            'git_branch' => 'N/D',
            'git_commit_hash' => 'N/D',
            'git_commit_msg' => 'N/D',
            'git_commit_author' => 'N/D',
        ],
        'dependencies' => [],
        'logs' => ['php_error_log' => null],
    ]
];


if (function_exists('shell_exec')) {
    $git_dir = __DIR__ . '/../../.git';
    if (is_dir($git_dir)) {
        $author_name = trim(@shell_exec("git --git-dir=$git_dir log -1 --pretty=%an 2>/dev/null")) ?: 'N/D';
        $author_email = trim(@shell_exec("git --git-dir=$git_dir log -1 --pretty=%ae 2>/dev/null")) ?: null;
        $gravatar_url = null;
        if ($author_email) {
            $email_hash = md5(strtolower(trim($author_email)));
            $gravatar_url = "https://www.gravatar.com/avatar/" . $email_hash . "?s=80&d=mp";
        }
        $response['data']['deployment'] = [
            'git_branch' => trim(@shell_exec("git --git-dir=$git_dir rev-parse --abbrev-ref HEAD 2>/dev/null")) ?: 'N/D',
            'git_commit_hash' => trim(@shell_exec("git --git-dir=$git_dir rev-parse --short HEAD 2>/dev/null")) ?: 'N/D',
            'git_commit_msg' => trim(@shell_exec("git --git-dir=$git_dir log -1 --pretty=%B 2>/dev/null")) ?: 'N/D',
            'git_commit_author' => $author_name,
            'git_author_gravatar' => $gravatar_url
        ];
    } else {
        $response['data']['deployment']['git_commit_msg'] = 'Repositório .git não encontrado em ' . $git_dir;
    }
}

if (function_exists('opcache_get_status') && ($opcache_status = @opcache_get_status(false)) !== false) {
    $memory_usage = $opcache_status['memory_usage'];
    $stats = $opcache_status['opcache_statistics'];
    $total_memory = $memory_usage['used_memory'] + $memory_usage['free_memory'] + $memory_usage['wasted_memory'];
    $response['data']['app_metrics']['opcache'] = [
        'enabled' => $opcache_status['opcache_enabled'],
        'memory_usage_percent' => round(($memory_usage['used_memory'] / $total_memory) * 100, 2),
        'hit_rate_percent' => round($stats['opcache_hit_rate'], 2),
        'total_scripts_cached' => $stats['num_cached_scripts'],
        'wasted_memory_percent' => round(($memory_usage['wasted_memory'] / $total_memory) * 100, 2),
    ];
}

if (function_exists('sys_getloadavg')) {
    $response['data']['metrics']['server_load_avg'] = sys_getloadavg();
}
$disk_root = __DIR__ . '/../../';
$disk_free = @disk_free_space($disk_root);
$disk_total = @disk_total_space($disk_root);
if ($disk_total > 0) {
    $response['data']['metrics']['disk_usage_percent'] = round((($disk_total - $disk_free) / $disk_total) * 100, 2);
}

try {

    include __DIR__ . '/conexao.php';

    $db_start_time = microtime(true);
    $conn->query("SELECT 1");
    $db_end_time = microtime(true);

    $response['data']['db_metrics']['db_connected'] = true;
    $response['data']['db_metrics']['db_latency_ms'] = round(($db_end_time - $db_start_time) * 1000, 2);

    $result_threads = $conn->query("SHOW GLOBAL STATUS LIKE 'Threads_connected'");
    if ($result_threads) {
        $threads = $result_threads->fetch_assoc();
        $response['data']['db_metrics']['threads_connected'] = (int) $threads['Value'];
    }
    $slow_queries = [];
    $result_processlist = $conn->query("SHOW FULL PROCESSLIST");
    if ($result_processlist) {
        while ($row = $result_processlist->fetch_assoc()) {
            if ($row['State'] != 'NULL' && $row['Command'] == 'Query' && $row['Time'] > 5) {
                $slow_queries[] = ['id' => $row['Id'], 'user' => $row['User'], 'time_sec' => $row['Time'], 'query' => substr(trim(preg_replace('/\s+/', ' ', $row['Info'])), 0, 150) . '...'];
            }
        }
    }
    $response['data']['db_metrics']['slow_queries'] = $slow_queries;

    $conn->close();

} catch (Exception $e) {
    $response['data']['db_metrics']['db_connected'] = false;
    $response['data']['db_metrics']['error_message'] = $e->getMessage();
}

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
$protocol = $is_https ? 'https' : 'http';
$base_url = $protocol . '://' . $_SERVER['HTTP_HOST'];

$response['data']['dependencies']['Página_Home_RPA'] = checkExternalApi($base_url . '/index.php');
$response['data']['dependencies']['API_CRUD_Fluxia'] = checkExternalApi('https://www.exemplo.dev.br/endpoints/endpoint.php');


$error_log_path = ini_get('error_log');
if ($error_log_path && file_exists($error_log_path) && is_readable($error_log_path)) {
    $log_content = file_get_contents($error_log_path, false, null, -5000);
    $response['data']['logs']['php_error_log'] = htmlspecialchars(trim($log_content));
} else {
    $response['data']['logs']['php_error_log'] = "Não foi possível ler o arquivo de log em: " . htmlspecialchars($error_log_path);
}


$response['data']['metrics']['php_generation_ms'] = round((microtime(true) - $page_start_time) * 1000, 2);

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
?>