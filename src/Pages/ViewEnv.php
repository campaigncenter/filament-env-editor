<?php

namespace Campaigncenter\FilamentEnvEditor\Pages;

use Campaigncenter\FilamentEnvEditor\FilamentEnvEditorPlugin;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups\DeleteBackupAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups\DownloadEnvFileAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups\MakeBackupAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups\RestoreBackupAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups\ShowBackupContentAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\Backups\UploadBackupAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\CreateAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\DeleteAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\EditAction;
use Campaigncenter\FilamentEnvEditor\Pages\Actions\OptimizeClearAction;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use GeoSot\EnvEditor\Dto\BackupObj;
use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Exceptions\EnvException;
use GeoSot\EnvEditor\Facades\EnvEditor;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ViewEnv extends Page implements HasForms, HasActions
{
    use HasUnsavedDataChangesAlert;
    use InteractsWithHeaderActions;
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected string $view = 'filament-env-editor::view-editor';

    /**
     * @var list<mixed>
     */
    public array $data = [];

    protected function getHeaderActions(): array
    {
        return [
            OptimizeClearAction::make('optimize-clear'),
        ];
    }

    /**
     * @throws EnvException
     */
    public function form(Schema $schema): Schema
    {
        $tabs = Tabs::make('Tabs')
            ->tabs([
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.current-env.title'))
                    ->schema(function () {
                        $envData = EnvEditor::getEnvFileContent()
                            ->filter(fn (EntryObj $obj) => !$obj->isSeparator())
                            ->groupBy('group')
                            ->map(function (Collection $group) {
                                $fields = $group
                                    ->reject(fn (EntryObj $obj) => in_array($obj->key, FilamentEnvEditorPlugin::get()->getHiddenKeys()))
                                    ->map(function (EntryObj $obj) {
                                        return Group::make([
                                            Actions::make([
                                                EditAction::make("edit_{$obj->key}")->setEntry($obj),
                                                DeleteAction::make("delete_{$obj->key}")->setEntry($obj),
                                            ])->alignEnd(),
                                            Text::make($obj->getAsEnvLine())
                                                ->columnSpan(4),
                                        ])->columns(5);
                                    });

                                return Section::make()->schema($fields->all())->columns(1);
                            })
                            ->all();

                        $header = Group::make([
                            Actions::make([
                                CreateAction::make('Add'),
                            ])->alignEnd(),
                        ]);

                        return [$header, ...$envData];
                    }),
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.backups.title'))
                    ->schema(function () {
                        $data = EnvEditor::getAllBackUps()
                            ->map(function (BackupObj $obj) {
                                return Group::make([
                                    Actions::make([
                                        DeleteBackupAction::make("delete_{$obj->name}")->setEntry($obj),
                                        DownloadEnvFileAction::make("download_{$obj->name}")->setEntry($obj->name)->hiddenLabel()->size(Size::Small),
                                        RestoreBackupAction::make("restore_{$obj->name}")->setEntry($obj->name),
                                        ShowBackupContentAction::make("show_raw_content_{$obj->name}")->setEntry($obj),
                                    ])->alignEnd(),
                                    Text::make($obj->name)
                                        ->columnSpan(2),
                                    Text::make($obj->createdAt->format('Y-m-d H:i:s'))
                                        ->columnSpan(2),
                                ])->columns(5);
                            })->all();

                        $header = Group::make([
                            Actions::make([
                                DownloadEnvFileAction::make('download_current}')->tooltip('')->outlined(false),
                                UploadBackupAction::make('upload'),
                                MakeBackupAction::make('backup'),
                            ])->alignEnd(),
                        ]);

                        return [$header, ...$data];
                    }),
            ]);

        return $schema
            ->components([$tabs])
            ->statePath('data');
    }

    public function refresh(): void
    {
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentEnvEditorPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentEnvEditorPlugin::get()->getNavigationSort();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentEnvEditorPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentEnvEditorPlugin::get()->getNavigationLabel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return FilamentEnvEditorPlugin::get()->getSlug();
    }

    public function getTitle(): string
    {
        return __('filament-env-editor::filament-env-editor.page.title');
    }

    public static function canAccess(): bool
    {
        return FilamentEnvEditorPlugin::get()->isAuthorized();
    }
}
