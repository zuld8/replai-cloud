<?php
// app/Services/ChatBot/RagService.php

namespace App\Services\ChatBot;

use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\FineTunnelDocument;
use App\Models\ChatBot\FineTunnelDocumentChunk;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use Intervention\Image\ImageManagerStatic as Image;
use Ramsey\Uuid\Uuid;

class RagService
{
    protected $openAiKey;
    protected $chunkSize = 500;
    protected $chunkOverlap = 50;

    public function __construct()
    {
        $this->openAiKey = Setting::withoutGlobalScopes()->whereNull('merchant_id')->value('open_ai_key');

        // Configure Intervention Image to use GD (not Imagick)
        Image::configure(['driver' => 'gd']);
    }

    /**
     * Process document (synchronous with detailed stats)
     */
    public function processDocument(FineTunnel $fineTunnel, UploadedFile $file): array
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Uuid::uuid4()->toString() . '.' . $extension;

        $filePath = $file->storeAs('uploads/rags', $filename);

        $document = FineTunnelDocument::create([
            'fine_tunnel_id' => $fineTunnel->id,
            'filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'processing'
        ]);

        $totalChunks = 0;
        $successfulChunks = 0;
        $failedChunks = 0;
        $totalImages = 0;

        try {
            // Extract text based on file type
            $extractedData = $this->extractText($file, $document);

            // Create chunks
            $chunks = $this->createChunks($extractedData);
            $totalChunks = count($chunks);

            // Generate embeddings and save chunks
            foreach ($chunks as $index => $chunkData) {
                try {
                    // Check if has image
                    if (isset($chunkData['image'])) {
                        $totalImages++;
                    }

                    $embedding = $this->getEmbedding($chunkData['content']);

                    FineTunnelDocumentChunk::create([
                        'document_id' => $document->id,
                        'content' => $chunkData['content'],
                        'image_path' => $chunkData['image'] ?? null,
                        'metadata' => $chunkData['metadata'],
                        'chunk_index' => $index,
                        'token_count' => $this->estimateTokens($chunkData['content']),
                        'embedding' => $embedding
                    ]);

                    $successfulChunks++;
                } catch (\Exception $e) {
                    $failedChunks++;
                    Log::error("Failed to process chunk {$index}: " . $e->getMessage());
                }
            }

            $document->update([
                'status' => 'completed',
                'total_chunks' => $totalChunks
            ]);
        } catch (\Exception $e) {
            $document->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            Log::error('RAG Document Processing Error: ' . $e->getMessage());
            throw $e;
        }

