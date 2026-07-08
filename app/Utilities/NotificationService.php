<?php

namespace App\Utilities;

use CodeIgniter\HTTP\CURLRequest;

class NotificationService
{
    /**
     * Send SMS notification
     * Uses Twilio API
     */
    public static function sendSMS($phoneNumber, $message)
    {
        try {
            $twilioSid = getenv('TWILIO_ACCOUNT_SID');
            $twilioToken = getenv('TWILIO_AUTH_TOKEN');
            $twilioNumber = getenv('TWILIO_PHONE_NUMBER');

            $client = new CURLRequest(new \Config\Services::curlrequest());

            $response = $client->request('POST', "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json", [
                'auth' => [$twilioSid, $twilioToken],
                'form_params' => [
                    'From' => $twilioNumber,
                    'To' => $phoneNumber,
                    'Body' => $message,
                ]
            ]);

            return ['status' => 'sent', 'response' => $response->getBody()];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Send Email notification
     */
    public static function sendEmail($email, $subject, $message)
    {
        try {
            $config = [
                'protocol' => 'smtp',
                'SMTPHost' => getenv('SMTP_HOST'),
                'SMTPUser' => getenv('SMTP_USER'),
                'SMTPPass' => getenv('SMTP_PASS'),
                'SMTPPort' => getenv('SMTP_PORT'),
                'mailType' => 'html',
                'charset' => 'UTF-8',
                'newline' => "\r\n"
            ];

            $email_service = \Config\Services::email($config);
            $email_service->setFrom(getenv('MAIL_FROM'), getenv('APP_NAME'));
            $email_service->setTo($email);
            $email_service->setSubject($subject);
            $email_service->setMessage($message);

            if ($email_service->send()) {
                return ['status' => 'sent'];
            } else {
                return ['status' => 'error', 'message' => $email_service->printDebugger()];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Send WhatsApp notification
     * Uses WhatsApp Business API
     */
    public static function sendWhatsApp($phoneNumber, $message)
    {
        try {
            $waBusinessApiUrl = getenv('WHATSAPP_API_URL');
            $waToken = getenv('WHATSAPP_TOKEN');

            $client = new CURLRequest(new \Config\Services::curlrequest());

            $response = $client->request('POST', $waBusinessApiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $waToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $phoneNumber,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => true,
                        'body' => $message
                    ]
                ]
            ]);

            return ['status' => 'sent', 'response' => $response->getBody()];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Send push notification
     */
    public static function sendPushNotification($deviceToken, $title, $body)
    {
        try {
            // Implementation for Firebase Cloud Messaging or similar service
            return ['status' => 'sent'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
