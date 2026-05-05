<?php

namespace App\Observers\WhatsappOfficial;

use App\Models\Master\MessageTemplate;
use App\Models\MetaAccount;
use App\Models\Store\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappOfficialServiceObserver
{

    protected $baseApiRequestEndpoint = 'https://graph.facebook.com/v22.0/';

    /*
    |--------------------------------------------------------------------------
    | 1.Connect Webhook for Subscriptions
    |--------------------------------------------------------------------------
    */

    public function connectToBaseWebhook($appId, $appSecret, $vendorUid)
    {

        $responseObject = new \stdClass();
        $response       = Http::post("{$this->baseApiRequestEndpoint}/{$appId}/subscriptions?access_token={$appId}|{$appSecret}", [
            'object'        => 'whatsapp_business_account',
            'fields'        => 'messages,message_template_quality_update,message_template_status_update,account_update',
            'callback_url'  => config('app.url') . '/api-app/waba/callback-url/' . $vendorUid,
            "verify_token"  => $vendorUid
        ]);


        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
        }

        return $responseObject;
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Debug Token
    |--------------------------------------------------------------------------
    */

    public function debugToken($appId, $appSecret, $inputToken)
    {

        $responseObject = new \stdClass();
        $response       = Http::get("{$this->baseApiRequestEndpoint}/debug_token", [
            'access_token'  => $appId . "|" . $appSecret,
            'input_token'   => $inputToken,
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'];
        }

        return $responseObject;
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Remove Existing and Replace Webhook
    |--------------------------------------------------------------------------
    */

    public function removeExistingAdnReplce($wabaId, $accessToken = null)
    {

        $responseObject = new \stdClass();

        $dataResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->delete("{$this->baseApiRequestEndpoint}/{$wabaId}/subscribed_apps");

        $response       = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$wabaId}/subscribed_apps");

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
        }

        return $responseObject;
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Get Phone Numbers
    |--------------------------------------------------------------------------
    */

    public function getPhones($wabaId, $accessToken = null)
    {

        $responseObject = new \stdClass();
        $fields         = 'display_phone_number,account_mode,certificate,name_status,new_certificate,new_name_status,verified_name,quality_rating,messaging_limit_tier';
        $response       = Http::get("https://graph.facebook.com/v22.0/{$wabaId}/phone_numbers", [
            'fields'        => $fields,
            'access_token'  => $accessToken,
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'];
        }

        return $responseObject;
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Disconnect Token
    |--------------------------------------------------------------------------
    */

    public function disconnectBaseWebhook($wabaId, $accessToken = null, $appId, $appSecret)
    {

        if ($wabaId) {
            $this->removeExistingAdnReplce($wabaId, $accessToken);
        }

        $responseObject = new \stdClass();


        $response       = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->delete("{$this->baseApiRequestEndpoint}/{$appId}/subscriptions?access_token={$appId}|{$appSecret}", [
            'object' => 'whatsapp_business_account',
            'fields' => 'messages,message_template_quality_update,message_template_status_update,account_update',
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'];
        }

        return $responseObject;
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Business Profile
    |--------------------------------------------------------------------------
    */

    public function getBusinessProfile($accessToken, $phoneNumberID)
    {
        $responseObject = new \stdClass();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("https://graph.facebook.com/v22.0/{$phoneNumberID}/whatsapp_business_profile", [
            'fields' => 'about,address,description,email,profile_picture_url,websites,vertical',
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'][0];
        }

        return $responseObject;
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Check Health
    |--------------------------------------------------------------------------
    */

    public function getHealthStatus($accessToken, $wabaID)
    {
        $responseObject = new \stdClass();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("https://graph.facebook.com/v22.0/{$wabaID}", [
            'fields' => 'health_status,name,account_review_status',
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback;
        }

        return $responseObject;
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Registeer Phone
    |--------------------------------------------------------------------------
    */

    public function registerPhoneNumber($accessToken, $phoneNumberID)
    {
        $phoneRegistration = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$phoneNumberID}/register", [
            'messaging_product' => 'whatsapp',
            'pin'               => '123456',
        ]);

        return $phoneRegistration;
    }


    /*
    |--------------------------------------------------------------------------
    | 9. Subcribe Apps
    |--------------------------------------------------------------------------
    */

    public function setSubcribeApps($webhookUrl, $uid, $accessToken, $wabaID)
    {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$wabaID}/subscribed_apps");


        Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$wabaID}/subscribed_apps", [
            "override_callback_uri"     => $webhookUrl,
            "verify_token"              => $uid
        ]);


        Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseApiRequestEndpoint}/{$wabaID}/subscribed_apps");
    }

    public function syncTokenFromFbLogin(Request $request)
    {
        $responseObject = new \stdClass();

        // Meta Embedded Signup: exchange code for business token using GET (not POST)
        // https://developers.facebook.com/docs/whatsapp/embedded-signup/onboarding-customers-as-a-solution-partner#step-1--exchange-the-token-code-for-a-business-token
        $response = Http::get("{$this->baseApiRequestEndpoint}oauth/access_token", [
            "client_id"         => platform_currency()->fb_app_id,
            "client_secret"     => platform_currency()->fb_app_secret,
            "code"              => $request->request_code,
        ]);

        \Log::info('WABA token exchange', [
            'status' => $response->status(),
            'body'   => substr($response->body(), 0, 300),
        ]);

        $callback = $response->json();

        if ($response->status() != 200) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->code    = $response->status();
            $responseObject->data->error->message = $callback['error']['message'] ?? 'Token exchange failed (HTTP ' . $response->status() . ')';
        } else {
            $responseObject->success = true;
            $responseObject->data    = $callback['access_token'] ?? null;

            if (empty($responseObject->data)) {
                // Some flows return token directly in different field
                $responseObject->data = $callback['token_type'] ? null : ($callback['access_token'] ?? null);
                if (empty($responseObject->data)) {
                    $responseObject->success = false;
                    $responseObject->data = new \stdClass();
                    $responseObject->data->error = new \stdClass();
                    $responseObject->data->error->code    = 0;
                    $responseObject->data->error->message = 'No access_token in response: ' . substr(json_encode($callback), 0, 200);
                }
            }
        }

        return $responseObject;
    }

    public function getSubscribedApps($wabaId, $accessToken)
    {
        $responseObject = new \stdClass();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseApiRequestEndpoint}/{$wabaId}/subscribed_apps");

        $callback = $response->json();

        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = $callback['data'] ?? [];
        }

        return $responseObject;
    }

    public function setSubcribeAppsEnhanced($webhookUrl, $uid, $accessToken, $wabaID)
    {
        $responseObject = new \stdClass();

        // Step 1: Subscribe WABA to app first (without override)
        $subscribeResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$wabaID}/subscribed_apps");

        $subscribeCallback = $subscribeResponse->json();

        // Check if subscription successful
        if ($subscribeResponse->status() != 200) {
            $responseObject->success = false;
            if (isset($subscribeCallback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $subscribeResponse->status();
                $responseObject->data->error->message = $subscribeCallback['error']['message'];
            }
            return $responseObject;
        }

        // Step 2: Now override the callback URI
        $overrideResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$wabaID}/subscribed_apps", [
            'override_callback_uri' => $webhookUrl,
            'verify_token'          => $uid
        ]);

        $overrideCallback = $overrideResponse->json();

        if ($overrideResponse->status() != 200) {
            $responseObject->success = false;
            if (isset($overrideCallback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $overrideResponse->status();
                $responseObject->data->error->message = $overrideCallback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = $overrideCallback;
        }

        return $responseObject;
    }

    public function subscribePhoneToWebhook($phoneNumberId, $accessToken)
    {
        $responseObject = new \stdClass();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseApiRequestEndpoint}/{$phoneNumberId}/subscribed_apps");

        $callback = $response->json();

        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = $callback;
        }

        return $responseObject;
    }

    public function sendMessageTemplate(Store $store, MessageTemplate $template, $variables)
    {
        $url = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$variables['phone_number']}/messages";

        $headers = $this->setHeaders($variables['access_token']);

        $requestData['messaging_product']   = 'whatsapp';
        $requestData['recipient_type']      = 'individual';
        $requestData['to']                  = $store->phone;
        $requestData['type']                = 'template';
        $requestData['template']            = $this->buildTemplateMessage($template);
        $responseObject                     = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success === true) {
        }

        return $responseObject;
    }

    function buildTemplateMessage(MessageTemplate $template)
    {
        $templateDetails = json_decode($template->message, true);

        $reformattedTemplate = [
            'header'        => collect($templateDetails['components'])->firstWhere('type', 'HEADER') ? [
                'format'        => $templateDetails['components'][0]['format'] ?? null,
                'text'          => $templateDetails['components'][0]['text'] ?? null,
                'parameters'    => [],
            ] : null,
            'body'          => collect($templateDetails['components'])->firstWhere('type', 'BODY') ? [
                'text'          => $templateDetails['components'][1]['text'] ?? null,
                'parameters'    => [],
            ] : null,
            'footer'        => collect($templateDetails['components'])->firstWhere('type', 'FOOTER') ? [
                'text'          => $templateDetails['components'][2]['text'] ?? null,
            ] : null,
            'buttons'       => collect($templateDetails['components'])->firstWhere('type', 'BUTTONS')['buttons'] ?? [],
            'media'         => null,
        ];

        return $reformattedTemplate;
    }

    public function getPhoneNumberId($accessToken, $wabaId)
    {

        $responseObject = new \stdClass();
        $fields         = 'display_phone_number,certificate,name_status,new_certificate,new_name_status,verified_name,quality_rating,messaging_limit_tier';
        $response       = Http::get("https://graph.facebook.com/v22.0/{$wabaId}/phone_numbers", [
            'fields'        => $fields,
            'access_token'  => $accessToken,
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'];
        }

        return $responseObject;
    }

    public function getPhoneNumberStatus($accessToken, $phoneNumberID)
    {
        $responseObject = new \stdClass();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("https://graph.facebook.com/v22.0/{$phoneNumberID}", [
            'fields' => 'status',
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback;
        }


        return $responseObject;
    }

    public function getAccountReviewStatus($accessToken, $wabaId)
    {
        $responseObject = new \stdClass();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("https://graph.facebook.com/v22.0/{$wabaId}", [
            'fields' => 'account_review_status',
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback;
        }

        return $responseObject;
    }

    //Set the headers for request
    public function setHeaders($accessToken)
    {
        return [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ];
    }

    private function sendHttpRequest($method, $url, $data = [], $headers = [])
    {
        $responseObject = new \stdClass();

        // Tentukan metode HTTP yang didukung
        $httpClient = Http::withHeaders($headers);

        // Cek metode HTTP untuk menambahkan data jika perlu
        if (isset($data) && in_array($method, ['POST', 'PUT', 'DELETE'])) {
            $response = $httpClient->$method($url, $data);
        } else {
            $response = $httpClient->get($url);
        }

        if ($response->status() != 200) {
            $responseObject->success    = false;
            $responseObject->error      = $response->status();
            $responseObject->message    = $response->json()['error']['message'];
        } else {
            $responseObject->success = true;
            $responseObject->data = $response->json();
        }


        return $responseObject;
    }

    function syncTemplates($accessToken, $wabaId, MetaAccount $meta)
    {
        $url = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$wabaId}/message_templates?fields=id,name,status,category,language,quality_score,rejected_reason,components&limit=100";

        $responseObject = new \stdClass();

        $response   = Http::withHeaders([
            'Authorization' => "OAuth {$accessToken}",
        ])->get($url);

        if ($response->status() != 200) {
            $responseObject->success = false;
            $responseObject->message = $response->json()['error']['message'];
        } else {
            $responseObject->success = true;
            $responseObject->message = 'Berhasil sinkronkan data';

            foreach ($response->json()['data'] as $templateData) {
                $template = MessageTemplate::where("meta_account_id", $meta->id)->where('meta_id', $templateData['id'])->first();

                if ($template) {
                    $qualityScore = null;
                    if (isset($templateData['quality_score']['score'])) {
                        $qualityScore = $templateData['quality_score']['score']; // GREEN, YELLOW, RED, UNKNOWN
                    }

                    // Preserve local HEADER if Meta doesn't include one
                    // (templates created without file upload have no HEADER in Meta)
                    $metaComponents = $templateData['components'] ?? [];
                    $metaHasHeader  = !empty(array_filter($metaComponents, fn($c) => ($c['type'] ?? '') === 'HEADER'));
                    if (!$metaHasHeader) {
                        $localData    = json_decode($template->message, true) ?? [];
                        $localComps   = $localData['components'] ?? [];
                        $localHeaders = array_values(array_filter($localComps, fn($c) => ($c['type'] ?? '') === 'HEADER'));
                        if (!empty($localHeaders)) {
                            array_unshift($metaComponents, $localHeaders[0]);
                            $templateData['components'] = $metaComponents;
                        }
                    }

                    $template->update([
                        'message'               => json_encode($templateData),
                        'waba_status_template'  => $templateData['status'],
                        'quality_score'         => $qualityScore,
                        'for_waba'              => 'yes'
                    ]);
                } else {
                    $qualityScore = isset($templateData['quality_score']['score'])
                        ? $templateData['quality_score']['score']
                        : null;
                    MessageTemplate::create([
                        'for_waba'              => 'yes',
                        'meta_account_id'       => $meta->id,
                        'meta_id'               => $templateData['id'],
                        'name'                  => $templateData['name'],
                        'category'              => $templateData['category'],
                        'lang'                  => $templateData['language'],
                        'message'               => json_encode($templateData),
                        'waba_status_template'  => $templateData['status'],
                        'quality_score'         => $qualityScore,
                        'created_by'            => my_user()->id,
                    ]);
                }
            }
        }

        return $responseObject;
    }

    function unSubscribeToWaba($wabaId, $accessToken)
    {
        $url                = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$wabaId}/subscribed_apps";
        $headers            = $this->setHeaders($accessToken);
        $responseObject     = $this->sendHttpRequest('DELETE', $url, NULL, $headers);

        return $responseObject;
    }

    public function sendTemplateMessage(Store $store, $templateContent, $variablesData = [])
    {
        $url = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$variablesData['phoneid']}/messages";

        $headers = $this->setHeaders($variablesData['access_token']);

        $requestData['messaging_product']   = 'whatsapp';
        $requestData['recipient_type']      = 'individual';
        $requestData['to']                  = $store->phone;
        $requestData['type']                = 'template';
        $requestData['template']            = $templateContent;

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success === true) {
            $messageId = $responseObject->data['messages'][0]['id'] ?? null;
            $response['status']      = 200;
            $response['message']     = 'success';
            $response['messageid']   = $messageId;
        } else {
            $response['status']      = $responseObject->error;
            $response['message']     = $responseObject->message;
            $response['messageid']   = null;
        }

        return $response;
    }

    public function sendTextMessage($phone, $textMessage, $variablesData = [])
    {
        $url        = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$variablesData['phoneid']}/messages";
        $headers    = $this->setHeaders($variablesData['access_token']);

        $requestData = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $phone,
            'type'              => 'text',
            'text'              => [
                'preview_url' => false, // true jika kamu ingin link otomatis jadi preview
                'body'        => $textMessage
            ],
        ];

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success === true) {
            $messageId = $responseObject->data['messages'][0]['id'] ?? null;

            return [
                'status'     => 200,
                'message'    => 'success',
                'messageid'  => $messageId
            ];
        } else {
            return [
                'status'  => $responseObject->error,
                'message' => $responseObject->message,
                'messageid' => null
            ];
        }
    }

    public function uploadMedia($accessToken, $fileUrl, $mimeType, $phoneId)
    {
        $fileContent = file_get_contents($fileUrl);

        if (!$fileContent) {
            return null;
        }

        $filename = basename(parse_url($fileUrl, PHP_URL_PATH));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->attach(
            'file',
            $fileContent,
            $filename
        )->post("https://graph.facebook.com/v22.0/{$phoneId}/media", [
            'messaging_product' => 'whatsapp',
            'type' => $mimeType,
        ]);

        $result = $response->json();
        return $result['id'] ?? null;
    }

    public function sendMediaMessage($phone, $mediaType, $mediaId, $caption = null, $fileName = null, $variablesData)
    {

        $url        = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$variablesData['phoneid']}/messages";
        $headers    = $this->setHeaders($variablesData['access_token']);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => $mediaType,
            $mediaType => [
                'id' => $mediaId,
            ]
        ];

        if ($caption && in_array($mediaType, ['image', 'video', 'document'])) {
            $payload[$mediaType]['caption'] = $caption;
        }

        if ($mediaType === 'document' && $fileName) {
            $payload[$mediaType]['filename'] = $fileName;
        }

        $responseObject = $this->sendHttpRequest('POST', $url, $payload, $headers);

        if ($responseObject->success === true) {
            $messageId = $responseObject->data['messages'][0]['id'] ?? null;

            return [
                'status'     => 200,
                'message'    => 'success',
                'messageid'  => $messageId
            ];
        } else {
            return [
                'status'  => $responseObject->error,
                'message' => $responseObject->message,
                'messageid' => null
            ];
        }
    }

    public function getWhatsappBusinessPhone($accessToken, $wabaId)
    {
        $responseObject = new \stdClass();
        $fields         = 'display_phone_number,certificate,name_status,new_certificate,new_name_status,verified_name,quality_rating,messaging_limit_tier';
        $response       = Http::get("https://graph.facebook.com/v22.0/{$wabaId}/phone_numbers", [
            'fields'        => $fields,
            'access_token'  => $accessToken,
        ]);

        $callback   = $response->json();
        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'][0];
        }


        return $responseObject;
    }

    public function subcribetionUpdate($accessToken, $wabaId)
    {
        $response       = Http::get("https://graph.facebook.com/v22.0/{$wabaId}}/subscribed_apps", [
            'access_token'  => $accessToken,
        ]);

        $responseObject = new \stdClass();
        $callback       = $response->json();

        if ($response->status() != 200) {
            $responseObject->success = false;
            if (isset($callback['error'])) {

                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response->status();
                $responseObject->data->error->message = $callback['error']['message'];
            }
        } else {
            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $callback['data'];
        }

        return $responseObject;
    }

    /*
    |--------------------------------------------------------------------------
    | Auto-discover WABA from user access token (when postMessage FINISH not received)
    | Tries multiple Meta API approaches in order
    |--------------------------------------------------------------------------
    */
    public function getWabaFromToken($accessToken)
    {
        $responseObject = new \stdClass();
        $apiBase        = $this->baseApiRequestEndpoint;
        $headers        = ['Authorization' => 'Bearer ' . $accessToken];

        \Log::info('WABA auto-discover: starting...');

        // Approach 1: GET /me/whatsapp_business_accounts (most direct)
        $r0 = Http::withHeaders($headers)->get("{$apiBase}me/whatsapp_business_accounts", [
            'fields' => 'id,name'
        ]);
        \Log::info('WABA discover /me/whatsapp_business_accounts', ['status' => $r0->status(), 'body' => substr($r0->body(), 0, 500)]);

        if ($r0->successful()) {
            $wabas = $r0->json('data', []);
            if (!empty($wabas)) {
                $responseObject->success  = true;
                $responseObject->waba_ids = array_column($wabas, 'id');
                $responseObject->waba_id  = $wabas[0]['id'];
                \Log::info('WABA auto-discovered via /me/whatsapp_business_accounts: ' . $wabas[0]['id']);
                return $responseObject;
            }
        }

        // Approach 2: GET /me/businesses?fields=whatsapp_business_accounts
        $r1 = Http::withHeaders($headers)->get("{$apiBase}me/businesses", [
            'fields' => 'id,name,whatsapp_business_accounts{id,name}'
        ]);
        \Log::info('WABA discover /me/businesses', ['status' => $r1->status(), 'body' => substr($r1->body(), 0, 500)]);

        if ($r1->successful()) {
            $businesses = $r1->json('data', []);
            foreach ($businesses as $business) {
                $wabas = $business['whatsapp_business_accounts']['data'] ?? [];
                if (!empty($wabas)) {
                    $responseObject->success  = true;
                    $responseObject->waba_ids = array_column($wabas, 'id');
                    $responseObject->waba_id  = $wabas[0]['id'];
                    \Log::info('WABA auto-discovered via /me/businesses: ' . $wabas[0]['id']);
                    return $responseObject;
                }
            }
        }

        // Approach 3: GET /me?fields=businesses{whatsapp_business_accounts}
        $r2 = Http::withHeaders($headers)->get("{$apiBase}me", [
            'fields' => 'businesses{whatsapp_business_accounts}'
        ]);
        \Log::info('WABA discover /me fields', ['status' => $r2->status(), 'body' => substr($r2->body(), 0, 500)]);

        if ($r2->successful()) {
            $businesses = $r2->json('businesses.data', []);
            foreach ($businesses as $biz) {
                $wabas = $biz['whatsapp_business_accounts']['data'] ?? [];
                if (!empty($wabas)) {
                    $responseObject->success  = true;
                    $responseObject->waba_ids = array_column($wabas, 'id');
                    $responseObject->waba_id  = $wabas[0]['id'];
                    \Log::info('WABA auto-discovered via /me fields: ' . $wabas[0]['id']);
                    return $responseObject;
                }
            }
        }

        // Approach 4: debug_token to get linked accounts
        $appId     = platform_currency()->fb_app_id;
        $appSecret = platform_currency()->fb_app_secret;
        $r3 = Http::get("{$apiBase}debug_token", [
            'input_token'  => $accessToken,
            'access_token' => "$appId|$appSecret",
        ]);
        \Log::info('WABA debug_token', ['status' => $r3->status(), 'body' => substr($r3->body(), 0, 500)]);

        if ($r3->successful()) {
            $granularScopes = $r3->json('data.granular_scopes', []);
            foreach ($granularScopes as $scope) {
                if (($scope['scope'] ?? '') === 'whatsapp_business_management') {
                    $wabaIds = $scope['target_ids'] ?? [];
                    if (!empty($wabaIds)) {
                        $responseObject->success  = true;
                        $responseObject->waba_ids = $wabaIds;
                        $responseObject->waba_id  = $wabaIds[0];
                        \Log::info('WABA auto-discovered via debug_token scope: ' . $wabaIds[0]);
                        return $responseObject;
                    }
                }
            }
        }

        \Log::warning('WABA auto-discover failed all approaches.', [
            'r0' => $r0->status() . ':' . substr($r0->body(), 0, 100),
            'r1' => $r1->status() . ':' . substr($r1->body(), 0, 100),
            'r2' => $r2->status() . ':' . substr($r2->body(), 0, 100),
            'r3' => $r3->status() . ':' . substr($r3->body(), 0, 100),
        ]);
        $responseObject->success    = false;
        $responseObject->waba_ids   = [];
        $responseObject->debug_r0   = substr($r0->body(), 0, 300);
        $responseObject->debug_r1   = substr($r1->body(), 0, 300);
        $responseObject->debug_r2   = substr($r2->body(), 0, 300);
        $responseObject->debug_r3   = substr($r3->body(), 0, 300);
        return $responseObject;
    }

}
