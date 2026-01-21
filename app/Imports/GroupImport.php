<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GroupImport implements WithMultipleSheets
{
    protected array $options;
    
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function sheets(): array
    {
        return [
            'Morning' => new MorningSheetImport($this->options),
            'Afternoon' => new AfternoonSheetImport($this->options),
        ];
    }
}
