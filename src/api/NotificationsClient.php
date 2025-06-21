<?php

namespace Blerify\Notifications;

use Blerify\Model\Request\NotificationBulkCreate;
use Blerify\Model\Response\CreateNotificationBulkResponse;
use Blerify\Model\Response\GetNotificationItemResponse;

class NotificationsClient
{
    private $apiClient;
    private $jwtHandler;

    private $notificationId;
    public function __construct($baseEndpoint, $jwtHandler, $notificationId)
    {
        $this->apiClient = new ApiClient($baseEndpoint, $jwtHandler);
        $this->jwtHandler = $jwtHandler;
        $this->notificationId = $notificationId;
    }

    public function sendNotificationBulk(NotificationBulkCreate $data, $correlationId = null)
    {
        $path = '/api/v1/organizations/' . $this->jwtHandler->getOrganizationId() . '/notifications/' . $this->notificationId . '/items/bulk';
        $response = $this->apiClient->call($data, $correlationId, $path, 'POST');

        if ($response['error']) {
            return $response;
        }

        return CreateNotificationBulkResponse::fromArray($response['data']);
    }

    public function getNotificationItemById(string $itemId, $correlationId = null)
    {
        $path = '/api/v1/organizations/' . $this->jwtHandler->getOrganizationId() . '/notifications/' . $this->notificationId . '/items/' . $itemId;
        $response = $this->apiClient->call(null, $correlationId, $path, 'GET');

        if ($response['error']) {
            return $response;
        }

        return GetNotificationItemResponse::fromArray($response['data']);
    }

}
