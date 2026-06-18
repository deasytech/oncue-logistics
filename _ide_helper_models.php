<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property string $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsoEbiSubscription whereUpdatedAt($value)
 */
	class AsoEbiSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read Category|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $state_id
 * @property string|null $iso_code
 * @property string|null $country_code
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @property-read \App\Models\State $state
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $subject
 * @property string $message
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEnquiry whereUpdatedAt($value)
 */
	class ContactEnquiry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string|null $email
 * @property string|null $address
 * @property int $is_active
 * @property string|null $setup_token
 * @property string|null $setup_token_expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $city_id
 * @property int|null $state_id
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guest> $guests
 * @property-read int|null $guests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageCustomization> $packageCustomizations
 * @property-read int|null $package_customizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentReceipt> $paymentReceipts
 * @property-read int|null $payment_receipts_count
 * @property-read \App\Models\State|null $state
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereSetupToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereSetupTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUserId($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $event_id
 * @property bool $delivery_required
 * @property string|null $address
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $delivery_service_id
 * @property numeric|null $cost
 * @property string $payment_status
 * @property string|null $payment_method
 * @property string|null $payment_reference
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property-read \App\Models\DeliveryService|null $deliveryService
 * @property-read \App\Models\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereDeliveryRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereDeliveryServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereUpdatedAt($value)
 */
	class Delivery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $cost
 * @property bool $is_active
 * @property string $applicable_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Delivery> $deliveries
 * @property-read int|null $deliveries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService applicableTo($eventType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereApplicableTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryService whereUpdatedAt($value)
 */
	class DeliveryService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property int $category_id
 * @property int|null $subcategory_id
 * @property string|null $custom_subcategory
 * @property string $location
 * @property string $estimated_number_of_guest
 * @property \Illuminate\Support\Carbon|null $event_date
 * @property string|null $aso_ebi_color
 * @property string|null $notes
 * @property string $slug
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsoEbiSubscription> $asoEbiSubscriptions
 * @property-read int|null $aso_ebi_subscriptions_count
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Delivery|null $delivery
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FabricType> $fabricTypes
 * @property-read int|null $fabric_types_count
 * @property-read mixed $display_subcategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuestFabricSelection> $guestFabricSelections
 * @property-read int|null $guest_fabric_selections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guest> $guests
 * @property-read int|null $guests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageCustomization> $packageCustomizations
 * @property-read int|null $package_customizations_count
 * @property-read \App\Models\Category|null $subCategory
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event isActive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAsoEbiColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCustomSubcategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEstimatedNumberOfGuest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSubcategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $base_price
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FabricType withoutTrashed()
 */
	class FabricType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string|null $title
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $city_id
 * @property int|null $state_id
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read string|null $customer_name
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuestPackageSelection> $packageSelections
 * @property-read int|null $package_selections_count
 * @property-read \App\Models\State|null $state
 * @method static \Database\Factories\GuestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereUpdatedAt($value)
 */
	class Guest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $guest_id
 * @property int $event_id
 * @property array<array-key, mixed> $fabric_selections
 * @property numeric $total_fabric_cost
 * @property int|null $delivery_zone_id
 * @property numeric $delivery_cost
 * @property numeric $total_amount
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $payment_reference
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $external_order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\Guest $guest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereDeliveryCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereDeliveryZoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereExternalOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereFabricSelections($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereTotalFabricCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestFabricSelection whereUpdatedAt($value)
 */
	class GuestFabricSelection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $guest_id
 * @property int $event_id
 * @property int $package_customization_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $total_price
 * @property int|null $delivery_service_id
 * @property numeric $delivery_cost
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $payment_reference
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeliveryService|null $deliveryService
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\Guest $guest
 * @property-read \App\Models\PackageCustomization $packageCustomization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereDeliveryCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereDeliveryServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection wherePackageCustomizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestPackageSelection whereUpdatedAt($value)
 */
	class GuestPackageSelection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Customer|null $customer
 * @property-read float $balance_due
 * @property-read bool $is_overdue
 * @property-read bool $is_paid
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withoutTrashed()
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Invoice|null $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem query()
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $email
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $subscribed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereSubscribedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereUpdatedAt($value)
 */
	class NewsletterSubscriber extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $cover_image
 * @property numeric $base_price
 * @property string|null $sku
 * @property array<array-key, mixed>|null $metadata
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageColor> $colors
 * @property-read int|null $colors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageCustomization> $customizations
 * @property-read int|null $customizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageFont> $fonts
 * @property-read int|null $fonts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageMaterial> $materials
 * @property-read int|null $materials_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereUpdatedAt($value)
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $package_id
 * @property string|null $name
 * @property string|null $hex
 * @property numeric $price_modifier
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor whereHex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor wherePriceModifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageColor whereUpdatedAt($value)
 */
	class PackageColor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $package_id
 * @property int $customer_id
 * @property int|null $material_id
 * @property int|null $font_id
 * @property int|null $color_id
 * @property string|null $message
 * @property string|null $location
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $total_price
 * @property string|null $preview_image_path
 * @property array<array-key, mixed>|null $meta
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $delivery_service_id
 * @property-read \App\Models\PackageColor|null $color
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\DeliveryService|null $deliveryService
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\PackageFont|null $font
 * @property-read \App\Models\PackageMaterial|null $material
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereDeliveryServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization wherePreviewImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageCustomization whereUpdatedAt($value)
 */
	class PackageCustomization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $package_id
 * @property string $name
 * @property string|null $google_font_family
 * @property numeric $price_modifier
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont whereGoogleFontFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont wherePriceModifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageFont whereUpdatedAt($value)
 */
	class PackageFont extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $package_id
 * @property string $name
 * @property numeric $price_modifier
 * @property array<array-key, mixed>|null $options
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial wherePriceModifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageMaterial whereUpdatedAt($value)
 */
	class PackageMaterial extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $reference
 * @property string $amount
 * @property string $payment_method
 * @property array<array-key, mixed>|null $customer_info
 * @property string|null $payment_notes
 * @property string|null $paid_at
 * @property string|null $payment_proof
 * @property string $status
 * @property array<array-key, mixed> $items
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereCustomerInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment wherePaymentNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment wherePaymentProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackagePayment whereUpdatedAt($value)
 */
	class PackagePayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $file_name
 * @property string $file_path
 * @property string $original_name
 * @property string $mime_type
 * @property int $file_size
 * @property string|null $description
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read string $file_size_formatted
 * @property-read string $status_badge_class
 * @property-read string $status_text
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereUpdatedAt($value)
 */
	class PaymentReceipt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string|null $payment_key
 * @property int $source_id
 * @property string $type
 * @property string|null $reference
 * @property string|null $payer_name
 * @property string|null $payer_email
 * @property numeric $amount
 * @property string $status
 * @property string $payment_method
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord wherePayerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord wherePayerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord wherePaymentKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRecord whereUpdatedAt($value)
 */
	class PaymentRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $iso_code
 * @property string|null $country_code
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\City> $cities
 * @property-read int|null $cities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereUpdatedAt($value)
 */
	class State extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

