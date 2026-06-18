<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GroupStandardImport implements WithMultipleSheets
{
    protected array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function sheets(): array
    {
        $sheets = [
            'Morning' => new MorningStandartSheetImport($this->options),
            'Afternoon' => new AfternoonStandartSheetImport($this->options),
        ];

        $filePath = is_string($this->options['file'])
            ? $this->options['file']
            : $this->options['file']->getRealPath();

        $spreadsheet = IOFactory::load($filePath);

        $collections = collect($spreadsheet->getSheetNames())
            // ->filter(function ($sheetName) use ($sheets) {
            //     return array_key_exists($sheetName, $sheets);
            // })
            ->mapWithKeys(function ($sheetName) use ($sheets) {
                if (!array_key_exists($sheetName, $sheets)) {
                    return [$sheetName => new AllSheetImport()];
                }
                
                return [$sheetName => $sheets[$sheetName]];
            })
            ->toArray();

        return $collections;
    }
}
