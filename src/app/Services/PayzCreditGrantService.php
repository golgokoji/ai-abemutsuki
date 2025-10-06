<?php
namespace App\Services;

use App\Models\CreditHistory;
use App\Models\AbelaboUserSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;

class PayzCreditGrantService
{
    public function run(?string $range = null): array
    {
        return Cache::lock('payz-import-lock', 300)->block(5, function () use ($range) {
            $rows = $this->getSheetValues($range);
            if (empty($rows)) {
                return ['checked'=>0,'granted'=>0,'skipped'=>0,'errors'=>0,'reason'=>'no rows'];
            }

            $header = array_map('trim', $rows[0]);
            $idx = [
                'order_id' => $this->findCol($header, ['注文ID','Order ID','order_id']),
                'amount'   => $this->findCol($header, ['金額（税込）','金額','Amount','amount']),
                'email'    => $this->findCol($header, ['メール','メールアドレス','Email','email']),
                'name'     => $this->findCol($header, ['購入者名','名前','Name']),
                'tel'      => $this->findCol($header, ['電話','電話番号','TEL','tel']),
                'note'     => $this->findCol($header, ['商材名','商品名','備考','note']),
                'date'     => $this->findCol($header, ['決済日','日付','Date']),
            ];

            $sum = ['checked'=>0,'granted'=>0,'skipped'=>0,'errors'=>0];

            foreach (array_slice($rows, 1) as $r) {
                $sum['checked']++;

                $orderId = $this->val($r, $idx['order_id']);
                if (!$orderId) { $sum['errors']++; continue; }

                // 事前に履歴重複チェック（system+order_id）
                $exists = \App\Models\CreditHistory::where('system', 'payz')
                    ->where('order_id', (string)$orderId)
                    ->exists();
                if ($exists) {
                    $sum['skipped']++;
                    continue;
                }

                try {
                    $rawAmt  = $this->val($r, $idx['amount']);
                    $amount  = $this->toIntAmount($rawAmt);

                    // 9800円未満はスキップ
                    if ($amount < 9800) {
                        $sum['skipped']++;
                        continue;
                    }

                    $email   = mb_strtolower(trim((string)$this->val($r, $idx['email'])));
                    $name    = trim((string)$this->val($r, $idx['name']));
                    $tel     = trim((string)$this->val($r, $idx['tel'])); // 文字列として扱う
                    $note    = (string)($this->val($r, $idx['note']) ?? 'Payz import');
                    $dateStr = $this->val($r, $idx['date']);
                    $granted = now();
                    if ($dateStr) {
                        $note .= ' [date: ' . $dateStr . ']';
                    }

                    $userId = $this->resolveUserIdCompact($email, $name, $tel);
                    if (!$userId) { $sum['errors']++; continue; }

                    $credit = (int) env('ABELABO_BONUS_CREDIT', 10);

                    DB::transaction(function () use ($userId, $orderId, $amount, $credit, $note, $granted) {
                        \App\Models\CreditHistory::create([
                            'user_id'    => $userId,
                            'order_id'   => (string)$orderId,
                            'amount'     => $amount,
                            'credit'     => $credit,
                            'system'     => 'payz',
                            'granted_at' => $granted,
                            'note'       => $note,
                        ]);
                        $user = \App\Models\User::find($userId);
                        if ($user) {
                            $user->credit_balance += $credit;
                            $user->save();
                        }
                    });

                    $sum['granted']++;

                } catch (\Throwable $e) {
                    $msg = mb_strtolower($e->getMessage());
                    if (str_contains($msg, 'unique') || str_contains($msg, 'duplicate')) {
                        $sum['skipped']++;
                    } else {
                        $sum['errors']++;
                        Log::warning('Payz import row error', [
                            'ex' => $e->getMessage(), 'row' => $r,
                        ]);
                    }
                }
            }

            return $sum;
        });
    }

    private function getSheetValues(?string $range = null): array
    {
        $credentialsPath = env('GOOGLE_SHEETS_CREDENTIALS');
        $sheetId         = env('PAYZ_SHEET_ID');
        $range           = $range ?? env('PAYZ_SHEET_RANGE', 'payz_orders!A:Z');

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsPath);
        $client->setApplicationName('Payz Import');
        $client->setScopes([GoogleSheets::SPREADSHEETS_READONLY]);

        $sheets = new GoogleSheets($client);
        $resp   = $sheets->spreadsheets_values->get($sheetId, $range);

        return $resp->getValues() ?? [];
    }

    private function findCol(array $header, array $candidates): ?int
    {
        foreach ($header as $i => $name) {
            foreach ($candidates as $c) {
                if (mb_strtolower($name) === mb_strtolower($c)) return $i;
            }
        }
        return null;
    }

    private function val(array $row, ?int $i): mixed
    {
        return $i === null ? null : ($row[$i] ?? null);
    }

    private function toIntAmount(string|int|null $raw): int
    {
        $n = preg_replace('/[^\d\-]/u', '', (string)$raw);
        return (int)$n;
    }

    private function toCarbon(?string $s): ?Carbon
    {
        if (!$s) return null;
        try { return Carbon::parse($s); } catch (\Throwable) { return null; }
    }

    private function resolveUserIdCompact(?string $email, ?string $name, ?string $tel): ?int
    {
        $q = AbelaboUserSetting::query();
        if ($email) $q->orWhereRaw('LOWER(email)=?', [mb_strtolower($email)]);
        // 名前一致は不要
        // if ($name)  $q->orWhere('name', $name);
        // 電話番号は入力値そのままで一致
        if ($tel)   $q->orWhere('tel', $tel);
        $s = $q->first();
        return $s?->user_id;
    }
}