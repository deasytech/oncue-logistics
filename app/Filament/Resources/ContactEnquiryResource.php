<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactEnquiryResource\Pages;
use App\Filament\Resources\ContactEnquiryResource\RelationManagers;
use App\Models\ContactEnquiry;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use App\Filament\Exports\ContactEnquiryExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactEnquiryResource extends Resource
{
    protected static ?string $model = ContactEnquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'outstanding' => 'Outstanding',
                                'pending' => 'Pending',
                                'concluded' => 'Concluded',
                            ])
                            ->default('outstanding')
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'outstanding' => 'Outstanding',
                        'pending' => 'Pending',
                        'concluded' => 'Concluded',
                    ])
                    ->default('outstanding')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('convertToCustomer')
                        ->label('Convert to Customer')
                        ->icon('heroicon-o-user-plus')
                        ->color('primary')
                        ->url(fn(ContactEnquiry $record): string => CustomerResource::getUrl('create', [
                            'enquiryName' => $record->name,
                            'enquiryEmail' => $record->email,
                            'enquiryPhone' => $record->phone,
                        ])),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ContactEnquiryExporter::class)
                    ->fileName('contact_enquiries_export_' . now()->format('Y-m-d_H-i-s'))
                    ->label('Export Enquiries'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total Enquiries Received';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactEnquiries::route('/'),
            // 'create' => Pages\CreateContactEnquiry::route('/create'),
            // 'edit' => Pages\EditContactEnquiry::route('/{record}/edit'),
        ];
    }
}
