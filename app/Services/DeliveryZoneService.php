<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DeliveryZoneService
{
  private $apiEndpoint;

  public function __construct()
  {
    $this->apiEndpoint = config('delivery.middleware_url') . config('delivery.endpoints.zones');
  }

  /**
   * Fetch delivery zones from external API (new format with categories)
   */
  public function getDeliveryZones(): array
  {
    return Cache::remember('delivery_zones', 3600, function () {
      try {
        $response = Http::timeout(10)->get($this->apiEndpoint);

        if ($response->successful()) {
          $data = $response->json();

          // The new API returns: {"success": true, "count": 14, "data": [...]}
          // Where "data" contains categories with zones
          if (isset($data['success']) && $data['success'] && isset($data['data'])) {
            return $data['data']; // Return the categories array
          }

          // Fallback: check if it's the old format (flat array)
          if (is_array($data) && !isset($data['success'])) {
            return $data;
          }
        }

        // Fallback to empty array if API fails
        return [];
      } catch (\Exception $e) {
        \Log::error('Failed to fetch delivery zones: ' . $e->getMessage());
        return [];
      }
    });
  }

  /**
   * Get delivery zone by ID (support both old and new formats)
   */
  public function getZoneById(int $zoneId): ?array
  {
    $zonesData = $this->getDeliveryZones();

    // Check if it's the new format (categories with zones)
    if (isset($zonesData[0]) && isset($zonesData[0]['category']) && isset($zonesData[0]['zones'])) {
      // New format: iterate through categories and their zones
      foreach ($zonesData as $category) {
        foreach ($category['zones'] as $zone) {
          if ($zone['id'] === $zoneId) {
            return $zone;
          }
        }
      }
    } else {
      // Old format: flat array
      foreach ($zonesData as $zone) {
        if ($zone['id'] === $zoneId) {
          return $zone;
        }
      }
    }

    return null;
  }

  /**
   * Calculate delivery cost for a zone
   */
  public function getDeliveryCost(int $zoneId): float
  {
    $zone = $this->getZoneById($zoneId);

    return $zone ? (float) $zone['price'] : 0.0;
  }

  /**
   * Get all zones flattened (for backward compatibility)
   */
  public function getAllZonesFlat(): array
  {
    $categories = $this->getDeliveryZones();
    $flatZones = [];

    // Check if it's the new format
    if (isset($categories[0]) && isset($categories[0]['category']) && isset($categories[0]['zones'])) {
      foreach ($categories as $category) {
        foreach ($category['zones'] as $zone) {
          $flatZones[] = $zone;
        }
      }
    } else {
      // Already flat format
      $flatZones = $categories;
    }

    return $flatZones;
  }

  /**
   * Get zones grouped by category (for new UI)
   */
  public function getZonesByCategory(): array
  {
    $categories = $this->getDeliveryZones();

    // If already in category format, return as-is
    if (isset($categories[0]) && isset($categories[0]['category']) && isset($categories[0]['zones'])) {
      return $categories;
    }

    // If old format, convert to category format
    $grouped = [];
    foreach ($categories as $zone) {
      $category = $zone['shipping_category'] ?? 'standard';
      $categoryLabel = $zone['category_label'] ?? ucfirst(str_replace('_', ' ', $category));

      if (!isset($grouped[$category])) {
        $grouped[$category] = [
          'category' => $category,
          'label' => $categoryLabel,
          'zones' => []
        ];
      }

      $grouped[$category]['zones'][] = $zone;
    }

    return array_values($grouped);
  }

  /**
   * Clear delivery zones cache
   */
  public function clearCache(): void
  {
    Cache::forget('delivery_zones');
  }
}
