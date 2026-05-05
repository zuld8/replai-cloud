<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\FineTunnel;
use App\Models\InternalSetting;
use App\Services\ChatBot\RagService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleUserSheetService;

class OpenAiServiceObserver
{


    protected $openAiUrl;
    protected $googleSheetService;
    protected $ragService;

    public function __construct()
    {
        $this->googleSheetService   = new GoogleUserSheetService();
        $this->ragService          = new RagService();
        $this->openAiUrl            = 'https://api.openai.com/v1/chat/';
    }


    public function detectIntent(
        FineTunnel $fineTunnel,
        String $openAiKey,
        $message = '',
        $conversations = null,
        String $modeAi = 'standart',
        $media = null,
    ) {

        $messages           = [];
        $description        = $fineTunnel->description . PHP_EOL . PHP_EOL;
        $usingMedia         = $media == null ? false : true;
        $conversationsData  = [];

        foreach ($conversations as $conversation) {
            if ($conversation->message != null && $conversation->message != '') {
                $conversationsData[] = [
                    'role'          => $conversation->from == 'user' ? 'user' : 'assistant',
                    'content'       => $conversation->message,
                ];
            }
        }

        if ($message != '') {
            $ragContext = $this->getRagContext($fineTunnel, $message, $conversationsData);
 
            if (!empty($ragContext)) {
                $description .= "\n\n=== INFORMASI DARI DOKUMEN ===\n";
                $description .= $ragContext;
                $description .= "\n=== AKHIR INFORMASI DOKUMEN ===\n";
                $description .= "\nGunakan informasi di atas untuk menjawab pertanyaan user jika relevan. Jika informasi tidak cukup, jawab berdasarkan pengetahuan umummu.";
            }
        }


        // Build training from gsheet
        $dataFromSheets     = [];
        foreach ($fineTunnel->gsheets as $sheet) {
            $sheetData = $this->googleSheetService->getAllDataForGPT($sheet->url);
            $dataFromSheets = array_merge($dataFromSheets, $sheetData);
        }

        if (count($dataFromSheets) > 0) {
            $description .= "Berikut data dari google sheet sebagai tambahan informasi :" . $this->formatAllDataForGPT($dataFromSheets);
        }

        if (!$usingMedia && $modeAi == 'advanced') {
            $modeAi = 'standart';
        }

        if ($description) {
            $messages[] = [
                'role'    => 'system',
                'content' => $description . prompt_detect_intent()
            ];
        }

        foreach ($conversations as $conversation) {
            if ($conversation->message != null && $conversation->message != '') {
                $messages[] = [
                    'role'      => $conversation->from == 'user' ? 'user' : 'assistant',
                    'content'   => $conversation->message,
                ];
            }
        }

        $schema = [
            "type" => "object",
            "properties" => [
                "intent" => [
                    "type" => "string",
                    "enum" => [
                        "media",
                        "check_shipping",
                        "question"
                    ]
                ],
                "multi_data_training_title" => [
                    "type"  => "array",
                    "items" => [
                        "type" => "string"
                    ]
                ],
                "medias" => [
                    "type"  => "array",
                    "items" => [
                        "type" => "string",
                        "format" => "uri"
                    ]
                ],
                "message" => [
                    "type" => ["string", "null"],
                    "description" => "Optional long/multi-line message. Null if not provided."
                ],
                "address"           => ["type" => ["string", "null"]],
                "pos_code"          => ["type" => ["string", "null"]],
                "quantity"          => ["type" => ["integer"]],
            ],
            "required" => ["intent"]
        ];

        if ($modeAi != 'standart' && $media != null) {
            $content = [];
            if ($message != '') {
                $content[] = [
                    'type' => 'text',
                    'text' => $message,
                ];
            }

            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => asset($media),
                ],
            ];

