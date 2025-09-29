<?php

namespace Campaigncenter\FilamentEnvEditor\Pages\Actions;

use Campaigncenter\FilamentEnvEditor\Pages\ViewEnv;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;

class EditAction extends \Filament\Actions\Action
{
    private EntryObj $entry;

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    public function setEntry(EntryObj $obj): static
    {
        $this->entry = $obj;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-c-cog-8-tooth');
        $this->hiddenLabel();
        $this->color(Color::Sky);
        $this->form([
            TextInput::make('key')->default(fn () => $this->entry->key)->required(),
            TextInput::make('value')->default(fn () => $this->entry->getValue()),
        ]);
        $this->action(function (array $data, ViewEnv $page) {
            EnvEditor::editKey($data['key'], $data['value']);
            $page->refresh();
        });
        $this->size(Size::Small);
        $this->outlined();
        $this->modalIcon('heroicon-c-cog-8-tooth');
        $this->modalHeading(__('filament-env-editor::filament-env-editor.actions.edit.modal.text'));
        $this->tooltip(fn (): string => __('filament-env-editor::filament-env-editor.actions.edit.tooltip', ['name' => $this->entry->key]));
    }
}
