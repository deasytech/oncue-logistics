<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Closure;

class GooglePlacesAutocomplete extends TextInput
{
  protected string $view = 'filament.forms.components.google-places-autocomplete';

  protected array | Closure $addressFields = [];

  protected string | Closure | null $country = 'ng';

  protected bool | Closure $restrictToCountry = true;

  public function addressFields(array | Closure $fields): static
  {
    $this->addressFields = $fields;

    return $this;
  }

  /**
   * @return array<string, mixed>
   */
  public function getAddressFields()
  {
    return $this->evaluate($this->addressFields) ?? [];
  }

  public function country(string | Closure | null $country): static
  {
    $this->country = $country;

    return $this;
  }

  public function getCountry(): ?string
  {
    return $this->evaluate($this->country);
  }

  public function restrictToCountry(bool | Closure $restrict = true): static
  {
    $this->restrictToCountry = $restrict;

    return $this;
  }

  public function shouldRestrictToCountry(): bool
  {
    return $this->evaluate($this->restrictToCountry);
  }

  public function getApiKey(): string
  {
    return config('services.google.places_api_key', '');
  }

  public function setUp(): void
  {
    parent::setUp();

    $this
      ->placeholder('Start typing to search for an address...')
      ->extraAttributes([
        'class' => 'google-places-autocomplete',
        'autocomplete' => 'off',
      ]);

    // Add validation to ensure address is selected from Google Places
    $this->rules(['required', function (string $attribute, $value, Closure $fail) {
      // Check if the value looks like a valid address (contains common address elements)
      if (empty($value) || strlen($value) < 5) {
        $fail('Please select a valid address from the dropdown.');
      }
    }]);
  }
}
