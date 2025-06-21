# Blerify Notifications library

## Description

Library to manage notification sending via API

## Setup

- In your project ensure you have a folder named "config" which should contain your service account generated from Blerify Portal.

## Examples

### Create and check the status of a notification

```php
<?php

require 'vendor/autoload.php';

use Blerify\Authentication\JwtHandler;
use Blerify\Notifications\NotificationsClient;
use Blerify\Model\Request\NotificationBulkCreate;
use Ramsey\Uuid\Uuid;

// Input variables
$baseEndpoint = 'https://api.staging.blerify.com';

$notificationGroupId = 'a844ede0-7a6a-4c00-8d2a-9b69cd8b4d3b';

// Initialize JWT handler
$jwtHandler = JwtHandler::new(__DIR__ . '/config/credentials.json');

// Initialize Notifications client
$notificationClient = new NotificationsClient($baseEndpoint, $jwtHandler, $notificationGroupId);

// Step 1: Create notifications in bulk
// Keep in ming that the only required attribute is "id", "Amount" is just an example of dynanic data generated when configuring the notification corresponding to `notificationGroupId`. Notifications are configured through the Blerify portal
echo "\n1. Send notifications in bulk: ";
$items = [
    json_decode('{
            "receiverData": {
                "id": "1-234-56",
                "Amount": "200.5"
            }
        }'),
    json_decode('{
            "receiverData": {
                "id": "82469256",
                "Amount": "5238"
            }
        }')
    ];
$correlationId = Uuid::uuid4()->toString();
$createRequest = NotificationBulkCreate::new()->items($items);
$createResponse = $notificationClient->sendNotificationBulk($createRequest, $correlationId);
handleError($createResponse, $correlationId);
echo "Create notifications in bulk - response: " . json_encode($createResponse->jsonSerialize()) . "\n";
echo "Ok\n";


// Step 2: Get notification status
echo "\n2. Get notification status: ";
$itemId = $createResponse->getItems()[0]->getId();
$correlationId = Uuid::uuid4()->toString();
$getNotificationStatusResponse = $notificationClient->getNotificationItemById($itemId, $correlationId);
handleError($getNotificationStatusResponse);
echo "Notification status for a particular item: \n" . json_encode($getNotificationStatusResponse->jsonSerialize()) . "\n";
echo "Ok\n";

function handleError($response, $correlationId = null)
{
    if (is_array($response) && !empty($response['error'])) {
        // Handle the error
        echo "Error occurred: " . $response['message'] . " (Code: " . $response['code'] . ")\n";
        echo "Error details: " . json_encode($response['details']);
        if($correlationId != null) {
            echo "CorrelationId: " . $correlationId;
        }
        exit;
    }
}
```
