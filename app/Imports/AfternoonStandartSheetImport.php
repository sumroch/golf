<?php

namespace App\Imports;

use App\Models\Group;
use App\Models\Player;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class AfternoonStandartSheetImport implements ToCollection, HasReferencesToOtherSheets, WithCalculatedFormulas
{
    protected array $groups = [];
    protected array $crossovers = [];
    protected array $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function collection(Collection $rows)
    {
        $section = null;

        foreach ($rows as $row) {

            $firstCell = trim((string) ($row[0] ?? ''));
            $secondCell = trim((string) ($row[1] ?? ''));

            if (str_contains(strtoupper($secondCell), 'START TEE 1')) {
                $section = 'tee_1';
            }

            if (str_contains(strtoupper($secondCell), 'START TEE 10')) {
                $section = 'tee_10';
            }

            if (str_contains(strtoupper($secondCell), 'CROSSOVER FROM T10') || str_contains(strtoupper($secondCell), 'CROSSOVER FROM T1')) {
                $section = 'crossover';
            }

            if ($section === 'crossover') {
                continue;
            }

            if (
                in_array($section, ['tee_1', 'tee_10'])
                && is_numeric($firstCell)
                && !in_array($row[1], [null, '', '-', 0])
                && !in_array($row[2], [null, '', '-', 0])
            ) {

                $this->groups[] = [
                    'name' => 'Group ' . $firstCell,
                    'time' => $this->normalizeTime($row[5] ?? null),
                    'tee' => $section === 'tee_1' ? 1 : 10,
                    'session' => 'afternoon',
                    'tournament_round_id' => $this->options['round_id'] ?? null,
                    'players' => [
                        $row[1] ?? null,
                        $row[2] ?? null,
                        $row[3] ?? null,
                        $row[4] ?? null,
                    ],
                ];
            }
        }

        foreach ($this->groups as $groupData) {
            $group = Group::create([
                'name' => $groupData['name'],
                'group_number'   => explode(' ', $groupData['name'])[1] ?? null,
                'time' => $groupData['time'],
                'tee' => $groupData['tee'],
                'session' => $groupData['session'],
                'tournament_round_id' => $groupData['tournament_round_id'],
            ]);

            foreach ($groupData['players'] as $player) {
                Player::create([
                    'group_id' => $group->id,
                    'name'     => $player,
                    'origin'   => null,
                ]);
            }
        }
    }

    protected function normalizeTime($value): ?string
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                ->format('H:i:s');
        }

        return date('H:i:s', strtotime($value));
    }
}
