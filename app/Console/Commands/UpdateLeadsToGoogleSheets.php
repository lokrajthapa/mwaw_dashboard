<?php

namespace App\Console\Commands;

use App\Google\Google;
use App\Models\Lead;
use Google\Service\Sheets;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;

class UpdateLeadsToGoogleSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates leads to google sheets';

    public string $spreadSheetId;
    public Sheets $service;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = Google::getClient();
        $this->service = new Sheets($client);

        $this->spreadSheetId = env('LEAD_SPREADSHEET_ID');

        $this->clearSheet();

        $leads = Lead::query()->get();
        $leads = $leads->filter(function ($item) {
            return $item->gclid;
        });

        $updateIds = $leads->pluck('id');

        $leads = $leads->map(function ($item) {
            return [
                $item->gclid,
                'Sales',
                $item->conversion_datetime ? $item->conversion_datetime . ' +0000' : '',
                $item->conversion_value ?: '',
                $item->conversion_currency ?: '',
            ];
        });

        if ($leads->isNotEmpty()) {
            $this->updateSheet($leads->toArray());

            Lead::query()->whereIn('id', $updateIds->toArray())->update(['uploaded' => true]);
            $this->info($leads->count() . ' items uploaded');
        }

        return 0;
    }

    public function clearSheet()
    {
        $this->service->spreadsheets_values->clear($this->spreadSheetId, 'Sheet1!A2:Z1000', new ClearValuesRequest());
    }

    public function updateSheet(array $values)
    {
        $valueRange = new ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];
        $this->service->spreadsheets_values->update($this->spreadSheetId, $this->computeRange($values), $valueRange, $params);
    }

    public function computeRange(array $values): string
    {
        return 'Sheet1!A2:E' . (count($values) + 1);
    }
}
