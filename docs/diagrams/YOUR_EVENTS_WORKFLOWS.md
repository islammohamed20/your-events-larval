# Your Events - System Workflows & Diagrams

This document contains detailed Mermaid diagrams showing the complete workflows for customers and suppliers in the Your Events platform.

## Customer Journey - Complete Workflow

### Customer Registration to Order Completion (Sequence Diagram)

```mermaid
sequenceDiagram
    autonumber
    actor C as Customer
    participant W as Website (Frontend)
    participant AC as AuthController
    participant OC as OtpController
    participant QC as QuoteController
    participant AdC as AdminController
    participant PC as PaymentController
    participant BC as BookingController
    participant M as MailService
    participant N as NotificationService

    %% Registration Phase
    C->>W: Access /register & Fill Registration Form
    W->>AC: Submit Registration Data
    AC->>AC: Validate Data (name, email, phone, password)
    AC->>AC: Store Data in Session
    AC->>OC: Generate OTP for Email Verification
    OC->>M: Send OTP Email to Customer
    AC->>W: Redirect to OTP Verification Page
    W->>C: Show OTP Input Form
    
    C->>W: Enter OTP Code
    W->>OC: Submit OTP for Verification
    OC->>OC: Validate OTP Code
    alt OTP Valid
        OC->>AC: Complete Registration
        AC->>AC: Create User Account
        AC->>W: Auto-login Customer
        W->>C: Show Dashboard/Success Message
    else OTP Invalid
        OC->>W: Show Error Message
        W->>C: Display Error & Retry Option
    end

    %% Service Selection Phase
    C->>W: Browse Services & Categories
    W->>C: Show Available Services with Prices
    C->>W: Select Service & Configure Options
    W->>QC: Add to Cart with Selections
    QC->>QC: Validate Service Availability
    QC->>W: Update Cart Status
    W->>C: Show Cart Summary

    %% Quote Request Phase
    C->>W: Proceed to Checkout
    W->>QC: Submit Quote Request with Notes
    QC->>QC: Validate Cart Items
    QC->>QC: Create Quote with Status 'under_review'
    QC->>QC: Generate Quote Number
    QC->>M: Send Quote Confirmation Email to Customer
    QC->>N: Send New Quote Notification to Admin
    QC->>W: Redirect to Quote Details Page
    W->>C: Show Quote Confirmation & PDF Download

    %% Admin Review Phase
    AdC->>AdC: Review Quote Details
    alt Quote Approved
        AdC->>QC: Update Status to 'approved'
        QC->>M: Send Approval Email to Customer
        QC->>N: Notify Relevant Suppliers
        AdC->>W: Update Quote Status
    else Quote Rejected
        AdC->>QC: Update Status to 'rejected'
        QC->>M: Send Rejection Email with Reason
        AdC->>W: Update Quote Status
    end

    %% Payment Phase (if approved)
    C->>W: Access Payment Page
    W->>PC: Show Payment Form
    C->>W: Submit Payment Details
    W->>PC: Process Payment
    PC->>PC: Validate Payment Method
    PC->>PC: Create Payment Record
    PC->>BC: Create Booking from Quote
    BC->>BC: Generate Booking Reference
    BC->>M: Send Booking Confirmation Email
    BC->>N: Send Booking Notification to Admin
    PC->>W: Show Payment Success Page
    W->>C: Display Booking Confirmation & Details

    %% Order Execution Phase
    BC->>BC: Update Quote Status to 'booked'
    BC->>N: Notify Suppliers about Confirmed Booking
    M->>C: Send Event Reminder Emails
    Note over C,M: Order completion and execution tracking continues...
```

## Supplier Journey - Complete Workflow

### Supplier Registration to Order Reception (Flow Diagram)

```mermaid
flowchart TD
    Start([Supplier Starts Registration]) --> FillForm[Fill Registration Form]
    FillForm --> SubmitDocs{Upload Documents?}
    
    SubmitDocs -->|Commercial Register| UploadCR[Upload Commercial Register]
    SubmitDocs -->|Tax Certificate| UploadTax[Upload Tax Certificate]
    SubmitDocs -->|Company Profile| UploadProfile[Upload Company Profile]
    SubmitDocs -->|Portfolio| UploadPortfolio[Upload Portfolio Files]
    
    UploadCR --> SelectServices[Select Service Categories]
    UploadTax --> SelectServices
    UploadProfile --> SelectServices
    UploadPortfolio --> SelectServices
    
    SelectServices --> SubmitApp[Submit Application]
    SubmitApp --> ValidateData{Validate All Data?}
    
    ValidateData -->|Valid| CreateAccount[Create Supplier Account]
    ValidateData -->|Invalid| ShowError[Show Validation Errors]
    ShowError --> FillForm
    
    CreateAccount --> SetPending[Set Status: pending]
    SetPending --> SendEmail[Send Confirmation Email]
    SendEmail --> AdminReview{Admin Review}
    
    AdminReview -->|Approved| SetApproved[Set Status: approved]
    AdminReview -->|Rejected| SetRejected[Set Status: rejected]
    SetRejected --> SendRejection[Send Rejection Email with Reason]
    
    SetApproved --> SendApproval[Send Approval Email]
    SendApproval --> EnableLogin[Enable Dashboard Access]
    EnableLogin --> SupplierLogin[Supplier Login]
    
    SupplierLogin --> ShowDashboard[Show Supplier Dashboard]
    ShowDashboard --> ViewQuotes{View Available Quotes}
    
    ViewQuotes -->|Service Match| ShowQuoteDetails[Show Quote Details]
    ViewQuotes -->|No Match| ShowNoQuotes[Show No Quotes Available]
    
    ShowQuoteDetails --> AcceptReject{Accept or Reject?}
    AcceptReject -->|Accept| CheckFirstCome[Check First-Come-First-Served]
    CheckFirstCome -->|Available| AcceptQuote[Accept Quote]
    AcceptQuote --> UpdateStatus[Update Quote Status]
    UpdateStatus --> NotifyCustomer[Notify Customer]
    NotifyCustomer --> TrackExecution[Track Order Execution]
    
    AcceptReject -->|Reject| AddReason[Add Rejection Reason]
    AddReason --> UpdateQuoteStatus[Update Quote Status]
    UpdateQuoteStatus --> NotifyRejection[Notify Customer of Rejection]
    
    CheckFirstCome -->|Already Taken| ShowTaken[Show Quote Already Taken]
    ShowTaken --> ViewQuotes
    
    TrackExecution --> OrderComplete([Order Completed Successfully])
    
    style Start fill:#e1f5fe
    style OrderComplete fill:#c8e6c9
    style SetRejected fill:#ffcdd2
    style SetApproved fill:#c8e6c9
```