            $messages[] = [
                'role'    => 'user',
                'content' => $content,
            ];
        } else {
            if ($message != '') {
                $messages[] = [
                    'role'    => 'user',
                    'content' => $message,
                ];
            }
        }


        Log::info('keisini - ' . $modeAi . ' - ' . $media);
        Log::info(json_encode($messages[1]));

        return Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . $openAiKey,
            'Content-Type'  => 'application/json',
        ])->post($this->openAiUrl . '/completions', [
            'model'         => $modeAi === 'standart' ? 'gpt-4o-mini' : ($usingMedia ? 'gpt-4o' : 'gpt-4.1-mini'),
            'messages'      => $messages,
            'temperature'       => 0,
            'response_format'   => [
                'type'              => 'json_schema',
                'json_schema'   => [
                    'name'          => 'intent_entity',
                    'schema'        => $schema
                ]
            ]
        ]);
    }

    private function getRagContext($fineTunnel, string $query, $conversations = []): string
    {
        try { 
            $similarChunks = $this->ragService->searchSimilarChunks($fineTunnel, $query, $conversations, 5);

            if (empty($similarChunks)) {
                return '';
            }

            $context = '';

            foreach ($similarChunks as $item) {
                $chunk = $item['chunk'];
                $similarity = $item['similarity'];
  
                if ($similarity < 0.4) {
                    continue;
                }

                $metadata = $chunk->metadata;
                $source = "Dokumen: {$chunk->document->filename}";

                // Add source info
                if (isset($metadata['page'])) {
                    $source .= ", Halaman: {$metadata['page']}";
                } elseif (isset($metadata['sheet'])) {
                    $source .= ", Sheet: {$metadata['sheet']}, Baris: {$metadata['row']}";
                } elseif (isset($metadata['section'])) {
                    $source .= ", Bagian: {$metadata['section']}";
                }

                $similarityPercent = round($similarity * 100, 1);
                $source .= " (Relevansi: {$similarityPercent}%)";

                $context .= "\n[{$source}]\n";
                $context .= $chunk->content . "\n";

                if (isset($metadata['image_url'])) {
                    $context .= "URL Gambar: {$metadata['image_url']}\n";
                }
            }
 
            return $context;
        } catch (\Exception $e) {
            Log::error('RAG Context Error: ' . $e->getMessage());
            return '';
        }
    }


    public function checkAudioData($url, $openAiKey)
    {
        $publicPath = parse_url($url, PHP_URL_PATH);
        $localPath = public_path($publicPath);

        if (!file_exists($localPath)) {
            return [
                'status'  => false,
                'message' => 'File tidak ditemukan'
            ];
        }

        $response = Http::timeout(60)->withToken($openAiKey)
            ->attach('file', file_get_contents($localPath), 'audio.ogg')
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1'
            ]);

        if (!$response->successful()) {
            return [
                'status'  => false,
                'message' => $response->json()['error']['message'] ?? 'API Gagal'
            ];
        }

        return [
            'status'  => true,
            'message' => $response->json()['text']
        ];
    }



    public function getQuestion(String $openAiKey, $message = '', $description, $conversations = null, String $modeAi = 'standart', $dataTraining = [], $checkOngkir, $media = null)
    {

        $usingMedia = $media == null ? false : true;
        $messages = [];

        if ($description) {
            $messages[] = [
                'role'    => 'system',
                'content' => $description . " Jika kamu ingin mengirim link, kirim HANYA teks link-nya saja, misalnya: https://whatsmail.org. Jangan tambahkan karakter seperti [], {}, (), <>, atau markdown apapun di sekitarnya."
            ];
        }

        if ($conversations != null) {
            foreach ($conversations as $conversation) {
                if ($conversation->message != null && $conversation->message != '') {
                    $messages[] = [
                        'role'      => $conversation->from == 'user' ? 'user' : 'assistant',
                        'content'   => $conversation->message,
                    ];
                }
            }
        }

        if (!$usingMedia && $modeAi == 'advanced') {
            $modeAi = 'standart';
        }


        if ($message != '' || $media !== null) {
            if ($modeAi != 'standart' && $media != null) {
                $content = [];
                if ($message != '') {
                    $content[] = [
                        'type' => 'text',
                        'text' => $message,
                    ];
                }

                $content[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => asset($media),
                    ],
                ];

                $messages[] = [
                    'role'    => 'user',
                    'content' => count($content) === 1 ? $content[0] : $content,
                ];
            } else {
                $messages[] = [
                    'role'    => 'user',
                    'content' => $message,
                ];
            }
        }


        $systemPrompt = "";

        $tools  = [];
        $checkOngkirMessage = '';
        $checkOngkirTools   = [];


        if (!empty($checkOngkir['status']) && $checkOngkir['status'] === true) {


            if (!empty($checkOngkir['region'])) {
                $checkOngkirMessage .= " Asal pengiriman berada di wilayah " . $checkOngkir['region'] . ".";
                $checkOngkirMessage .= " Berat persatuan barang " . $checkOngkir['weight'] . " Kg.";
            }

            $checkOngkirTools = [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'check_ongkir',
                        'description' => 'Digunakan untuk mengecek kelengkapan data untuk kebutuhan check ongkos kirim dari wilayah asal ( kota, kecamatan, desa / kelurahan ) tujuan menggunakan layanan dari RajaOngkir.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'zip_code' => [
                                    'type' => 'string',
                                    'description' => 'Kode pos alamat tujuan',
                                ],

                                'message' => [
                                    'type' => 'string',
                                    'description' => 'Digunakan jika data kode pos tidak di sertakan. Minta pengguna menyebutkan kode pos secara jelas.',
                                ],
                                'response_data' => [
                                    'type' => 'string',
                                    'description' => 'Jika alamat lengkap tersedia, tampilkan hasil pengecekan ongkir berdasarkan data tersebut.',
                                ],
                                'quantity' => [
                                    'type' => 'integer',
                                    'description' => 'isi 1 apabila user tidak menyebutkan berapa jumlah quantity pesanan atau yang akan di beli',
                                ],
                                'weight' => [
                                    'type' => 'integer',
                                    'description' => 'kalikan dari berat ' . $checkOngkir['weight'] . ', dengan jumlah quantity yang di beli user ',
                                ],
                            ],
                            'required' => []
                        ]
                    ]
                ]
            ];
        }


        if (!empty($checkOngkir['status']) && $checkOngkir['status'] === true && $checkOngkirMessage !== '') {
            $systemPrompt .= ' ' . $checkOngkirMessage;
        } else {
            $systemPrompt .= " Perhatian: Kamu tidak diizinkan untuk melakukan pengecekan ongkos kirim atau menghitung biaya pengiriman. Jika pengguna menanyakan tentang ongkos kirim atau pengiriman maka jawab sesuai intrusi pada Deskripsi Data Training";
        }

        Log::info($systemPrompt);
        if (!$usingMedia) {
            $tools = [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'media_data',
                        'description' => 'List kan URL media yang akan dikirim.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'urls' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'string']
                                ],
                                'message' => [
                                    'type' => 'string',
                                    'description' => 'Pesan setelah mengirim media, contoh: berikut media yang diminta.',
                                ],
                            ],
                            'required' => ['urls']
                        ]
                    ]
                ]
            ];

            $listTraining = [];
            foreach ($dataTraining as $training) {
                $listTraining[] = [
                    'type' => 'function',
                    'function' => [
                        'name'          => 'training_' . $training->id,
                        'description'   => $training->answer,
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type'          => 'string',
                                    'description'   => 'Gunakan fungsi ini untuk menjawab pertanyaan yang relevan terkait topic ini.',
                                ],
                            ]
                        ]
                    ]
                ];
            }

            $tools = array_merge($tools, $listTraining, $checkOngkirTools);
        }

        $listTraining = [];
        foreach ($dataTraining as $training) {
            $listTraining[] = [
                'type' => 'function',
                'function' => [
                    'name'          => 'training_' . $training->id,
                    'description'   => $training->answer,
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => [
                                'type'          => 'string',
                                'description'   => 'Gunakan fungsi ini untuk menjawab pertanyaan yang relevan terkait topic ini.',
                            ],
                        ]
                    ]
                ]
            ];
        }


        return Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . $openAiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $modeAi === 'standart' ? 'gpt-4o-mini' : ($usingMedia ? 'gpt-4o' : 'gpt-4.1-mini'),
            'messages'  => $messages,
            'tools'     => $tools,
            'tool_choice' => 'auto'
        ]);
    }

    public function forFollowUp(String $openAiKey, $prompt = null, $description, $conversations = null, String $modeAi = 'standart')
    {

        $messages   = [];

        if ($description) {
            $messages[] = [
                'role'      => 'system',
                'content'   => $description,
            ];
        }


        if ($conversations != null) {
            foreach ($conversations as $conversation) {
                if ($conversation->message != null && $conversation->message != '') {
                    $messages[] = [
                        'role'      => $conversation->from == 'user' ? 'user' : 'assistant',
                        'content'   => $conversation->message,
                    ];
                }
            }
        }

        if ($prompt != '' && $prompt != null) {
            $messages[] = [
                'role'      => 'system',
                'content'   => $prompt,
            ];
        }


        return Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . $openAiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model'     => $modeAi == 'standart' ? 'gpt-4o-mini' : 'gpt-3.5-turbo',
            'messages'  => $messages,
        ]);
    }


    private function formatAllDataForGPT(array $data): string
    {
        $formatted = [];

        foreach ($data as $index => $item) {
            $itemText = [];
            foreach ($item as $key => $value) {
                if (!empty(trim($value))) {
                    $itemText[] = "{$key}: {$value}";
                }
            }
            $formatted[] = ($index + 1) . ". " . implode(", ", $itemText);
        }

        return implode("\n", $formatted);
    }



    public function getFileTun(FineTunnel $fineTunnel, String $openAiKey)
    {
        return Http::timeout(60)->withHeaders([
            'Authorization' => "Bearer $openAiKey",
        ])->get('https://api.openai.com/v1/files/' . $fineTunnel->fine_tunnel_id);
    }

    public function deleteFileTun(FineTunnel $fineTunnel, String $openAiKey)
    {
        return Http::timeout(60)->withHeaders([
            'Authorization' => "Bearer $openAiKey",
        ])->delete('https://api.openai.com/v1/files/' . $fineTunnel->fine_tunnel_id);
    }

    public function uploadFileTune(FineTunnel $fineTunnel, String $openAiKey, String $jsonName)
    {
        return Http::timeout(60)->withHeaders([
            'Authorization' => "Bearer $openAiKey",
        ])->attach(
            'file',
            file_get_contents($fineTunnel->filejson),
            '' . $jsonName . ''
        )->post('https://api.openai.com/v1/files', [
            'purpose' => 'fine-tune',
        ]);
    }

    public function getAskWithTunnel(FineTunnel $fineTunnel, String $openAiKey, $message)
    {
        return Http::timeout(60)->withHeaders([
            'Authorization'     => "Bearer $openAiKey",
            'Content-Type'      => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $fineTunnel->fine_tunnel_id,
            'messages' => [
                ['role' => 'user', 'content' => $message]
            ],
        ]);
    }


    public function fineTunnelProcess(FineTunnel $fineTunnel, String $openAiKey)
    {
        return Http::timeout(60)->withHeaders([
            'Authorization'     => "Bearer $openAiKey",
            'Content-Type'      => 'application/json',
        ])->post('https://api.openai.com/v1/fine_tunes', [
            'training_file' => $fineTunnel->fine_tunnel_id,
            'model' => 'gpt-4o-mini'
        ]);
    }

    public function calculateCompletions($modelAi, $token)
    {
        $settingConfiguration     = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $usageCredit    = 0;

        if ($modelAi == 'standart') {
            $creditPer250Tokens = 1;
            $tokensPerCredit = $settingConfiguration->credit_token_basic;

            $usageCredit = ceil($token / $tokensPerCredit) * $creditPer250Tokens;
        }

        if ($modelAi == 'advanced') {
            $creditPer250Tokens = 1;
            $tokensPerCredit = $settingConfiguration->credit_token_advance;

            $usageCredit = ceil($token / $tokensPerCredit) * $creditPer250Tokens;
        }

        return $usageCredit;
    }
}
