<?php
require __DIR__ . '/vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;

header('Content-Type: application/json; charset=utf-8');
$API_SECRET_KEY = '/';
$METODO = $_SERVER['REQUEST_METHOD'];

function send_json_error($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$provided_key = $_GET['api_key'] ?? '';
if (empty($provided_key)) {
    send_json_error('Chave de API não fornecida.', 401);
}
if ($provided_key !== $API_SECRET_KEY) {
    send_json_error('Chave de API inválida.', 403);
}

$action = $_GET['action'] ?? '';
if (empty($action)) {
    send_json_error('Nenhuma ação especificada.');
}

try {
    switch ($action) {

        case 'check_site_status':
            $url_para_verificar = 'https://www.exemplo.dev.br/index.php';
            $ch = curl_init($url_para_verificar);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            if ($error) {
                echo json_encode(['success' => true, 'data' => ['status' => 'offline', 'http_code' => "Erro: $error"]]);
            } elseif ($http_code >= 200 && $http_code < 400) {
                echo json_encode(['success' => true, 'data' => ['status' => 'online', 'http_code' => $http_code]]);
            } else {
                echo json_encode(['success' => true, 'data' => ['status' => 'offline', 'http_code' => $http_code]]);
            }
            break;

        case 'get_analytics_30d':
            $property_id = 'SEU_PROPERTY_ID_AQUI';
            $key_file_path = __DIR__ . '/analytics_key.json';
            $cache_file = __DIR__ . '/analytics_cache.json';
            $cache_time_seconds = 3600;

            if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time_seconds)) {
                $cached_data = file_get_contents($cache_file);
                echo $cached_data;
                break;
            }

            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $key_file_path);
            $client = new BetaAnalyticsDataClient();

            $response = $client->runReport([
                'property' => 'properties/' . $property_id,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => '30daysAgo',
                        'end_date' => 'today',
                    ]),
                ],
                'metrics' => [
                    new Metric(['name' => 'screenPageViews']),
                ]
            ]);

            $pageviews = 0;
            foreach ($response->getRows() as $row) {
                $pageviews = $row->getMetricValues()[0]->getValue();
            }

            $json_response = json_encode(['success' => true, 'data' => ['pageviews' => (int) $pageviews]]);

            file_put_contents($cache_file, $json_response, LOCK_EX);

            echo $json_response;
            break;

        case 'get_deploy_info':

            $log_file_path = __DIR__ . '/deploy.log';
            if (file_exists($log_file_path)) {
                $lines = file($log_file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $last_lines = array_slice($lines, -10);
                $info = implode("\n", $last_lines);
                echo json_encode(['success' => true, 'data' => ['info' => $info]]);
            } else {
                $info = "Arquivo de log não encontrado em:\n" . $log_file_path;
                echo json_encode(['success' => true, 'data' => ['info' => $info]]);
            }
            break;

        default:
            send_json_error("Ação desconhecida: '$action'");
            break;
    }

} catch (Exception $e) {
    send_json_error('Erro interno na API: ' . $e->getMessage(), 500);
}
?>