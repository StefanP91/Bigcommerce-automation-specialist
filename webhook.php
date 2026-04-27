<?php
header('ngrok-skip-browser-warning: 1');

$store_hash = 'ljloonmrju';
$access_token = 'rlzk6gq5lwp5mmyat0u242vrt7x289c';
$logFile = __DIR__ . '/webhook_activity.log';

$input = file_get_contents('php://input');
$webhook_data = json_decode($input, true);

if (isset($webhook_data['data']['id'])) {
    $productId = $webhook_data['data']['id'];
    $timestamp = time();
    $currentDate = date('Y-m-d H:i:s', $timestamp);
    
    $tempFile = __DIR__ . "/last_update_$productId.txt";
    if (file_exists($tempFile)) {
        $lastUpdate = (int)file_get_contents($tempFile);
        if (($timestamp - $lastUpdate) < 10) {
            exit; 
        }
    }
    file_put_contents($tempFile, $timestamp);

    file_put_contents($logFile, "[$currentDate] Processing for ID: $productId\n", FILE_APPEND);

    $productUrl = "https://api.bigcommerce.com/stores/$store_hash/v3/catalog/products/$productId";
    
    $ch = curl_init($productUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Auth-Token: $access_token", "Accept: application/json"]);
    $productData = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($productData['data'])) {
        $oldDescription = $productData['data']['description'];
        
        $cleanDescription = preg_replace('/<p>Last Verified:.*?<\/p>/', '', $oldDescription);
        $newDescription = "<p>Last Verified: $currentDate</p>" . $cleanDescription;

        $payload = json_encode(["description" => $newDescription]);

        $ch = curl_init($productUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Auth-Token: $access_token",
            "Content-Type: application/json"
        ]);
        
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status == 200) {
            file_put_contents($logFile, "[$currentDate] SUCCESSFULLY FINISHED.\n", FILE_APPEND);
        }
    }
}
?>