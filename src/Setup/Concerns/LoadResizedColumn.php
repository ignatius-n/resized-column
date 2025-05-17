<?php

namespace Asmit\ResizedColumn\Setup\Concerns;

use Asmit\ResizedColumn\Models\TableSetting;
use Asmit\ResizedColumn\Setup\Setup;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;

trait LoadResizedColumn
{
    /**
     * @var array<string, array{width: string|null}>
     */
    protected array $columnWidths = [];

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
        $this->columnWidths[$columnName]['width'] = $newWidth;

        if (self::isPreservedOnDB()) {
            TableSetting::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'resource' => $this->getResourceModelFullPath(),
                ],
                ['styles' => $this->columnWidths]
            );
        }

        session()->put($this->getSessionKey(), $this->columnWidths);
    }

    protected function getResourceModelFullPath(): string
    {
        if (method_exists($this, 'getRelationship')) { // @phpstan-ignore-line

            $relationShipModelInstance = $this->getRelationship(); // @phpstan-ignore-line

            return $relationShipModelInstance->getModel()::class;
        } else {
            return static::getModel();
        }
    }

    private function getColumnHtmlId(string $columnName): string
    {
        return Str::of($columnName)->snake()
            ->replace('_', '-')
            ->replace(' ', '-')
            ->toString();
    }

    protected function getSessionKey(): string
    {
        $modelName = Str::snake(class_basename($this->getResourceModelFullPath()));

        return "tables.{$modelName}_columns_style";
    }

    protected function loadColumnWidths(): void
    {
        if (self::isPreservedOnSession()) {
            $sessionKey = $this->getSessionKey();
            $this->columnWidths = session($sessionKey);
        }

        if (self::isPreservedOnDB()) {
            if (blank($this->columnWidths)) {
                $this->columnWidths = TableSetting::query()
                    ->where('user_id', Auth::id())
                    ->where('resource', $this->getResourceModelFullPath())
                    ->value('styles') ?? [];

                session()->put($this->getSessionKey(), $this->columnWidths);
            }
        }

    }

    public static function isPreservedOnDB(): bool
    {
        return Setup::preserveOnDB();
    }

    public static function isPreservedOnSession(): bool
    {
        return Setup::preserveOnSession();
    }
}
