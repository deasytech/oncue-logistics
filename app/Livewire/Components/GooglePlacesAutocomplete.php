<?php

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class GooglePlacesAutocomplete extends Component
{
  public $name = 'address';
  public $label = 'Address';
  public $placeholder = 'Start typing to search for an address...';
  public $value = '';
  public $country = 'ng';
  public $restrictToCountry = true;
  public $required = false;
  public $apiKey;

  // Additional fields to populate
  public $stateField = null;
  public $cityField = null;
  public $stateValue = null;
  public $cityValue = null;

  public function mount(
    $name = 'address',
    $label = 'Address',
    $placeholder = 'Start typing to search for an address...',
    $value = '',
    $country = 'ng',
    $restrictToCountry = true,
    $required = false,
    $stateField = null,
    $cityField = null
  ) {
    $this->name = $name;
    $this->label = $label;
    $this->placeholder = $placeholder;
    $this->value = $value;
    $this->country = $country;
    $this->restrictToCountry = $restrictToCountry;
    $this->required = $required;
    $this->apiKey = config('services.google.places_api_key', '');
    $this->stateField = $stateField;
    $this->cityField = $cityField;
  }

  #[On('google-places-selected')]
  public function handlePlaceSelected($data)
  {
    if ($data['statePath'] === $this->name) {
      $this->value = $data['addressData']['formatted_address'] ?? '';

      // Dispatch events to update parent component fields
      if ($this->stateField && !empty($data['addressData']['state'])) {
        $this->dispatch('google-places-state-detected', [
          'field' => $this->stateField,
          'stateName' => $data['addressData']['state']
        ]);
      }

      if ($this->cityField && !empty($data['addressData']['city'])) {
        $this->dispatch('google-places-city-detected', [
          'field' => $this->cityField,
          'cityName' => $data['addressData']['city']
        ]);
      }
    }
  }

  public function updatedValue($value)
  {
    // Dispatch event to parent component
    $this->dispatch('address-updated', [
      'field' => $this->name,
      'value' => $value
    ]);
  }

  public function render()
  {
    return view('livewire.components.google-places-autocomplete');
  }
}
