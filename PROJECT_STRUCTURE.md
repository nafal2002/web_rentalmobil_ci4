# Project Structure - Enterprise Version

## Directory Layout

```
web_rentalmobil_ci4/
├── app/
│   ├── Config/                          # Configuration files
│   │   ├── Routes.php                   # Enhanced with API routes
│   │   ├── Database.php
│   │   └── ...
│   ├── Controllers/
│   │   ├── Home.php                     # Frontend controller
│   │   ├── Product.php
│   │   ├── Account.php
│   │   ├── BookingManagement.php        # NEW: Booking management
│   │   ├── PaymentManagement.php        # NEW: Payment management
│   │   ├── ReportingAnalytics.php       # NEW: Reporting
│   │   ├── GPSTrackingController.php    # NEW: GPS tracking
│   │   ├── Dashboard.php                # NEW: Dashboard
│   │   └── Api/                         # NEW: REST API controllers
│   │       ├── BookingController.php
│   │       ├── PaymentController.php
│   │       ├── AnalyticsController.php
│   │       ├── ReviewController.php
│   │       └── DashboardController.php
│   ├── Models/
│   │   ├── Product_model.php            # Existing
│   │   ├── Booking_model.php            # NEW: Booking model
│   │   ├── Payment_model.php            # NEW: Payment model
│   │   ├── Analytics_model.php          # NEW: Analytics model
│   │   ├── Review_model.php             # NEW: Review model
│   │   └── ...
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2024_07_08_001_AddPaymentSystem.php
│   │   │   └── 2024_07_08_002_AddAnalyticsTracking.php
│   │   └── db_rentalmobil.sql
│   ├── Views/
│   │   ├── frontend/
│   │   │   └── v_dashboard_customer.php
│   │   └── backend/
│   │       ├── v_booking_management.php
│   │       ├── v_payment_management.php
│   │       ├── v_report_daily.php
│   │       ├── v_gps_tracking.php
│   │       └── ...
│   ├── Utilities/                       # NEW: Utility classes
│   │   ├── PaymentGateway.php           # Midtrans, Stripe integration
│   │   ├── NotificationService.php      # SMS, Email, WhatsApp
│   │   └── GPSTracking.php              # GPS tracking utilities
│   ├── Language/
│   │   └── id/
│   │       ├── Validation.php           # NEW: Indonesian validation messages
│   │       └── ...
│   └── Common.php
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   │   └── api-client.js            # NEW: API client library
│   │   └── images/
│   └── ...
├── writable/
│   ├── cache/
│   ├── logs/
│   └── uploads/
├── .env                                 # Environment configuration
├── .env.example                         # NEW: Environment template
├── composer.json
├── composer.lock
├── UPGRADE_GUIDE.md                     # NEW: Upgrade documentation
├── API_DOCUMENTATION.md                 # NEW: API documentation
├── PROJECT_STRUCTURE.md                 # NEW: This file
└── README.md
```

## Database Tables (NEW)

### tabel_booking
Manages all rental bookings with detailed tracking.
```
Fields:
- booking_id (PK)
- customer_id (FK)
- car_id (FK)
- booking_date_from
- booking_date_to
- booking_status (PENDING, CONFIRMED, ACTIVE, COMPLETED, CANCELLED)
- booking_total_price
- booking_notes
- driver_id (FK, optional)
- pickup_location
- dropoff_location
- gps_latitude
- gps_longitude
- created_at, updated_at
```

### tabel_payment
Tracks all payment transactions.
```
Fields:
- payment_id (PK)
- booking_id (FK)
- payment_amount
- payment_method (TRANSFER, CARD, E_WALLET, CASH)
- payment_status (PENDING, COMPLETED, FAILED, REFUNDED)
- payment_reference
- payment_gateway (midtrans, stripe, etc)
- created_at, updated_at
```

### tabel_gps_tracking
Records GPS locations in real-time.
```
Fields:
- tracking_id (PK)
- booking_id (FK)
- latitude
- longitude
- speed
- timestamp
```

