# Your Events - System Diagrams

This document contains Mermaid diagrams describing the system workflows for Customers, Suppliers, and Payments.

## 1. Customer Journey (Registration to Booking Execution)

This sequence diagram illustrates the full flow from a new customer registration, through requesting a quote, admin approval, payment, and finally booking execution.

```mermaid
sequenceDiagram
    autonumber
    actor C as Customer
    participant W as Website (Frontend)
    participant A as Admin Panel
    participant S as Supplier Panel
    participant P as Payment System (Admin)

    %% Registration Phase
    Note over C, W: Registration Phase
    C->>W: Access /register & Fill Data
    W->>W: Store Reg Data in Session + Send OTP via Email
    C->>W: Enter OTP & Verify
    W->>W: Create User + Auto Login (register.complete)
    W-->>C: Redirect to Home (Authenticated)

    %% Quote Request Phase
    Note over C, W: Quote Request Phase
    C->>W: Browse Services & Add to Cart
    C->>W: Execute /quotes/checkout
    W->>W: Create Quote (status: under_review)
    W->>W: Convert CartItems to QuoteItems
    W->>W: Clear Cart
    W-->>C: Send Quote Email + Quote Reference
    W-->>A: Send n8n Notification (Review Needed)

    %% Admin Approval Phase
    Note over A, S: Admin Approval Phase
    A->>W: Review Quote Details
    A->>W: Update Status -> approved
    W-->>C: Send Approval Email
    W-->>S: Notify Suppliers linked to Quote Services (via Email/System)

    %% Payment & Booking Phase
    Note over C, P: Payment & Booking Phase
    C->>W: Access /quotes/{id}/payment (Approved Only)
    C->>W: Select Method (Card/Bank/Cash) & Submit
    W->>W: Create Booking (status: confirmed, payment: pending)
    W->>W: Create Payment Record (status: pending)
    W->>W: Update Quote Status -> booked
    W-->>C: Send Booking Confirmation Email
    
    %% Payment Confirmation (Admin)
    A->>P: Review Payment Record
    A->>P: Update Payment Status -> paid
    P-->>W: Update Payment Record (captured_at set)

    %% Execution Phase
    Note over S, C: Execution Phase
    S->>W: View Booking in Supplier Panel
    S->>W: Update Booking Status -> in_progress
    S->>W: Execute Service
    S->>W: Update Booking Status -> completed
    W-->>A: Update Reports & Stats
```

## 2. Supplier Journey (Registration to Order Fulfillment)

This flowchart describes how a supplier registers, gets approved, and starts receiving and fulfilling orders/bookings.

```mermaid
flowchart TD
    subgraph Registration
        A[Start: Supplier Registration] --> B[Fill Form & Submit]
        B --> C[Receive OTP Email]
        C --> D{Verify OTP}
        D -->|Valid| E[Account Created (Status: Pending)]
        D -->|Invalid| C
    end

    subgraph Approval
        E --> F[Admin Review]
        F -->|Approve| G[Account Active (Status: Approved)]
        F -->|Reject| H[Account Rejected]
        H --> I[End]
    end

    subgraph Onboarding
        G --> J[Supplier Login]
        J --> K[Manage Services]
        K --> L[Update Availability & Prices]
    end

    subgraph Operations
        L --> M{New Approved Quote?}
        M -->|Contains Supplier Service| N[View Quote in Dashboard]
        N --> O{Accept Quote?}
        O -->|Yes (First)| P[Lock Quote (Accepted by Supplier)]
        O -->|No| M
        P --> Q[Receive Booking (Confirmed)]
        Q --> R[Start Execution (In Progress)]
        R --> S[Complete Execution (Completed)]
        S --> T[Revenue Recorded]
    end
```

## 3. Quote & Payment States

State diagram showing the lifecycle of a Quote and its associated Payment.

```mermaid
stateDiagram-v2
    direction LR

    state "Quote Lifecycle" as QL {
        [*] --> Pending
        Pending --> UnderReview: Checkout
        UnderReview --> Approved: Admin Approval
        UnderReview --> Rejected: Admin Rejection
        Approved --> Booked: Payment Processed
        Booked --> Completed: Service Done
        Rejected --> [*]
        Completed --> [*]
    }

    state "Payment Lifecycle" as PL {
        [*] --> PaymentPending: Created
        PaymentPending --> PaymentProcessing: Processing (Optional)
        PaymentPending --> Paid: Admin Confirm
        PaymentProcessing --> Paid: Gateway Success
        PaymentProcessing --> Failed: Gateway Fail
        PaymentPending --> Cancelled: User Cancel
        Paid --> Refunded: Admin Refund
        Failed --> [*]
        Cancelled --> [*]
        Refunded --> [*]
    }
```

## Key Technical References

### Customer Flow
- **Registration**: `AuthController::register` -> `OtpController::showVerifyForm` -> `OtpController::completeRegistration`
- **Quote Creation**: `QuoteController::checkout` (Creates Quote from Cart)
- **Payment**: `QuoteController::processPayment` (Creates Booking & Payment records)

### Supplier Flow
- **Registration**: `SupplierController::store` -> `SupplierController::verifyOtp`
- **Dashboard**: `SupplierDashboardController::index`
- **Quote Acceptance**: `SupplierDashboardController::acceptQuote` (Locks quote to supplier)

### Admin Management
- **Quote Approval**: `Admin\QuoteController::updateStatus`
- **Payment Confirmation**: `Admin\PaymentController::updateStatus`
