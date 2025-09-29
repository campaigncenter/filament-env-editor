<?php

namespace Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups;

use Campaigncenter\FilamentEnvEditor\Pages\ViewEnv;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use GeoSot\EnvEditor\Dto\BackupObj;
use GeoSot\EnvEditor\Facades\EnvEditor;

class DeleteBackupAction extends Action
{
    private BackupObj $entry;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    public function setEntry(BackupObj $obj): static
    {
        $this->entry = $obj;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-trash');
        $this->hiddenLabel();
        $this->outlined();
        $this->color(Color::Rose);

        $this->size(ActionSize::Small);
        $this->tooltip(fn (): string => __('filament-env-editor::filament-env-editor.actions.delete-backup.tooltip', ['name' => $this->entry->name]));
        $this->modalIcon('heroicon-o-trash');
        $this->modalHeading(fn (): string => __('filament-env-editor::filament-env-editor.actions.delete-backup.confirm.title', ['name' => $this->entry->name]));

        $this->action(function (ViewEnv $page) {
            EnvEditor::deleteBackup($this->entry->name);
            $page->refresh();
        });

        $this->requiresConfirmation();
    }
}
