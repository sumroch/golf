<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GroupShotgunImport implements WithMultipleSheets
{
    protected array $options;
    
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function sheets(): array
    {
        return [
            'Morning' => new MorningShotgunSheetImport($this->options),
            'Afternoon' => new AfternoonShotgunSheetImport($this->options),
        ];
    }
}