        return [
            'document' => $document->fresh(),
            'total_chunks' => $totalChunks,
            'successful_chunks' => $successfulChunks,
            'failed_chunks' => $failedChunks,
            'total_images' => $totalImages
        ];
    }

    /**
     * Extract text from different file types
     */
    protected function extractText(UploadedFile $file, FineTunnelDocument $document): array
    {
        $filePath = public_path($document->file_path);
        $extractedData = [];

        switch (strtolower($document->file_type)) {
            case 'pdf':
                $extractedData = $this->extractFromPdf($filePath, $document);
                break;

            case 'docx':
                $extractedData = $this->extractFromWord($filePath, $document);
                break;

            case 'xlsx':
            case 'xls':
            case 'csv':
                $extractedData = $this->extractFromExcel($filePath);
                break;

            default:
                throw new \Exception('Unsupported file type: ' . $document->file_type);
        }

        return $extractedData;
    }

    /**
     * Extract from PDF - TEXT ONLY (no page images to avoid Imagick dependency)
     * But extract embedded images if any
     */
    protected function extractFromPdf(string $filePath, FineTunnelDocument $document): array
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);

            $pages = $pdf->getPages();
            $extractedData = [];

            foreach ($pages as $pageNumber => $page) {
                $pageNum = $pageNumber + 1;

                // Extract text
                try {
                    $text = $page->getText();

                    if (!empty(trim($text))) {
                        $extractedData[] = [
                            'content' => trim($text),
                            'metadata' => [
                                'page' => $pageNum,
                                'type' => 'text',
                                'source' => 'pdf'
                            ]
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to extract text from PDF page {$pageNum}: " . $e->getMessage());
                }

                // Try to extract embedded images (XObject)
                try {
                    $imageData = $this->extractPdfEmbeddedImages($page, $pageNum, $document);
                    if (!empty($imageData)) {
                        $extractedData = array_merge($extractedData, $imageData);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to extract embedded images from PDF page {$pageNum}: " . $e->getMessage());
                }
            }

            if (empty($extractedData)) {
                throw new \Exception('Tidak dapat mengekstrak teks dari PDF. File mungkin berisi hanya gambar atau terproteksi.');
            }

            return $extractedData;
        } catch (\Exception $e) {
            Log::error("PDF extraction error: " . $e->getMessage());
            throw new \Exception('Gagal memproses PDF: ' . $e->getMessage());
        }
    }

    /**
     * Extract embedded images from PDF page using smalot/pdfparser
     */
    protected function extractPdfEmbeddedImages($page, int $pageNum, FineTunnelDocument $document): array
    {
        $imageData = [];

        try {
            // Get XObjects (images) from the page
            $xObjects = $page->getXObjects();

            if (empty($xObjects)) {
                return $imageData;
            }

            // Create directory if not exists
            $imageDir = public_path("uploads/folders/{$document->fineTunnel->business->id}/ai-training");
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }

            $imageIndex = 0;
            foreach ($xObjects as $xObjectName => $xObject) {
                try {
                    // Get image details
                    $details = $xObject->getDetails();

                    // Check if it's an image
                    if (!isset($details['Subtype']) || $details['Subtype'] !== 'Image') {
                        continue;
                    }

                    // Get image content
                    $content = $xObject->getContent();

                    if (empty($content)) {
                        continue;
                    }

                    // **FIX: Validate if content is actually an image**
                    if (!$this->isValidImageData($content)) {
                        Log::info("Skipping non-image XObject on page {$pageNum}");
                        continue;
                    }

                    $imageIndex++;

                    // Determine image format from filter
                    $filter = $details['Filter'] ?? '';
                    $extension = 'jpg'; // default

                    if (is_array($filter)) {
                        $filter = implode(' ', $filter);
                    }

                    if (strpos($filter, 'DCTDecode') !== false) {
                        $extension = 'jpg';
                    } elseif (strpos($filter, 'FlateDecode') !== false) {
                        $extension = 'png';
                    } elseif (strpos($filter, 'JPXDecode') !== false) {
                        $extension = 'jp2';
                    } elseif (strpos($filter, 'CCITTFaxDecode') !== false) {
                        $extension = 'tiff';
                    } else {
                        // Try to detect from content
                        $detectedExt = $this->detectImageExtension($content);
                        if ($detectedExt) {
                            $extension = $detectedExt;
                        } else {
                            Log::info("Unknown image filter '{$filter}' on page {$pageNum}, skipping");
                            continue;
                        }
                    }

                    // Generate unique filename
                    $imageName = Uuid::uuid4()->toString() . '_page' . $pageNum . '_img' . $imageIndex . '.' . $extension;
                    $imagePath = "uploads/folders/{$document->fineTunnel->business->id}/ai-training/" . $imageName;
                    $fullImagePath = public_path($imagePath);

                    // Save image
                    file_put_contents($fullImagePath, $content);

                    // **FIX: Verify saved file is actually an image**
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $fullImagePath);
                    finfo_close($finfo);

                    if (!str_starts_with($mimeType, 'image/')) {
                        Log::warning("Extracted file is not an image (MIME: {$mimeType}), deleting: {$imagePath}");
                        unlink($fullImagePath);
                        $imageIndex--;
                        continue;
                    }

                    // Try to optimize (skip if fails)
                    try {
                        $this->optimizeImage($fullImagePath);
                    } catch (\Exception $e) {
                        Log::warning("Could not optimize PDF image (non-critical): " . $e->getMessage());
                        // Don't fail, just skip optimization
                    }

                    $imageUrl = asset($imagePath);

                    $imageData[] = [
                        'content' => "Gambar dari halaman {$pageNum}\nURL: {$imageUrl}",
                        'image' => $imagePath,
                        'metadata' => [
                            'page' => $pageNum,
                            'type' => 'image',
                            'source' => 'pdf',
                            'image_url' => $imageUrl,
                            'image_index' => $imageIndex,
                            'mime_type' => $mimeType
                        ]
                    ];

                    Log::info("Extracted embedded image from PDF page {$pageNum}: {$imagePath} (MIME: {$mimeType})");
                } catch (\Exception $e) {
                    Log::warning("Failed to extract individual image from PDF page {$pageNum}: " . $e->getMessage());
                    $imageIndex--; // Don't count failed extraction
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to extract embedded images from PDF page {$pageNum}: " . $e->getMessage());
        }

        return $imageData;
    }

    protected function isValidImageData(string $data): bool
    {
        if (empty($data) || strlen($data) < 10) {
            return false;
        }

        // Check image signatures (magic numbers)
        $signatures = [
            "\xFF\xD8\xFF",           // JPEG
            "\x89PNG\r\n\x1a\n",      // PNG
            "GIF87a",                 // GIF87
            "GIF89a",                 // GIF89
            "BM",                     // BMP
            "\x00\x00\x01\x00",       // ICO
            "II*\x00",                // TIFF (Intel)
            "MM\x00*",                // TIFF (Motorola)
        ];

        foreach ($signatures as $signature) {
            if (substr($data, 0, strlen($signature)) === $signature) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect image extension from content
     */
    protected function detectImageExtension(string $data): ?string
    {
        if (empty($data) || strlen($data) < 10) {
            return null;
        }

        // Check for JPEG
        if (substr($data, 0, 3) === "\xFF\xD8\xFF") {
            return 'jpg';
        }

        // Check for PNG
        if (substr($data, 0, 8) === "\x89PNG\r\n\x1a\n") {
            return 'png';
        }

        // Check for GIF
        if (substr($data, 0, 6) === "GIF87a" || substr($data, 0, 6) === "GIF89a") {
            return 'gif';
        }

        // Check for BMP
        if (substr($data, 0, 2) === "BM") {
            return 'bmp';
        }

        // Check for WebP
        if (substr($data, 8, 4) === "WEBP") {
            return 'webp';
        }

        return null;
    }

    /**
     * Optimize image size using GD (built-in PHP) - IMPROVED
     */
    protected function optimizeImage(string $imagePath): void
    {
        try {
            // Check if file exists and is readable
            if (!file_exists($imagePath) || !is_readable($imagePath)) {
                throw new \Exception("Image file not accessible: {$imagePath}");
            }

            // Verify it's actually an image
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $imagePath);
            finfo_close($finfo);

            if (!str_starts_with($mimeType, 'image/')) {
                throw new \Exception("File is not an image (MIME: {$mimeType})");
            }

            // Try to create image with Intervention
            $img = Image::make($imagePath);

            // Resize if too large (max width 1200px)
            if ($img->width() > 1200) {
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Compress (quality 80%)
            $img->save($imagePath, 80);

            Log::info("Optimized image: {$imagePath}");
        } catch (\Exception $e) {
            // If optimization fails, it's not critical - just log and continue
            Log::warning("Failed to optimize image {$imagePath}: " . $e->getMessage());
            // Re-throw only if it's a critical error (file doesn't exist)
            if (strpos($e->getMessage(), 'not accessible') !== false) {
                throw $e;
            }
        }
    }




    /**
     * Extract from Word with Images - ENHANCED
     */
    protected function extractFromWord(string $filePath, FineTunnelDocument $document): array
    {
        try {
            $extractedData = [];
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if ($extension === 'docx') {
                $zip = new \ZipArchive();

                if ($zip->open($filePath) !== true) {
                    throw new \Exception('Tidak dapat membuka file DOCX');
                }

                // Extract document.xml
                $xmlContent = $zip->getFromName('word/document.xml');

                if ($xmlContent === false) {
                    $zip->close();
                    throw new \Exception('File DOCX tidak valid');
                }

                // Parse XML and extract text
                $xml = simplexml_load_string($xmlContent);
                if ($xml === false) {
                    $zip->close();
                    throw new \Exception('Gagal parse XML dari DOCX');
                }

                // Register namespaces
                $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                // Extract text from paragraphs
                $paragraphs = $xml->xpath('//w:p');
                $sectionText = '';
                $sectionIndex = 0;

                foreach ($paragraphs as $paragraph) {
                    $textNodes = $paragraph->xpath('.//w:t');
                    $paragraphText = '';

                    foreach ($textNodes as $textNode) {
                        $paragraphText .= (string)$textNode;
                    }

                    if (!empty(trim($paragraphText))) {
                        $sectionText .= trim($paragraphText) . "\n";

                        if (strlen($sectionText) > 1000) {
                            $extractedData[] = [
                                'content' => trim($sectionText),
                                'metadata' => [
                                    'section' => ++$sectionIndex,
                                    'type' => 'text',
                                    'source' => 'docx'
                                ]
                            ];
                            $sectionText = '';
                        }
                    }
                }

                // Add remaining text
                if (!empty(trim($sectionText))) {
                    $extractedData[] = [
                        'content' => trim($sectionText),
                        'metadata' => [
                            'section' => ++$sectionIndex,
                            'type' => 'text',
                            'source' => 'docx'
                        ]
                    ];
                }

                // Extract images from DOCX
                $imageData = $this->extractDocxImages($zip, $document);
                $extractedData = array_merge($extractedData, $imageData);

                $zip->close();
            } else {
                throw new \Exception('Format DOC tidak didukung. Silakan convert ke DOCX terlebih dahulu.');
            }

            if (empty($extractedData)) {
                throw new \Exception('Tidak dapat mengekstrak teks dari Word. File mungkin kosong.');
            }

            return $extractedData;
        } catch (\Exception $e) {
            Log::error("Word extraction error: " . $e->getMessage());
            throw new \Exception('Gagal memproses Word: ' . $e->getMessage());
        }
    }

    /**
     * Extract images from DOCX - IMPROVED VALIDATION
     */
    protected function extractDocxImages(\ZipArchive $zip, FineTunnelDocument $document): array
    {
        $imageData = [];

        try {
            // Create directory if not exists
            $imageDir = public_path("uploads/folders/{$document->fineTunnel->business->id}/ai-training");
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }

            // Look for images in word/media/ folder
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                // Check if it's an image in media folder
                if (strpos($filename, 'word/media/') === 0) {
                    $imageContent = $zip->getFromName($filename);

                    if ($imageContent === false || empty($imageContent)) {
                        continue;
                    }

                    // **FIX: Validate if content is actually an image**
                    if (!$this->isValidImageData($imageContent)) {
                        Log::info("Skipping non-image file in DOCX media: {$filename}");
                        continue;
                    }

                    // Get file extension
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    // Validate extension
                    if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'])) {
                        // Try to detect from content
                        $detectedExt = $this->detectImageExtension($imageContent);
                        if (!$detectedExt) {
                            Log::info("Unknown image type in DOCX: {$filename}");
                            continue;
                        }
                        $ext = $detectedExt;
                    }

                    // Generate unique filename
                    $imageName = Uuid::uuid4()->toString() . '.' . $ext;
                    $imagePath = "uploads/folders/{$document->fineTunnel->business->id}/ai-training/" . $imageName;
                    $fullImagePath = public_path($imagePath);

                    // Save image
                    file_put_contents($fullImagePath, $imageContent);

                    // **FIX: Verify saved file is actually an image**
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $fullImagePath);
                    finfo_close($finfo);

                    if (!str_starts_with($mimeType, 'image/')) {
                        Log::warning("Extracted DOCX file is not an image (MIME: {$mimeType}), deleting: {$imagePath}");
                        unlink($fullImagePath);
                        continue;
                    }

                    // Optimize image (skip if fails)
                    try {
                        $this->optimizeImage($fullImagePath);
                    } catch (\Exception $e) {
                        Log::warning("Could not optimize DOCX image (non-critical): " . $e->getMessage());
                    }

                    $imageUrl = asset($imagePath);

                    $imageData[] = [
                        'content' => "Gambar dari dokumen\nURL: {$imageUrl}",
                        'image' => $imagePath,
                        'metadata' => [
                            'type' => 'image',
                            'source' => 'docx',
                            'original_name' => basename($filename),
                            'image_url' => $imageUrl,
                            'mime_type' => $mimeType
                        ]
                    ];

                    Log::info("Extracted image from DOCX: {$imagePath} (MIME: {$mimeType})");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to extract DOCX images: " . $e->getMessage());
        }

        return $imageData;
    }

    /**
     * Extract from Excel
     */
    protected function extractFromExcel(string $filePath): array
    {
        try {
            $spreadsheet = SpreadsheetIOFactory::load($filePath);
            $extractedData = [];

            foreach ($spreadsheet->getAllSheets() as $sheetIndex => $sheet) {
                $sheetData = $sheet->toArray(null, true, true, true);

                if (empty($sheetData)) {
                    continue;
                }

                $headers = array_shift($sheetData);

                $headers = array_filter($headers, function ($h) {
                    return !is_null($h) && $h !== '';
                });

                if (empty($headers)) {
                    continue;
                }

                foreach ($sheetData as $rowIndex => $row) {
                    $rowText = [];

                    foreach ($row as $colKey => $cell) {
                        if (!empty($cell) && isset($headers[$colKey])) {
                            $header = $headers[$colKey];
                            $rowText[] = "{$header}: {$cell}";
                        }
                    }

                    if (!empty($rowText)) {
                        $extractedData[] = [
                            'content' => implode(", ", $rowText),
                            'metadata' => [
                                'sheet' => $sheet->getTitle(),
                                'row' => $rowIndex,
                                'type' => 'data',
                                'source' => 'excel'
                            ]
                        ];
                    }
                }
            }

            if (empty($extractedData)) {
                throw new \Exception('Tidak dapat mengekstrak data dari Excel. File mungkin kosong.');
            }

            return $extractedData;
        } catch (\Exception $e) {
            Log::error("Excel extraction error: " . $e->getMessage());
            throw new \Exception('Gagal memproses Excel: ' . $e->getMessage());
        }
    }

    /**
     * Create chunks from extracted data
     */
    protected function createChunks(array $extractedData): array
    {
        $chunks = [];

        foreach ($extractedData as $data) {
            $content = $data['content'];
            $metadata = $data['metadata'];

            // If it's an image, don't chunk it - keep as single chunk
            if (isset($metadata['type']) && $metadata['type'] === 'image') {
                $chunks[] = [
                    'content' => $content,
                    'metadata' => $metadata,
                    'image' => $data['image'] ?? null
                ];
                continue;
            }

            // For text content, apply chunking
            $contentLength = mb_strlen($content);
            $chunkCharSize = $this->chunkSize * 4;

            if ($contentLength <= $chunkCharSize) {
                $chunks[] = [
                    'content' => $content,
                    'metadata' => $metadata,
                    'image' => null
                ];
            } else {
                $offset = 0;
                $chunkIndex = 0;

                while ($offset < $contentLength) {
                    $chunkContent = mb_substr($content, $offset, $chunkCharSize);

                    $chunks[] = [
                        'content' => $chunkContent,
                        'metadata' => array_merge($metadata, [
                            'chunk_part' => ++$chunkIndex
                        ]),
                        'image' => null
                    ];

                    $offset += $chunkCharSize - ($this->chunkOverlap * 4);
                }
            }
        }

        return $chunks;
    }

    // ... rest of the methods remain the same (getEmbedding, searchSimilarChunks, estimateTokens)

    public function getEmbedding(string $text): array
    {
        if (empty($this->openAiKey)) {
            throw new \Exception('OpenAI API key tidak ditemukan');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->openAiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/embeddings', [
                    'model' => 'text-embedding-3-small',
                    'input' => $text
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new \Exception('OpenAI Error: ' . ($error['error']['message'] ?? 'Unknown error'));
            }

            $result = $response->json();
            return $result['data'][0]['embedding'];
        } catch (\Exception $e) {
            Log::error('Embedding generation error: ' . $e->getMessage());
            throw new \Exception('Gagal generate embedding: ' . $e->getMessage());
        }
    }

    // public function searchSimilarChunks(FineTunnel $fineTunnel, string $query, int $topK = 5): array
    // {
    //     $queryEmbedding = $this->getEmbedding($query);

    //     $chunks = FineTunnelDocumentChunk::whereHas('document', function ($q) use ($fineTunnel) {
    //         $q->where('fine_tunnel_id', $fineTunnel->id)
    //             ->where('status', 'completed');
    //     })->get();

    //     if ($chunks->isEmpty()) {
    //         return [];
    //     }

    //     $similarities = [];
    //     foreach ($chunks as $chunk) {
    //         $similarity = $chunk->cosineSimilarity($queryEmbedding);
    //         $similarities[] = [
    //             'chunk' => $chunk,
    //             'similarity' => $similarity
    //         ];
    //     }

    //     usort($similarities, function ($a, $b) {
    //         return $b['similarity'] <=> $a['similarity'];
    //     });

    //     return array_slice($similarities, 0, $topK);
    // }

    public function searchSimilarChunks(
        FineTunnel $fineTunnel,
        string $query,
        int $topK = 5,
        float $minSimilarity = 0.7
    ): array {
        try { 
            $queryEmbedding = $this->getEmbedding($query);
 
            
            $keywords = $this->extractKeywords($query);

            $chunks = FineTunnelDocumentChunk::whereHas('document', function ($q) use ($fineTunnel) {
                $q->where('fine_tunnel_id', $fineTunnel->id)
                    ->where('status', 'completed');
            })
                ->when(!empty($keywords), function ($q) use ($keywords) { 
                    $q->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->orWhere('content', 'LIKE', "%{$keyword}%");
                        }
                    });
                })
                ->with('document')
                ->get();
 
            if ($chunks->isEmpty()) { 
                $chunks = FineTunnelDocumentChunk::whereHas('document', function ($q) use ($fineTunnel) {
                    $q->where('fine_tunnel_id', $fineTunnel->id)
                        ->where('status', 'completed');
                })
                    ->with('document')
                    ->limit(100) // Safety limit
                    ->get();
            }

            // 3. Calculate cosine similarity
            $results = [];
            foreach ($chunks as $chunk) {
                $similarity = $chunk->cosineSimilarity($queryEmbedding);
 
                if ($similarity >= $minSimilarity) {
                    $results[] = [
                        'chunk' => $chunk,
                        'similarity' => $similarity,
                        'has_image' => !empty($chunk->image_path),
                        'keyword_overlap' => $this->calculateKeywordOverlap($query, $chunk->content)
                    ];
                }
            }
 
            usort($results, function ($a, $b) { 
                $scoreA = ($a['similarity'] * 0.7) + ($a['keyword_overlap'] * 0.3);
                $scoreB = ($b['similarity'] * 0.7) + ($b['keyword_overlap'] * 0.3);

                return $scoreB <=> $scoreA;
            });
 
            if ($this->queryNeedsImage($query)) {
                usort($results, function ($a, $b) {
                    if ($a['has_image'] && !$b['has_image']) return -1;
                    if (!$a['has_image'] && $b['has_image']) return 1;
                    return $b['similarity'] <=> $a['similarity'];
                });
            }
  
            return array_slice($results, 0, $topK);
        } catch (\Exception $e) {
            Log::error('RAG Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract keywords from query (remove stopwords)
     */
    protected function extractKeywords(string $query): array
    {
        $stopwords = [
            'yang',
            'adalah',
            'dari',
            'di',
            'ke',
            'untuk',
            'pada',
            'dengan',
            'atau',
            'dan',
            'ini',
            'itu',
            'nya',
            'apa',
            'bagaimana',
            'kapan',
            'dimana',
            'mengapa',
            'siapa',
            'apakah',
            'berapa'
        ];

        // Lowercase and split
        $words = preg_split('/\s+/', strtolower($query));

        // Remove stopwords and short words
        $keywords = array_filter($words, function ($word) use ($stopwords) {
            return !in_array($word, $stopwords) && strlen($word) > 2;
        });

        return array_values($keywords);
    }

    /**
     * Calculate keyword overlap between query and content
     */
    protected function calculateKeywordOverlap(string $query, string $content): float
    {
        $queryKeywords = $this->extractKeywords($query);
        $contentLower = strtolower($content);

        if (empty($queryKeywords)) {
            return 0.0;
        }

        $matches = 0;
        foreach ($queryKeywords as $keyword) {
            if (strpos($contentLower, $keyword) !== false) {
                $matches++;
            }
        }

        return $matches / count($queryKeywords);
    }

    /**
     * Detect if query might need image
     */
    protected function queryNeedsImage(string $query): bool
    {
        $imageKeywords = [
            'gambar',
            'foto',
            'tampilan',
            'lihat',
            'screenshot',
            'contoh',
            'visualisasi',
            'diagram',
            'grafik',
            'chart',
            'desain',
            'warna',
            'bentuk',
            'logo'
        ];

        $queryLower = strtolower($query);

        foreach ($imageKeywords as $keyword) {
            if (strpos($queryLower, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    public function searchWithMultiQuery(
        FineTunnel $fineTunnel,
        string $originalQuery,
        int $topK = 5
    ): array {
        try {
            // 1. Generate query variations using GPT
            $queryVariations = $this->generateQueryVariations($originalQuery);

            // Add original query
            array_unshift($queryVariations, $originalQuery);

            // 2. Search for each variation
            $allResults = [];
            $seenChunkIds = [];

            foreach ($queryVariations as $query) { 
                $results = $this->searchSimilarChunks(
                    $fineTunnel,
                    $query,
                    $topK * 2 
                );
 
                foreach ($results as $result) {
                    $chunkId = $result['chunk']->id;

                    // Deduplicate
                    if (!isset($seenChunkIds[$chunkId])) {
                        $allResults[] = $result;
                        $seenChunkIds[$chunkId] = true;
                    }
                }
            }

            // 3. Rerank all results
            usort($allResults, function ($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });

            // 4. Return top K from all queries
            return array_slice($allResults, 0, $topK);
        } catch (\Exception $e) {
            Log::error('Multi-query RAG error: ' . $e->getMessage());
            // Fallback to single query
            return $this->searchSimilarChunks($fineTunnel, $originalQuery, $topK);
        }
    }

    protected function generateQueryVariations(string $query): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->openAiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Generate 2 variations of the given question. Each variation should ask the same thing but with different wording. Output as JSON array of strings.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $query
                        ]
                    ],
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => [
                            'name' => 'query_variations',
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'variations' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string']
                                    ]
                                ],
                                'required' => ['variations']
                            ]
                        ]
                    ],
                    'temperature' => 0.7
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $variations = json_decode($result['choices'][0]['message']['content'], true);
                return $variations['variations'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::warning('Query variation generation failed: ' . $e->getMessage());
            return [];
        }
    }



    protected function estimateTokens(string $text): int
    {
        return (int) ceil(mb_strlen($text) / 4);
    }
}
