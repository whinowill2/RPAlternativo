<?php

$webhook_secret = 'SEU_WEBHOOK_SECRET';
$log_file_path = __DIR__ . '/deploy.log';

$github_signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

if (empty($github_signature)) {
    http_response_code(403);
    die('Webhook secret não fornecido.');
}

list($algo, $hash) = explode('=', $github_signature, 2);
$expected_hash = hash_hmac($algo, $payload, $webhook_secret);

if (!hash_equals($hash, $expected_hash)) {
    http_response_code(403);
    die('Assinatura do webhook inválida.');
}

$data = json_decode($payload, true);

if (isset($data['commits']) && count($data['commits']) > 0) {

    $commit = $data['commits'][0];

    $timestamp = new DateTime($commit['timestamp']);
    $timestamp->setTimezone(new DateTimeZone('America/Sao_Paulo'));

    $committer_name = $commit['committer']['name'] ?? 'N/A';
    $commit_message = $commit['message'] ?? 'N/A';
    $date_formatted = $timestamp->format('d/m/Y H:i:s');

    $log_line = "[$date_formatted] por: $committer_name\n";
    $log_line .= "Mensagem: $commit_message\n";
    $log_line .= "----------------------------------------\n";

    file_put_contents($log_file_path, $log_line, FILE_APPEND | LOCK_EX);

    http_response_code(200);
    echo "Log de deploy recebido e salvo.";

} else {
    http_response_code(200);
    echo "Payload recebido, mas não era um push de commit.";
}

?>