### tabel_analytics
Stores daily analytics data.
```
Fields:
- analytics_id (PK)
- analytics_date
- total_bookings
- total_revenue
- total_customers
- average_rating
- occupancy_rate
- created_at
```

### tabel_reviews
Customer reviews and ratings.
```
Fields:
- review_id (PK)
- booking_id (FK)
- rating (1-5)
- review_comment
- created_at
```

## API Endpoints

### Bookings
- `GET /api/v1/bookings`
- `POST /api/v1/bookings`
- `GET /api/v1/bookings/{id}`
- `PUT /api/v1/bookings/{id}`
- `DELETE /api/v1/bookings/{id}`

### Payments
- `GET /api/v1/payments`
- `POST /api/v1/payments`
- `PUT /api/v1/payments/{id}/status`
- `GET /api/v1/payments/revenue`

### Analytics
- `GET /api/v1/analytics`
- `GET /api/v1/analytics/latest`
- `POST /api/v1/analytics/calculate`

### Reviews
- `POST /api/v1/reviews`
- `GET /api/v1/reviews/{bookingId}`
- `GET /api/v1/reviews/average`

### Dashboard
- `GET /api/v1/dashboard/admin`
- `GET /api/v1/dashboard/customer/{customerId}`
- `GET /api/v1/dashboard/revenue`

## Key Features

### 1. Booking Management
- Create, read, update, cancel bookings
- Assign drivers to bookings
- Track booking status
- Support multiple locations

### 2. Payment Processing
- Midtrans integration
- Stripe integration
- Multiple payment methods
- Payment status tracking
- Revenue analytics

### 3. GPS Tracking
- Real-time location tracking
- Location history
- Speed monitoring
- Distance calculation

### 4. Notifications
- SMS via Twilio
- Email via SMTP
- WhatsApp messages
- Push notifications

### 5. Analytics & Reporting
- Daily analytics
- Revenue tracking
- Occupancy rate
- Customer satisfaction ratings
- Customizable reports

## Setup Instructions

### 1. Database Migration
```bash
php spark migrate
```

### 2. Environment Configuration
```bash
cp .env.example .env
# Edit .env with your configuration
```

### 3. Install Dependencies
```bash
composer install
```

### 4. Clear Cache
```bash
php spark cache:clear
```

## Development Workflow

### Adding New Feature
1. Create migration for database changes
2. Create model for data access
3. Create controller for business logic
4. Create views/API endpoints
5. Add tests
6. Update documentation

### Code Structure Best Practices
- Models handle database queries
- Controllers handle business logic
- Views handle presentation
- Utilities handle shared functionality
- Language files handle localization

## Security Considerations

1. **Authentication**: Implement JWT for API
2. **Authorization**: Role-based access control
3. **Validation**: Input validation on all endpoints
4. **CORS**: Configure for cross-origin requests
5. **HTTPS**: Enable in production
6. **Rate Limiting**: Implement on API endpoints
7. **SQL Injection**: Use parameterized queries (already using ORM)
8. **XSS Protection**: Use view templating with escaping

## Performance Optimization

1. **Caching**: Use Redis for session and query caching
2. **Database**: Add indexes on frequently queried fields
3. **API Response**: Implement pagination
4. **Frontend**: Minify assets
5. **Images**: Optimize and compress images

## Testing

### Unit Tests
```bash
php spark test
```

### API Testing
Use Postman or similar tools to test API endpoints.

## Deployment

### Production Checklist
- [ ] Set CI_ENVIRONMENT to 'production'
- [ ] Configure .env for production
- [ ] Enable HTTPS
- [ ] Setup database backups
- [ ] Configure logging
- [ ] Setup monitoring
- [ ] Test all API endpoints
- [ ] Load testing

## Monitoring & Logging

Logs are stored in `writable/logs/`

## Support & Maintenance

For issues or questions, please refer to:
- UPGRADE_GUIDE.md
- API_DOCUMENTATION.md
- CodeIgniter 4 documentation

---

**Last Updated**: 2024-07-08
**Version**: 1.0 Enterprise
