<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        $path = env('GOOGLE_SHEETS_CREDENTIALS_PATH', storage_path('app/google-credentials.json'));
        if (!$this->spreadsheetId || !file_exists($path)) return;
        $this->client = new Client();
        $this->client->setAuthConfig($path);
        $this->client->addScope(Sheets::SPREADSHEETS);
        $this->service = new Sheets($this->client);
    }

    public function isConfigured(): bool
    {
        return $this->service !== null && $this->spreadsheetId !== null;
    }

    public function updateOnboardingStep(string $name, string $email, string $step, string $regAt, array $extra = []): void
    {
        if (!$this->isConfigured()) { Log::warning('Google Sheets not configured'); return; }
        try {
            $this->initializeHeaders();
            $ex = $this->findRowByEmail($email);
            $map = [
                'registration' => ['Inscription', '1/3'],
                'portal_created' => ['Portail cree', '2/3'],
                'subscription' => ['Abonne', '3/3'],
            ];
            $s = $map[$step] ?? [$step, '?'];
            $row = [
                $name, $email, $regAt, $s[0], $s[1],
                $step === 'registration' ? now()->format('d/m/Y H:i') : ($ex[5] ?? ''),
                $step === 'portal_created' ? now()->format('d/m/Y H:i') : ($ex[6] ?? ''),
                $step === 'subscription' ? now()->format('d/m/Y H:i') : ($ex[7] ?? ''),
                ($extra['portal_name'] ?? '') ?: ($ex[8] ?? ''),
                ($extra['plan_name'] ?? '') ?: ($ex[9] ?? ''),
                ($extra['amount'] ?? '') ?: ($ex[10] ?? ''),
                $ex[11] ?? '',
            ];
            $body = new \Google\Service\Sheets\ValueRange(['values' => [$row]]);
            $opts = ['valueInputOption' => 'USER_ENTERED'];
            if ($ex !== null) {
                $r = 'Onboarding!A' . $ex['_row_index'] . ':L' . $ex['_row_index'];
                $this->service->spreadsheets_values->update($this->spreadsheetId, $r, $body, $opts);
            } else {
                $this->service->spreadsheets_values->append($this->spreadsheetId, 'Onboarding!A:L', $body, $opts);
            }
            Log::info('Google Sheets updated', ['email' => $email, 'step' => $step]);
        } catch (\Exception $e) {
            Log::error('Google Sheets failed', ['error' => $e->getMessage(), 'email' => $email]);
        }
    }

    protected function findRowByEmail(string $email): ?array
    {
        try {
            $v = $this->service->spreadsheets_values->get($this->spreadsheetId, 'Onboarding!A:L')->getValues();
            if (!$v) return null;
            foreach ($v as $i => $row) {
                if ($i === 0) continue;
                if (isset($row[1]) && strtolower($row[1]) === strtolower($email)) {
                    $row['_row_index'] = $i + 1;
                    return $row;
                }
            }
        } catch (\Exception $e) {
            Log::error('Sheets lookup failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    public function initializeHeaders(): void
    {
        if (!$this->isConfigured()) return;
        try {
            $resp = $this->service->spreadsheets_values->get($this->spreadsheetId, 'Onboarding!A1:L1');
            if (empty($resp->getValues())) {
                $h = [['Nom', 'Email', 'Date inscription', 'Etape actuelle', 'Progression', 'Date inscription', 'Date creation portail', 'Date abonnement', 'Nom du portail', 'Offre souscrite', 'Montant', 'Notes']];
                $this->service->spreadsheets_values->update($this->spreadsheetId, 'Onboarding!A1:L1', new \Google\Service\Sheets\ValueRange(['values' => $h]), ['valueInputOption' => 'USER_ENTERED']);
            }
        } catch (\Exception $e) {
            Log::error('Sheets headers failed', ['error' => $e->getMessage()]);
        }
    }
}
