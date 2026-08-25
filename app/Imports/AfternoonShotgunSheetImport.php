<?php

namespace App\Imports;

use App\Models\Group;
use App\Models\Player;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithValidation;

class AfternoonShotgunSheetImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsEmptyRows
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
            '*.group'  => ['required', 'string'],
            '*.tee'    => ['required', 'numeric', 'min:1', 'max:18'],
            '*.name'   => ['required', 'string'],
            '*.origin' => ['required', 'string'],
        ];
    }

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        if (!isset($data['group']) && !isset($data['tee'])) {
            return;
        }

        if (!isset($this->groupCache[$data['group']])) {
            $this->groupCache[$data['group']] = Group::firstOrCreate(
                [
                    'name' => $data['group'],
                    'time'  => $this->options['round']->afternoon,
                    'tee'   => $data['tee'],
                    'group_number'   => explode(' ', $data['group'])[1] ?? null,
                    'session'   => 'afternoon',
                    'tournament_round_id' => $this->options['round']->id,
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
