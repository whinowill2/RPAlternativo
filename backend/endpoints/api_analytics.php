<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../google-api/vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate, no-store, max-age=0');

$API_SECRET_KEY = '';
$provided_key = $_GET['api_key'] ?? '';
if (empty($provided_key) || $provided_key !== $API_SECRET_KEY) {
    http_response_code(403);
    echo json_encode(['error' => 'Chave de API inválida ou ausente.']);
    exit;
}

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/analytics_key.json');

$ga4PropertyId = '371909764';

$client = new BetaAnalyticsDataClient();
$response = [
    'onlineUsers' => 0,
    'trafficSourcesToday' => [],
    'overview30d' => [
        'activeUsers' => 0,
        'newUsers' => 0,
        'averageSessionDuration' => 0,
    ],
    'topPages30d' => [],
    'topEvents30d' => [],
    'topCities30d' => [],
    'error' => null
];

if ($ga4PropertyId === 'YOUR_RPA_PROPERTY_ID_HERE') {
    $response['error'] = 'ID da propriedade GA4 não configurado em api_analytics.php';
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$property_name = 'properties/' . $ga4PropertyId;

try {
    $realtimeReport = $client->runRealtimeReport([
        'property' => $property_name,
        'metrics' => [new Metric(['name' => 'activeUsers'])],
    ]);
    $onlineUsers = 0;
    foreach ($realtimeReport->getRows() as $row) {
        $onlineUsers += $row->getMetricValues()[0]->getValue();
    }
    $response['onlineUsers'] = (int) $onlineUsers;

    $trafficReport = $client->runReport([
        'property' => $property_name,
        'dateRanges' => [new DateRange(['start_date' => 'today', 'end_date' => 'today'])],
        'dimensions' => [new Dimension(['name' => 'sessionSourceMedium'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'orderBys' => [new OrderBy(['metric' => new MetricOrderBy(['metric_name' => 'sessions']), 'desc' => true])],
        'limit' => 5
    ]);
    $sources = [];
    foreach ($trafficReport->getRows() as $row) {
        $sources[] = ['name' => $row->getDimensionValues()[0]->getValue(), 'value' => (int) $row->getMetricValues()[0]->getValue()];
    }
    $response['trafficSourcesToday'] = $sources;

    $overviewReport = $client->runReport([
        'property' => $property_name,
        'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
        'metrics' => [new Metric(['name' => 'activeUsers']), new Metric(['name' => 'newUsers']), new Metric(['name' => 'averageSessionDuration']),]
    ]);
    if ($overviewReport->getRows()) {
        $overviewRow = $overviewReport->getRows()[0];
        $response['overview30d'] = [
            'activeUsers' => (int) $overviewRow->getMetricValues()[0]->getValue(),
            'newUsers' => (int) $overviewRow->getMetricValues()[1]->getValue(),
            'averageSessionDuration' => (float) $overviewRow->getMetricValues()[2]->getValue(),
        ];
    }

    $pagesReport = $client->runReport([
        'property' => $property_name,
        'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
        'orderBys' => [new OrderBy(['metric' => new MetricOrderBy(['metric_name' => 'screenPageViews']), 'desc' => true])],
        'limit' => 5
    ]);
    $pages = [];
    foreach ($pagesReport->getRows() as $row) {
        $pages[] = ['name' => $row->getDimensionValues()[0]->getValue(), 'value' => (int) $row->getMetricValues()[0]->getValue()];
    }
    $response['topPages30d'] = $pages;

    $eventsReport = $client->runReport([
        'property' => $property_name,
        'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
        'dimensions' => [new Dimension(['name' => 'eventName'])],
        'metrics' => [new Metric(['name' => 'eventCount'])],
        'orderBys' => [new OrderBy(['metric' => new MetricOrderBy(['metric_name' => 'eventCount']), 'desc' => true])],
        'limit' => 5
    ]);
    $events = [];
    foreach ($eventsReport->getRows() as $row) {
        $events[] = ['name' => $row->getDimensionValues()[0]->getValue(), 'value' => (int) $row->getMetricValues()[0]->getValue()];
    }
    $response['topEvents30d'] = $events;

    $citiesReport = $client->runReport([
        'property' => $property_name,
        'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
        'dimensions' => [new Dimension(['name' => 'city'])],
        'metrics' => [new Metric(['name' => 'activeUsers'])],
        'orderBys' => [new OrderBy(['metric' => new MetricOrderBy(['metric_name' => 'activeUsers']), 'desc' => true])],
        'limit' => 5
    ]);
    $cities = [];
    foreach ($citiesReport->getRows() as $row) {
        $cityName = $row->getDimensionValues()[0]->getValue();
        if ($cityName === '(not set)' || $cityName === '')
            continue;
        $cities[] = ['name' => $cityName, 'value' => (int) $row->getMetricValues()[0]->getValue()];
    }
    $response['topCities30d'] = $cities;


} catch (Exception $e) {
    $response['status'] = 'error';
    $response['error'] = 'Falha ao conectar à API do Google Analytics: ' . $e->getMessage();
    $response['onlineUsers'] = 0;
    $response['trafficSourcesToday'] = [['name' => 'Erro ao carregar', 'value' => 0]];
    $response['topPages30d'] = [['name' => 'Erro ao carregar', 'value' => 0]];
    $response['topEvents30d'] = [['name' => 'Erro ao carregar', 'value' => 0]];
    $response['topCities30d'] = [['name' => 'Erro ao carregar', 'value' => 0]];
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
?>