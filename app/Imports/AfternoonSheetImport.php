<?php

namespace App\Imports;

use App\Models\Group;
use App\Models\Player;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithValidation;

class AfternoonSheetImport implements OnEachRow, WithHeadingRow
{
    protected array $groupCache = [];
    protected array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            '*.group'  => ['required'],
            '*.time'   => ['required'],
            '*.tee'    => ['required'],
            '*.name'   => ['required'],
            '*.origin' => ['required'],
        ];
    }

    public function onRow(Row $row)
    {
        $data = $row->toArray();
        // dd($data);

        if (!isset($data['group']) && !isset($data['time']) && !isset($data['tee'])) {
            return;
        }

        if (!isset($this->groupCache[$data['group']])) {
            $this->groupCache[$data['group']] = Group::firstOrCreate(
                [
                    'name' => $data['group'],
                    'time'  => $data['time'],
                    'tee'   => $data['tee'],
                    'session'   => 'afternoon',
                    'tournament_round_id' => $this->options['round_id'],
                ],
                []
            );
        }

        $group = $this->groupCache[$data['group']];

        Player::create([
            'group_id' => $group->id,
            'name'     => $data['name'],
            'origin'   => $data['origin'],
        ]);
    }
}
