<?php namespace App\Controllers\PushControllers;

use CodeIgniter\RESTful\ResourceController;
use Pushok\AuthProvider;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;

class PushController extends ResourceController

{

    // send notification
    public function BasicPush()
    {
        $options = [
            'key_id' => 'T374QLBK9F', // The Key ID obtained from Apple developer account
            'team_id' => 'PDSG3QTZ95', // The Team ID obtained from Apple developer account
            'app_bundle_id' => 'dev.eb4marinefe.eb4solutions.fr', // The bundle ID for app obtained from Apple developer account
            'private_key_path' => __DIR__ . '/private_key.p8', // Path to private key
            'private_key_secret' => null // Private key secret
        ];

        // Be aware of thing that Token will stale after one hour, so you should generate it again.
        // Can be useful when trying to send pushes during long-running tasks
        $authProvider = AuthProvider\Token::create($options);

        $alert = Alert::create()->setTitle('eb4 Marine');
        $alert = $alert->setBody('hello thierry');

        $payload = Payload::create()->setAlert($alert);

        //set notification sound to default
        $payload->setSound('default');

        //add custom value to your notification, needs to be customized
        $payload->setCustomValue('key', 'value');

        $deviceTokens = ['49a4553cba4d6247a0113591aca9eae20f759ab9b5b0b0ba9ea2676b02f462d3'];

        $notifications = [];
        foreach ($deviceTokens as $deviceToken) {
            $notifications[] = new Notification($payload,$deviceToken);
        }

        // If you have issues with ssl-verification, you can temporarily disable it. Please see attached note.
        // Disable ssl verification
        // $client = new Client($authProvider, $production = false, [CURLOPT_SSL_VERIFYPEER=>false] );
        $client = new Client($authProvider, $production = false);
        $client->addNotifications($notifications);



        $responses = $client->push(); // returns an array of ApnsResponseInterface (one Response per Notification)

        foreach ($responses as $response) {
            // The device token
            $response->getDeviceToken();
            // A canonical UUID that is the unique ID for the notification. E.g. 123e4567-e89b-12d3-a456-4266554400a0
            $response->getApnsId();
            
            // Status code. E.g. 200 (Success), 410 (The device token is no longer active for the topic.)
            $response->getStatusCode();
            // E.g. The device token is no longer active for the topic.
            $response->getReasonPhrase();
            // E.g. Unregistered
            $response->getErrorReason();
            // E.g. The device token is inactive for the specified topic.
            $response->getErrorDescription();
            $response->get410Timestamp();
        }
            }

}