## Quote Lifecycle States

### Quote Status Flow (State Diagram)

```mermaid
stateDiagram-v2
    [*] --> under_review: Customer Submits Quote
    
    under_review --> pending: Admin Initial Review
    under_review --> rejected: Admin Rejects
    
    pending --> approved: Admin Approves
    pending --> rejected: Admin Rejects
    
    approved --> booked: Customer Pays
    approved --> rejected: Admin Cancels
    
    booked --> completed: Event Executed
    booked --> cancelled: Customer/Admin Cancels
    
    rejected --> [*]: Final State
    completed --> [*]: Final State
    cancelled --> [*]: Final State
    
    state under_review {
        [*] --> waiting_admin
        waiting_admin --> [*]
    }
    
    state approved {
        [*] --> available_for_suppliers
        available_for_suppliers --> [*]
        Note: Suppliers can now accept quotes
    }
    
    state booked {
        [*] --> confirmed_booking
        confirmed_booking --> supplier_notified
        supplier_notified --> [*]
        Note: Payment confirmed, booking created
    }
```

## Payment Workflow States

### Payment Status Flow (State Diagram)

```mermaid
stateDiagram-v2
    [*] --> pending: Payment Initiated
    
    pending --> processing: Payment Gateway Processing
    pending --> failed: Validation Error
    
    processing --> completed: Payment Successful
    processing --> failed: Payment Declined
    processing --> cancelled: User Cancelled
    
    completed --> refunded: Refund Requested
    completed --> [*]: Final State
    
    failed --> [*]: Final State
    cancelled --> [*]: Final State
    refunded --> [*]: Final State
    
    state processing {
        [*] --> gateway_validation
        gateway_validation --> bank_processing
        bank_processing --> [*]
        Note: External payment gateway handling
    }
    
    state completed {
        [*] --> payment_confirmed
        payment_confirmed --> booking_created
        booking_created --> [*]
        Note: Automatic booking creation
    }
```

## Technical Implementation References

### Customer Registration Flow
- **Registration Form**: `AuthController::register()` - Validates and stores registration data
- **OTP Generation**: `OtpVerification::generate()` - Creates and sends verification code
- **OTP Verification**: `OtpController::completeRegistration()` - Validates OTP and creates account
- **Session Management**: Registration data stored in session between steps

### Quote Management System
- **Quote Creation**: `QuoteController::checkout()` - Converts cart to quote with 'under_review' status
- **Admin Approval**: `Admin\QuoteController::updateStatus()` - Changes quote status and notifies parties
- **Quote Display**: `QuoteController::show()` - Shows quote details to customer
- **PDF Generation**: `QuoteController::downloadPdf()` - Generates downloadable PDF using mPDF

### Payment Processing
- **Payment Form**: `QuoteController::showPayment()` - Displays payment form for approved quotes
- **Payment Processing**: `QuoteController::processPayment()` - Handles payment and creates booking
- **Payment Records**: `Payment::create()` - Creates payment transaction record
- **Booking Creation**: `Booking::create()` - Creates confirmed booking from quote

### Supplier Management
- **Registration**: `SupplierController::store()` - Creates supplier with 'pending' status
- **Admin Review**: `Admin\SupplierController::updateStatus()` - Approves/rejects supplier applications
- **Quote Visibility**: `SupplierDashboardController::quotes()` - Shows relevant quotes to suppliers
- **Quote Acceptance**: `SupplierDashboardController::acceptQuote()` - Handles quote acceptance with first-come-first-served logic

### Key Business Rules
1. **OTP Verification**: Required for all customer registrations and logins
2. **Quote Approval**: Admin must approve quotes before customer can pay
3. **Supplier Filtering**: Suppliers only see quotes containing their services
4. **First-Come-First-Served**: Only one supplier can accept each quote item
5. **Payment Confirmation**: Automatic booking creation upon successful payment
6. **Status Tracking**: Comprehensive status tracking throughout all workflows

### Notification System
- **Email Notifications**: Laravel Mail system for customer and supplier communications
- **Admin Notifications**: n8n integration for WhatsApp and Gmail notifications
- **Real-time Updates**: Session-based flash messages for immediate user feedback

This documentation provides a complete overview of the system workflows, enabling developers to understand the business logic and implement features consistently with existing patterns.