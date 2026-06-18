# Delivery Service Flow Implementation

## Overview

This document describes the new delivery service flow implemented in the Oncue event management system. The flow ensures that customers must complete payment for delivery services before they can access guest management features.

## Flow Summary

### 1. **Direct Access via Sidebar** (NEW)

-   Customers can now access delivery services directly from the main navigation sidebar
-   Located under the "Platform" section with a truck icon
-   Provides quick access to manage delivery services for all events

### 2. Event Creation (DONE)

-   Customer creates an event through the existing event creation form
-   After successful creation, a modal appears with three options:
    -   **Choose Delivery Service** (NEW) - Takes customer to delivery service selection
    -   **Add Guest to Event** - Direct access to guest creation (only if delivery is paid)
    -   **Go to Events List** - Returns to events dashboard

### 2. Delivery Service Selection

-   Customer is presented with available delivery services based on event type (corporate/social)
-   Services include:
    -   **Only Aso Ebi & Invitation** (₦15,000) - Both corporate and social events
    -   **Aso Ebi, Invitation & Packaging** (₦25,000) - Both corporate and social events
    -   **Invitation Card (Corporate only)** (₦8,000) - Corporate events only
    -   **Packages Only** (₦12,000) - Both corporate and social events

### 3. Payment Process

Customer can choose between two payment methods:

#### Online Payment (Paystack)

-   Redirects to Paystack payment gateway
-   Automatic payment verification via webhook
-   Updates delivery status to 'paid' upon successful payment

#### Offline Payment (Receipt Upload)

-   Customer uploads payment receipt
-   Receipt is reviewed by admin team
-   Manual approval process required

### 5. Guest Management Access Control

-   **Before Payment**: Guest management features are restricted
-   **After Payment**: Full access to guest list, import, and creation features
-   Access is checked based on delivery payment status across all customer events

## Navigation Integration

### Sidebar Access

The delivery services page is now accessible directly from the main navigation sidebar:

-   **Location**: Platform section → Delivery Services (truck icon)
-   **Route**: `/delivery-services`
-   **Active State**: Highlights when on the delivery services page

This provides customers with two ways to access delivery services:

1. **Event Creation Flow**: After creating an event, choose "Delivery Service" from the success modal
2. **Direct Navigation**: Click "Delivery Services" from the sidebar at any time

## Database Schema Changes

### New Tables

1. **delivery_services**
    - `id`, `name`, `description`, `cost`, `is_active`, `applicable_to`, `timestamps`

### Modified Tables

1. **deliveries** (enhanced)
    - Added: `delivery_service_id`, `cost`, `payment_status`, `payment_method`, `payment_reference`, `paid_at`

## Key Components

### Models

-   **DeliveryService**: Manages available delivery services
-   **Delivery** (enhanced): Tracks delivery requests and payment status

### Livewire Components

-   **EventCreate** (modified): Updated success modal with delivery service option
-   **DeliveryServices** (new): Handles delivery service selection and payment
-   **GuestList** (enhanced): Added payment status check for access control
-   **PaymentReceipts** (enhanced): Links receipts to delivery payments

### Controllers

-   **PaystackController** (enhanced): Handles both package and delivery service payments

### Console Commands

-   **TestDeliveryFlow**: Tests the complete delivery flow for customers

## Testing the Implementation

### Manual Testing Steps

1. Create a new event as a customer
2. Click "Choose Delivery Service" in the success modal
3. Select a delivery service and proceed to payment
4. Test both online (Paystack) and offline (receipt upload) payment methods
5. Verify guest management access is granted after payment
6. Test access restriction before payment

### Automated Testing

Use the provided console command:

```bash
php artisan test:delivery-flow {customer_id}
```

## Security Considerations

1. **Access Control**: Guest management is strictly controlled by delivery payment status
2. **Payment Verification**: Both online and offline payments are verified before granting access
3. **Event Ownership**: Delivery services are tied to specific customer events
4. **Receipt Validation**: Uploaded receipts require admin approval

## Future Enhancements

1. **Admin Dashboard**: Interface for managing delivery service payments and receipts
2. **Email Notifications**: Automated notifications for payment status changes
3. **Delivery Tracking**: Real-time delivery status updates
4. **Multiple Delivery Options**: Support for multiple delivery addresses per event
5. **Delivery Scheduling**: Allow customers to schedule delivery dates

## Troubleshooting

### Common Issues

1. **Guest access still restricted after payment**: Check delivery payment status in database
2. **Delivery services not showing**: Verify event type categorization and service applicability
3. **Paystack payment failures**: Check Paystack configuration and API keys

### Debug Commands

```bash
# Check delivery services
php artisan tinker
>>> App\Models\DeliveryService::all()

# Check customer delivery status
php artisan test:delivery-flow {customer_id}
```

## Migration Rollback

If needed, the changes can be rolled back:

```bash
php artisan migrate:rollback --step=2
```

This will remove the delivery_services table and revert the deliveries table modifications.
