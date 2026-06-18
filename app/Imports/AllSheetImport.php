<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class AllSheetImport implements ToCollection, WithCalculatedFormulas, SkipsEmptyRows, HasReferencesToOtherSheets
{
    public function collection(Collection $rows) {}
}
