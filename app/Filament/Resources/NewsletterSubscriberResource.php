<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
  protected static ?string $model = NewsletterSubscriber::class;

  protected static ?string $navigationIcon = 'heroicon-o-envelope';

  protected static ?string $navigationGroup = 'Entries';

  protected static ?int $navigationSort = 9;

  public static function getNavigationLabel(): string
  {
    return 'Newsletter Subscribers';
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Subscriber Details')
          ->schema([
            Forms\Components\TextInput::make('email')
              ->email()
              ->required()
              ->disabled(),
            Forms\Components\Toggle::make('is_active')
              ->label('Active Subscription')
              ->required(),
            Forms\Components\DateTimePicker::make('subscribed_at')
              ->label('Subscribed At')
              ->seconds(false),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('email')
          ->searchable()
          ->sortable(),
        Tables\Columns\IconColumn::make('is_active')
          ->label('Active')
          ->boolean()
          ->sortable(),
        Tables\Columns\TextColumn::make('subscribed_at')
          ->label('Subscribed')
          ->dateTime()
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('Active'),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
      ->defaultSort('subscribed_at', 'desc');
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListNewsletterSubscribers::route('/'),
      'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
    ];
  }

  public static function getNavigationBadge(): ?string
  {
    return (string) static::getModel()::where('is_active', true)->count();
  }

  public static function getNavigationBadgeTooltip(): ?string
  {
    return 'Active newsletter subscribers';
  }
}
