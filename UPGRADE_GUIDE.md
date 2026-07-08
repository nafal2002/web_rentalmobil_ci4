# Enterprise Upgrade Guide

Panduan lengkap untuk upgrade sistem Rental Mobil Anda ke versi Enterprise.

## 🚀 Apa yang Ditambahkan

### 1. **Payment System** 💳
- Integrasi Midtrans dan Stripe
- Track status pembayaran (PENDING, COMPLETED, FAILED, REFUNDED)
- Payment reference untuk setiap transaksi

### 2. **Booking Management** 📅
- Detail booking dengan status komprehensif
- Support untuk pickup/dropoff locations
- GPS coordinate tracking
- Driver assignment

### 3. **GPS Tracking** 📍
- Real-time location tracking
- Location history untuk setiap booking
- Distance calculation menggunakan Haversine formula
- Speed tracking

### 4. **Analytics Dashboard** 📊
- Daily revenue tracking
- Booking statistics
- Customer satisfaction (ratings)
- Occupancy rate

### 5. **Notification System** 🔔
- Email notifications (SMTP)
- SMS notifications (Twilio)
- WhatsApp notifications
- Push notifications support

### 6. **REST API** 🔌
- RESTful endpoints untuk semua operasi
- JSON response format
- Proper HTTP status codes
- Versioned API (v1)

## 📋 Setup Instructions

### Step 1: Run Database Migrations
```bash
php spark migrate
```

Migrations akan membuat:
- `tabel_booking` - Booking details
- `tabel_payment` - Payment records
- `tabel_gps_tracking` - GPS location history
- `tabel_analytics` - Daily analytics
- `tabel_reviews` - Customer reviews

### Step 2: Configure Environment Variables

Copy `.env.example` ke `.env`:
```bash
cp .env.example .env
```

Isi konfigurasi sesuai environment Anda:

**Database Configuration:**
```
database.default.hostname = localhost
database.default.database = db_rentalmobil
database.default.username = root
database.default.password = your_password
```

**Payment Gateway (Midtrans):**
```
MIDTRANS_MERCHANT_ID = your_merchant_id
MIDTRANS_CLIENT_KEY = your_client_key
MIDTRANS_SERVER_KEY = your_server_key
MIDTRANS_ENV = sandbox  # atau production
```

**SMS Notifications (Twilio):**
```
TWILIO_ACCOUNT_SID = your_account_sid
TWILIO_AUTH_TOKEN = your_auth_token
TWILIO_PHONE_NUMBER = +1234567890
```

**Email Configuration:**
```
SMTP_HOST = smtp.gmail.com
SMTP_USER = your_email@gmail.com
SMTP_PASS = your_app_password
SMTP_PORT = 587
```

### Step 3: Composer Dependencies

Instal dependencies baru:
```bash
composer require firebase/php-jwt
composer require guzzlehttp/guzzle
```

## 🔌 API Documentation

### Base URL
```
http://your-domain/api/v1
```

### Authentication
Gunakan API token di header:
```
Authorization: Bearer YOUR_API_TOKEN
```

### Endpoints

#### Bookings
- `GET /bookings` - List semua booking
- `POST /bookings` - Buat booking baru
- `GET /bookings/{id}` - Detail booking
- `PUT /bookings/{id}` - Update booking
- `DELETE /bookings/{id}` - Cancel booking
- `GET /bookings/customer/{customerId}` - Booking per customer

**Contoh POST /bookings:**
```json
{
  "customer_id": 1,
  "car_id": 1,
  "booking_date_from": "2024-07-15 10:00:00",
  "booking_date_to": "2024-07-17 10:00:00",
  "pickup_location": "Hotel A",
  "dropoff_location": "Airport",
  "booking_notes": "Extra driver needed"
}
```

#### Payments
- `GET /payments` - List semua pembayaran
- `POST /payments` - Buat pembayaran
- `GET /payments/{id}` - Detail pembayaran
- `PUT /payments/{id}/status` - Update status pembayaran
- `GET /payments/revenue?from=2024-07-01&to=2024-07-31` - Total revenue

**Contoh POST /payments:**
```json
{
  "booking_id": 1,
  "payment_amount": 500000,
  "payment_method": "TRANSFER",
  "payment_gateway": "midtrans"
}
```

