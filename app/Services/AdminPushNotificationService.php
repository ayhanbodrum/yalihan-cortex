<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AdminPushNotificationService
{
    public function sendNotification($title, $message, $users = null)
    {
        // Push notification gönderme logic
        Log::info("Push notification sent: {$title} - {$message}");
        
        return ["status" => "success", "message" => "Notification sent"];
    }
    
    public function subscribeUser($userId, $endpoint, $keys)
    {
        // User subscription logic
        return ["status" => "success", "message" => "User subscribed"];
    }
}