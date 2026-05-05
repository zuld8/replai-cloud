<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;
use simplehtmldom\HtmlWeb;
use Illuminate\Support\Facades\Http;

class GoogleUserSheetService
{
    /**
     * Parse Google Spreadsheet URL dan konversi ke array data
     *
     * @param string $url URL Google Spreadsheet
     * @return array Data hasil parsing
     * @throws \Exception Jika parsing gagal
     */
    public function parseSpreadsheet(string $url): array
    {
        // Validasi URL Google Spreadsheet
        if (!$this->isValidGoogleSheetsUrl($url)) {
            throw new \Exception('URL bukan Google Spreadsheet yang valid');
        }

        // Convert URL ke format yang bisa di-parse
        $parseableUrl = $this->convertToParseableUrl($url);

        Log::info('Parsing Google Spreadsheet', [
            'original_url' => $url,
            'parseable_url' => $parseableUrl
        ]);

        // Parsing berdasarkan format URL
        if (str_contains($parseableUrl, 'pubhtml')) {
            return $this->parsePubHtml($parseableUrl);
        } elseif (str_contains($parseableUrl, 'output=csv')) {
            return $this->parseCsv($parseableUrl);
        } elseif (str_contains($parseableUrl, 'output=tsv')) {
            return $this->parseCsv($parseableUrl, "\t"); // TSV
        }

        throw new \Exception('Format spreadsheet tidak didukung');
    }

    /**
     * Validasi apakah URL adalah Google Spreadsheet yang valid
     *
     * @param string $url
     * @return bool
     */
    public function isValidGoogleSheetsUrl(string $url): bool
    {
        return str_contains($url, 'docs.google.com/spreadsheets');
    }

    /**
     * Konversi URL Google Spreadsheet ke format yang bisa di-parse
     *
     * @param string $url
     * @return string
     */
    protected function convertToParseableUrl(string $url): string
    {
        // Jika sudah dalam format yang bisa di-parse, kembalikan apa adanya
        if (
            str_contains($url, 'output=csv') ||
            str_contains($url, 'output=tsv') ||
            str_contains($url, 'pubhtml')
        ) {
            return $url;
        }

        // Extract spreadsheet ID dari URL
        preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);

        if (empty($matches[1])) {
            throw new \Exception('Tidak dapat mengekstrak ID spreadsheet dari URL');
        }

        $spreadsheetId = $matches[1];

        // Extract gid jika ada
        $gid = '0'; // default
        if (preg_match('/[#&]gid=([0-9]+)/', $url, $gidMatches)) {
            $gid = $gidMatches[1];
        }