#### Analytics
- `GET /analytics?from=2024-07-01&to=2024-07-31` - Analytics by date range
- `GET /analytics/latest` - Latest analytics
- `POST /analytics/calculate` - Calculate daily analytics

#### Reviews
- `GET /reviews/{bookingId}` - Reviews untuk booking
- `POST /reviews` - Buat review
- `GET /reviews/average` - Average rating

**Contoh POST /reviews:**
```json
{
  "booking_id": 1,
  "rating": 5,
  "review_comment": "Great service!"
}
```

## 🛠️ Using Utilities

### Payment Processing
```php
use App\Utilities\PaymentGateway;

$paymentData = [
    'booking_id' => 1,
    'amount' => 500000,
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_phone' => '+62812345678'
];

$result = PaymentGateway::processMidtrans($paymentData);
```

### Sending Notifications
```php
use App\Utilities\NotificationService;

// Send SMS
NotificationService::sendSMS("+62812345678", "Your booking is confirmed!");

// Send Email
NotificationService::sendEmail(
    "customer@example.com",
    "Booking Confirmation",
    "<h1>Your booking is confirmed</h1>"
);

// Send WhatsApp
NotificationService::sendWhatsApp("+62812345678", "Your booking is ready!");
```

### GPS Tracking
```php
use App\Utilities\GPSTracking;

// Record location
GPSTracking::recordLocation(
    $bookingId,
    -6.2088,  // latitude
    106.8456, // longitude
    60        // speed km/h
);

// Get current location
$location = GPSTracking::getCurrentLocation($bookingId);

// Get location history
$history = GPSTracking::getLocationHistory($bookingId);

// Calculate distance
$distance = GPSTracking::calculateDistance(
    -6.2088, 106.8456,
    -6.2100, 106.8500
);
```

## 📱 Frontend Integration Example

### Create Booking (JavaScript)
```javascript
fetch('/api/v1/bookings', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    customer_id: 1,
    car_id: 1,
    booking_date_from: '2024-07-15 10:00:00',
    booking_date_to: '2024-07-17 10:00:00',
    pickup_location: 'Hotel A',
    dropoff_location: 'Airport'
  })
})
.then(response => response.json())
.then(data => console.log('Booking created:', data));
```

### Get Dashboard Analytics (JavaScript)
```javascript
fetch('/api/v1/analytics/latest')
  .then(response => response.json())
  .then(data => {
    console.log('Total Revenue:', data.total_revenue);
    console.log('Total Bookings:', data.total_bookings);
    console.log('Average Rating:', data.average_rating);
  });
```

## 🔐 Security Best Practices

1. **Never commit `.env` file** - Selalu gunakan `.env.example` untuk template
2. **Use HTTPS** - Enable `app.forceGlobalSecureRequests = true` di production
3. **API Authentication** - Implement JWT atau API token validation
4. **CORS Configuration** - Configure CORS untuk cross-origin requests
5. **Rate Limiting** - Implement rate limiting untuk API endpoints
6. **Input Validation** - Validate semua input dari client

## 📊 Database Schema

### tabel_booking
```sql
- booking_id (Primary Key)
- customer_id (Foreign Key)
- car_id (Foreign Key)
- booking_date_from
- booking_date_to
- booking_status
- booking_total_price
- booking_notes
- driver_id (Foreign Key, Optional)
- pickup_location
- dropoff_location
- gps_latitude
- gps_longitude
```

### tabel_payment
```sql
- payment_id (Primary Key)
- booking_id (Foreign Key)
- payment_amount
- payment_method
- payment_status
- payment_reference
- payment_gateway
- created_at
- updated_at
```

## 🚀 Next Steps

1. **Implement JWT Authentication** untuk API security
2. **Add WebSocket** untuk real-time notifications
3. **Create Admin Dashboard UI** untuk analytics
4. **Develop Mobile App** menggunakan Flutter/React Native
5. **Implement Caching** menggunakan Redis
6. **Add Unit Tests** untuk API endpoints
7. **Setup CI/CD Pipeline** untuk automated deployment

## 📞 Support

Untuk pertanyaan atau masalah, silakan hubungi tim support Anda.

Happy coding! 🎉
