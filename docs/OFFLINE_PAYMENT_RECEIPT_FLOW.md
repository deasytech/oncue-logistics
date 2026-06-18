# Offline Payment Receipt Flow - Implementation Summary

## Overview

This document describes the complete flow for offline payment receipt upload, admin approval, and guest access restriction lifting in the Oncue application.

## Flow Components

### 1. Customer Receipt Upload (Frontend)

**File:** `app/Livewire/PaymentReceipts.php`

-   Customers can upload payment receipts (PDF, JPG, JPEG, PNG) up to 5MB
-   Receipts are automatically linked to pending offline delivery payments
-   System updates delivery status to 'receipt_uploaded'
-   Receipt status is set to 'pending' awaiting admin approval

### 2. Admin Receipt Management (Filament Backend)

**Files:**

-   `app/Filament/Resources/PaymentReceiptResource.php`
-   `app/Filament/Resources/PaymentReceiptResource/Pages/ListPaymentReceipts.php`
-   `app/Filament/Resources/PaymentReceiptResource/Pages/EditPaymentReceipt.php`

**Features:**

-   Admin can view all uploaded receipts with customer information
-   Bulk approve/reject functionality
-   Individual receipt review and status update
-   Download receipt files
-   Add admin notes for approval/rejection decisions

### 3. Approval Logic & Email Notifications

**Files:**

-   `app/Filament/Resources/PaymentReceiptResource/Pages/EditPaymentReceipt.php`
-   `app/Mail/PaymentReceiptApprovedMail.php`
-   `resources/views/emails/payment-receipt-approved.blade.php`

When a receipt is **approved**:

-   Delivery payment status changes from 'receipt_uploaded' to 'paid'
-   Paid timestamp is recorded
-   **Beautiful email notification sent to customer** with:
    -   Payment confirmation details
    -   Direct link to guest management
    -   List of available features
    -   Support contact information
-   Admin notification about successful email delivery
-   Audit logging for tracking

When a receipt is **rejected**:

-   Delivery payment status resets to 'pending'
-   Customer needs to upload a new receipt
-   Audit logging for tracking

### 4. Guest Access Restriction Logic

**Files:**

-   `app/Livewire/Guests/GuestList.php`
-   `app/Livewire/Guests/GuestEdit.php`

**Access Criteria:** Customers can access guest management if they have:

1. **Paid delivery services** (payment_status = 'paid'), OR
2. **Approved payment receipts** (status = 'approved')

This ensures customers who paid offline and had their receipts approved get the same access as those who paid online.

## Database Schema

### Payment Receipts Table

```sql
CREATE TABLE payment_receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    description TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);
```

### Delivery Table Status Flow

1. `pending` → Customer needs to pay
2. `receipt_uploaded` → Customer uploaded receipt, awaiting approval
3. `paid` → Payment confirmed (online or approved offline)

## Usage Instructions

### For Customers:

1. Navigate to payment receipts page
2. Upload receipt file for offline payment
3. Add description (optional)
4. Wait for admin approval
5. Once approved, access guest management features

### For Admins:

1. Navigate to Payment Receipts in Filament admin panel
2. Review uploaded receipts
3. Download and verify receipt files
4. Update status (approve/reject) with notes
5. Bulk operations available for multiple receipts

## Security Features

-   File type validation (PDF, images only)
-   File size limit (5MB)
-   Customer authorization checks
-   Admin-only approval functionality
-   Audit logging for all approval actions
-   Email delivery error handling with admin notifications

## Error Handling

-   Comprehensive try-catch blocks in approval logic
-   Graceful error logging
-   User-friendly error messages
-   Fallback mechanisms for failed operations
-   Email delivery failure notifications to admin
-   Customer notification status tracking

## Testing

Use the existing test command to verify the flow:

```bash
php artisan test:delivery-flow {customer_id}
```

This will show:

-   Delivery service status
-   Payment status
-   Guest management access status

## Future Enhancements

-   ✅ **Email notifications for approval/rejection** - **IMPLEMENTED**
-   Receipt expiration dates
-   Multiple receipt uploads per delivery
-   Advanced filtering and search
-   Receipt validation automation
-   SMS notifications as backup
-   In-app notifications for customers
