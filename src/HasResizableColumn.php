<?php

namespace Asmit\ResizedColumn;

use Asmit\ResizedColumn\Setup\Setup;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;

trait HasResizableColumn
{
    public function bootedHasResizableColumn()
    {
        foreach ($this->getCurrentTableColumns() as $columnName => $column) {
            $this->applyExtraAttributes($columnName, $column);
        }
        if (self::isPreservedOnDB()) {
            // Logic to save on database
        }
    }

    private function getColumnHtmlId(string $columnName): string
    {
        return Str::of($columnName)->snake()
            ->replace('_', '-')
            ->replace(' ', '-')
            ->toString();
    }

    private function applyExtraAttributes(string $columnName, Column $column): void
    {
        /**
         * @var Column $column
         */
        $width = $this->columnWidths[$columnName]['width'] ?? null;
        $styles = $this->getColumnStyles($width);

        $columnId = $this->getColumnHtmlId($columnName);

        $column->extraHeaderAttributes([
            'x-data' => "resizedColumn(`{$columnName}`, `{$columnId}`)",
            ...$styles['header'],
        ])
            ->extraCellAttributes($styles['cell']);
    }

    /**
     * @return array{header: array<string, string>, cell: array<string, string>}
     */
    protected function getColumnStyles(?string $width): array
    {
        if (! $width) {
            return ['header' => [], 'cell' => []];
        }

        $style = "min-width: {$width}px; width: {$width}px; max-width: {$width}px";

        return [
            'header' => ['style' => $style],
            'cell' => ['style' => "{$style}; overflow: hidden"],
        ];
    }

    public static function isPreservedOnDB(): bool
    {
        return Setup::preserveOnDB();
    }

    /**
     * Retrieves the table's columns.
     *
     * @return array<string, Column>
     */
    #[Computed(cache: true)]
    protected function getCurrentTableColumns(): array
    {
        return $this->getTable()->getColumns();
    }

    #[Renderless]
    public function updateColumnWidth(string $columnName, string $newWidth): void
    {
        //
    }
}
