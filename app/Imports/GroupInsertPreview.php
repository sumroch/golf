<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class GroupInsertPreview implements WithCustomStartCell
{
    public function startCell(): string
    {
        return 'B2';
    }

    public function rules(): array
    {
        return [
            '*.GROUP'  => ['required'],
            '*.TIME'   => ['required'],
            '*.TEE'    => ['required'],
            '*.NAME'   => ['required'],
            '*.ORIGIN' => ['required'],
        ];
    }
}