        // Return URL dalam format CSV export
        return "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";
    }

    /**
     * Parse HTML public spreadsheet
     *
     * @param string $url
     * @return array
     * @throws \Exception
     */
    protected function parsePubHtml(string $url): array
    {
        try {
            // Coba konversi ke CSV terlebih dahulu karena lebih reliable
            $csvUrl = $this->convertPubHtmlToCsv($url);
            if ($csvUrl) {
                try {
                    Log::info('Mencoba parsing CSV dari pubhtml URL', ['csv_url' => $csvUrl]);
                    return $this->parseCsv($csvUrl);
                } catch (\Exception $csvError) {
                    Log::warning('CSV parsing gagal, lanjut ke HTML parsing', ['error' => $csvError->getMessage()]);
                }
            }

            // Jika CSV gagal, coba parse HTML
            $client = new HtmlWeb();
            $dom = $client->load($url);

            if (!$dom) {
                throw new \Exception('Gagal memuat HTML dari URL');
            }

            // Log struktur HTML untuk debugging
            Log::info('HTML Structure Debug', [
                'url' => $url,
                'html_snippet' => substr($dom->outertext, 0, 1000)
            ]);

            $raw = [];

            // Coba berbagai selector untuk menemukan tabel
            $tableSelectors = [
                'table.waffle',           // Google Sheets waffle table
                'table.grid',             // Grid table
                'table',                  // Generic table
                'div.waffle table',       // Table inside waffle div
                'div.sheets-table table', // Table inside sheets div
                '.waffle table',          // Class waffle table
                'tbody table'             // Table inside tbody
            ];

            $table = null;
            foreach ($tableSelectors as $selector) {
                $table = $dom->find($selector, 0);
                if ($table) {
                    Log::info("Tabel ditemukan dengan selector: {$selector}");
                    break;
                }
            }

            if (!$table) {
                // Coba cari div yang mengandung data spreadsheet dengan class waffle
                $waffleDiv = $dom->find('div.waffle', 0);
                if ($waffleDiv) {
                    Log::info('Ditemukan waffle div, mencoba parsing alternatif');
                    return $this->parseWaffleDiv($waffleDiv);
                }

                // Coba cari semua tbody dan tr untuk parsing manual
                $tbody = $dom->find('tbody', 0);
                if ($tbody) {
                    $rows = $tbody->find('tr');
                    if (!empty($rows)) {
                        Log::info('Ditemukan tbody dengan rows, mencoba parsing');
                        return $this->parseTableRows($rows);
                    }
                }

                throw new \Exception('Tidak ditemukan tabel atau struktur data dalam HTML spreadsheet');
            }

            // Parse tabel yang ditemukan
            $rows = $table->find('tr');
            if (empty($rows)) {
                $rows = $table->find('tbody tr');
            }

            return $this->parseTableRows($rows);
        } catch (\Exception $e) {
            Log::error('Error parsing HTML spreadsheet', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal parsing HTML spreadsheet: ' . $e->getMessage());
        }
    }

    /**
     * Konversi URL pubhtml ke CSV URL
     *
     * @param string $pubhtmlUrl
     * @return string|null
     */
    protected function convertPubHtmlToCsv(string $pubhtmlUrl): ?string
    {
        try {
            // Extract spreadsheet ID dari URL pubhtml
            if (preg_match('/\/spreadsheets\/d\/e\/([a-zA-Z0-9-_]+)/', $pubhtmlUrl, $matches)) {
                $spreadsheetKey = $matches[1];

                // Extract gid jika ada
                $gid = '0'; // default
                if (preg_match('/[#&?]gid=([0-9]+)/', $pubhtmlUrl, $gidMatches)) {
                    $gid = $gidMatches[1];
                }

                // Buat CSV URL dengan format yang benar untuk published spreadsheet
                return "https://docs.google.com/spreadsheets/d/e/{$spreadsheetKey}/pub?gid={$gid}&single=true&output=csv";
            }

            // Jika format ID biasa (bukan published)
            if (preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $pubhtmlUrl, $matches)) {
                $spreadsheetId = $matches[1];

                $gid = '0';
                if (preg_match('/[#&?]gid=([0-9]+)/', $pubhtmlUrl, $gidMatches)) {
                    $gid = $gidMatches[1];
                }

                return "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Gagal konversi pubhtml ke CSV', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parse rows dari tabel HTML
     *
     * @param array $rows
     * @return array
     */
    protected function parseTableRows(array $rows): array
    {
        $raw = [];

        foreach ($rows as $row) {
            $cols = $row->find('td');

            // Jika tidak ada td, coba th (untuk header)
            if (empty($cols)) {
                $cols = $row->find('th');
            }

            $rowData = [];

            foreach ($cols as $col) {
                $cellValue = trim($col->plaintext);
                // Pastikan nilai adalah string yang valid
                $rowData[] = is_string($cellValue) ? $cellValue : (string)$cellValue;
            }

            // Hanya tambahkan baris yang memiliki data
            if (!empty($rowData) && !empty(array_filter($rowData, fn($val) => !empty(trim($val))))) {
                $raw[] = $rowData;
            }
        }

        if (count($raw) < 1) {
            throw new \Exception('Tidak ada data yang ditemukan dalam tabel');
        }

        // Jika hanya ada 1 baris, anggap sebagai header saja
        if (count($raw) < 2) {
            Log::warning('Hanya ditemukan header, tidak ada data');
            return [];
        }

        return $this->formatData($raw);
    }

    /**
     * Parse waffle div Google Spreadsheet
     *
     * @param $waffleDiv
     * @return array
     */
    protected function parseWaffleDiv($waffleDiv): array
    {
        $raw = [];

        // Cari semua div yang berisi cell data dalam waffle
        $cells = $waffleDiv->find('div[id*="cell"]');

        if (empty($cells)) {
            // Alternatif: cari berdasarkan class atau pattern lain
            $cells = $waffleDiv->find('div.s0, div.s1, div.s2, div.s3, div.s4');
        }

        if (empty($cells)) {
            // Coba cari tbody dan tr dalam waffle
            $tbody = $waffleDiv->find('tbody', 0);
            if ($tbody) {
                $rows = $tbody->find('tr');
                if (!empty($rows)) {
                    return $this->parseTableRows($rows);
                }
            }

            throw new \Exception('Tidak dapat menemukan struktur data dalam waffle div');
        }

        // Group cells by row (berdasarkan posisi atau attribute)
        $rowData = [];
        foreach ($cells as $cell) {
            $cellText = trim($cell->plaintext);
            if (!empty($cellText)) {
                $rowData[] = $cellText;
            }
        }

        // Karena sulit menentukan struktur baris dari div cells,
        // coba estimate berdasarkan jumlah data
        if (count($rowData) < 2) {
            throw new \Exception('Data tidak cukup ditemukan dalam waffle div');
        }

        // Asumsi data pertama adalah header, sisanya adalah rows
        // Ini adalah estimasi sederhana
        $raw[] = [$rowData[0]]; // header
        for ($i = 1; $i < count($rowData); $i++) {
            $raw[] = [$rowData[$i]];
        }

        return $this->formatData($raw);
    }

    /**
     * Parse CSV/TSV spreadsheet
     *
     * @param string $url
     * @param string $delimiter
     * @return array
     * @throws \Exception
     */
    protected function parseCsv(string $url, string $delimiter = ','): array
    {
        try {
            // Gunakan HTTP client Laravel untuk mendapatkan konten
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                throw new \Exception("HTTP Error: " . $response->status());
            }

            $content = $response->body();
            $lines = explode(PHP_EOL, $content);
            $lines = array_filter($lines, fn($line) => trim($line) !== '');

            if (count($lines) < 2) {
                throw new \Exception('CSV harus memiliki header dan minimal satu baris data');
            }

            $raw = [];
            foreach ($lines as $line) {
                try {
                    $row = str_getcsv($line, $delimiter);
                    if (!empty($row)) {
                        // Pastikan semua nilai dalam row adalah string
                        $row = array_map(fn($value) => (string)$value, $row);
                        $raw[] = $row;
                    }
                } catch (\Exception $csvParseException) {
                    Log::warning("Error parsing CSV line", [
                        'line' => $line,
                        'error' => $csvParseException->getMessage()
                    ]);
                    // Skip baris yang bermasalah
                    continue;
                }
            }

            return $this->formatData($raw);
        } catch (\Exception $e) {
            Log::error('Error parsing CSV spreadsheet', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal parsing CSV spreadsheet: ' . $e->getMessage());
        }
    }

    /**
     * Format raw data menjadi array associative
     *
     * @param array $raw
     * @return array
     */
    protected function formatData(array $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $headers = array_shift($raw); // Ambil baris pertama sebagai header
        $formatted = [];

        // Pastikan headers adalah array string
        $headers = array_map(fn($header) => trim((string)$header), $headers);

        foreach ($raw as $row) {
            $assoc = [];
            foreach ($headers as $i => $key) {
                $value = isset($row[$i]) ? trim((string)$row[$i]) : null;
                $assoc[$key] = $value;
            }

            // Hanya tambahkan baris yang tidak kosong
            if (!empty(array_filter($assoc, fn($val) => !empty($val)))) {
                $formatted[] = $assoc;
            }
        }

        return $formatted;
    }

    /**
     * Konversi data spreadsheet menjadi format yang lebih clean
     * Mengembalikan data mentah dari spreadsheet tanpa wrapper tambahan
     *
     * @param array $data Data hasil parsing spreadsheet
     * @return array Array data spreadsheet yang sudah dibersihkan
     */
    public function convertToCleanData(array $data): array
    {
        // Langsung return data yang sudah di-parse
        // Tambahkan timestamp import untuk tracking
        return array_map(function ($row) {
            $row['imported_at'] = now()->toDateTimeString();
            return $row;
        }, $data);
    }

    /**
     * Konversi data spreadsheet menjadi format prompt yang sesuai (legacy method)
     * Method ini tetap ada untuk backward compatibility
     *
     * @param array $data Data hasil parsing spreadsheet
     * @return array Array prompt yang sudah diformat
     */
    public function convertToPrompts(array $data): array
    {
        $prompts = [];

        foreach ($data as $row) {
            // Cari kolom yang mengandung prompt
            $prompt = $this->extractPrompt($row);
            $description = $this->extractDescription($row);
            $category = $this->extractCategory($row);

            if (!empty($prompt)) {
                $prompts[] = [
                    'prompt' => $prompt,
                    'description' => $description,
                    'category' => $category,
                    'imported_at' => now()->toDateTimeString()
                ];
            }
        }

        return $prompts;
    }

    /**
     * Extract prompt dari baris data
     *
     * @param array $row
     * @return string|null
     */
    protected function extractPrompt(array $row): ?string
    {
        // Cari kolom yang mungkin berisi prompt
        $promptKeys = ['prompt', 'pesan', 'message', 'text', 'content', 'konten'];

        foreach ($promptKeys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
        }

        // Jika tidak ada kolom yang cocok, ambil nilai non-empty pertama
        foreach ($row as $value) {
            if (!empty(trim($value))) {
                return trim($value);
            }
        }

        return null;
    }

    /**
     * Extract description dari baris data
     *
     * @param array $row
     * @return string|null
     */
    protected function extractDescription(array $row): ?string
    {
        $descKeys = ['description', 'deskripsi', 'keterangan', 'desc', 'detail'];

        foreach ($descKeys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
        }

        return null;
    }

    /**
     * Extract category dari baris data
     *
     * @param array $row
     * @return string|null
     */
    protected function extractCategory(array $row): ?string
    {
        $categoryKeys = ['category', 'kategori', 'type', 'tipe', 'jenis'];

        foreach ($categoryKeys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
        }

        return null;
    }

    /**
     * Get preview data dari spreadsheet (maksimal 5 baris)
     *
     * @param string $url
     * @return array
     */
    public function getPreviewData(string $url): array
    {
        $data = $this->parseSpreadsheet($url);
        return array_slice($data, 0, 5); // Ambil maksimal 5 baris untuk preview
    }

    /**
     * Validate spreadsheet structure untuk prompt import
     *
     * @param string $url
     * @return array
     */
    public function validateSpreadsheetStructure(string $url): array
    {
        try {
            $data = $this->getPreviewData($url);

            if (empty($data)) {
                return [
                    'valid' => false,
                    'message' => 'Spreadsheet kosong atau tidak dapat dibaca'
                ];
            }

            $firstRow = $data[0];
            $promptFound = false;

            // Cek apakah ada kolom yang bisa dijadikan prompt
            $promptKeys = ['prompt', 'pesan', 'message', 'text', 'content', 'konten'];
            foreach ($promptKeys as $key) {
                if (array_key_exists($key, $firstRow)) {
                    $promptFound = true;
                    break;
                }
            }

            return [
                'valid' => true,
                'message' => 'Spreadsheet valid untuk import',
                'preview' => $data,
                'prompt_column_found' => $promptFound,
                'columns' => array_keys($firstRow),
                'origin_total_rows' => count($this->parseSpreadsheet($url)),
                'total_rows' => count($data)
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Method untuk test parsing URL spesifik dan debugging
     *
     * @param string $url
     * @return array
     */
    public function testParseUrl(string $url): array
    {
        try {
            Log::info('Testing URL parsing', ['url' => $url]);

            // Test konversi ke CSV
            $csvUrl = $this->convertPubHtmlToCsv($url);
            $result = [
                'original_url' => $url,
                'csv_url' => $csvUrl,
                'csv_test' => null,
                'html_structure' => null,
                'final_result' => null
            ];

            // Test CSV URL jika berhasil dikonversi
            if ($csvUrl) {
                try {
                    $csvResponse = Http::timeout(10)->get($csvUrl);
                    $result['csv_test'] = [
                        'status' => $csvResponse->status(),
                        'successful' => $csvResponse->successful(),
                        'content_length' => strlen($csvResponse->body()),
                        'content_preview' => substr($csvResponse->body(), 0, 500)
                    ];

                    if ($csvResponse->successful() && !empty($csvResponse->body())) {
                        $result['final_result'] = $this->parseCsv($csvUrl);
                        return $result;
                    }
                } catch (\Exception $csvError) {
                    $result['csv_test'] = ['error' => $csvError->getMessage()];
                }
            }

            // Test struktur HTML
            try {
                $client = new HtmlWeb();
                $dom = $client->load($url);

                if ($dom) {
                    $result['html_structure'] = [
                        'html_length' => strlen($dom->outertext),
                        'tables_found' => count($dom->find('table')),
                        'waffle_divs_found' => count($dom->find('div.waffle')),
                        'tbody_found' => count($dom->find('tbody')),
                        'tr_found' => count($dom->find('tr')),
                        'td_found' => count($dom->find('td')),
                        'th_found' => count($dom->find('th')),
                        'html_snippet' => substr($dom->outertext, 0, 1000)
                    ];

                    $result['final_result'] = $this->parsePubHtml($url);
                }
            } catch (\Exception $htmlError) {
                $result['html_structure'] = ['error' => $htmlError->getMessage()];
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'url' => $url
            ];
        }
    }

    /**
     * SUPER SIMPLE: Get ALL data, let GPT handle filtering
     */
    public function getAllDataForGPT(string $publishedUrl): array
    {
        $allData = $this->fetchPublishedSheetData($publishedUrl);

        // Limit data untuk avoid token limit (max 50 rows)
        $limitedData = array_slice($allData, 0, 50);

        // Clean data
        return $this->cleanDataForGPT($limitedData);
    }

    public function fetchPublishedSheetData(string $publishedUrl): array
    {
        try {
            // Convert pubhtml URL to CSV export URL
            $csvUrl = $this->convertToCsvUrl($publishedUrl);

            $cacheKey = "published_sheet_" . md5($csvUrl);

            // Try cache first (10 minutes for published sheets)
            // $cachedData = Cache::get($cacheKey);
            // if ($cachedData !== null) {
            //     Log::info('Published sheet data retrieved from cache');
            //     return $cachedData;
            // }

            // Fetch CSV data via HTTP
            $response = Http::timeout(30)->get($csvUrl);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch published sheet data');
            }

            // Parse CSV
            $csvData = $response->body();
            $parsedData = $this->parseCsvData($csvData);

            // Cache for 10 minutes
            // Cache::put($cacheKey, $parsedData, 600);

            Log::info('Published sheet data fetched successfully', [
                'rows_count' => count($parsedData),
                'url' => $csvUrl
            ]);

            return $parsedData;
        } catch (\Exception $error) {
            Log::error('Error fetching published sheet: ' . $error->getMessage());
            throw new \Exception('Gagal mengambil data dari Google Sheets: ' . $error->getMessage());
        }
    }

    /**
     * Convert pubhtml URL to CSV export URL
     */
    private function convertToCsvUrl(string $pubhtmlUrl): string
    {
        // Extract spreadsheet ID from pubhtml URL
        // Format: https://docs.google.com/spreadsheets/d/e/2PACX-1vTv-Gk6DhY.../pubhtml

        if (preg_match('/\/spreadsheets\/d\/e\/([a-zA-Z0-9-_]+)/', $pubhtmlUrl, $matches)) {
            $sheetId = $matches[1];
            return "https://docs.google.com/spreadsheets/d/e/{$sheetId}/pub?output=csv";
        }

        // Fallback: try to replace pubhtml with pub?output=csv
        return str_replace('pubhtml', 'pub?output=csv', $pubhtmlUrl);
    }

    /**
     * Parse CSV data menjadi array
     */
    private function parseCsvData(string $csvData): array
    {
        $lines = explode("\n", $csvData);

        if (empty($lines)) {
            return [];
        }

        // Parse headers
        $headers = str_getcsv($lines[0]);
        $data = [];

        // Parse data rows
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $row = str_getcsv($line);
            $rowData = [];

            foreach ($headers as $index => $header) {
                $rowData[trim($header)] = $row[$index] ?? '';
            }

            // Skip completely empty rows
            if (!empty(array_filter($rowData))) {
                $data[] = $rowData;
            }
        }

        return $data;
    }

     /**
     * Clean data untuk GPT processing
     */
    private function cleanDataForGPT(array $data): array
    {
        return array_map(function($row) {
            $cleaned = [];
            foreach ($row as $key => $value) {
                $cleanValue = trim($value);
                if (!empty($cleanValue) && $cleanValue !== '-' && $cleanValue !== 'N/A') {
                    $cleaned[trim($key)] = $cleanValue;
                }
            }
            return $cleaned;
        }, $data);
    }
    
}