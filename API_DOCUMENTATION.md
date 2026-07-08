# REST API Documentation

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
Gunakan API Token atau Bearer Token di header:
```
Authorization: Bearer YOUR_TOKEN
```

## Response Format
Semua response dalam format JSON.

### Success Response
```json
{
  "status": 200,
  "data": { ... },
  "message": "Success"
}
```

### Error Response
```json
{
  "status": 400,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

---

## 📅 Bookings API

### Create Booking
**POST** `/bookings`

**Request Body:**
```json
{
  "customer_id": 1,
  "car_id": 1,
  "booking_date_from": "2024-07-15 10:00:00",
  "booking_date_to": "2024-07-17 10:00:00",
  "booking_total_price": 500000,
  "pickup_location": "Hotel A",
  "dropoff_location": "Airport",
  "booking_notes": "Extra driver needed"
}
```

**Response:**
```json
{
  "status": 201,
  "data": {
    "booking_id": 1,
    "message": "Booking created successfully"
  }
}
```

---

### Get All Bookings
**GET** `/bookings`

**Query Parameters:**
- `status` - Filter by status (PENDING, CONFIRMED, ACTIVE, COMPLETED, CANCELLED)
- `page` - Pagination
- `limit` - Items per page

**Response:**
```json
{
  "status": 200,
  "data": [
    {
      "booking_id": 1,
      "customer_id": 1,
      "car_id": 1,
      "booking_status": "CONFIRMED",
      "booking_date_from": "2024-07-15 10:00:00",
      "booking_date_to": "2024-07-17 10:00:00",
      "booking_total_price": 500000,
      "penyewa_nama": "John Doe",
      "mobil_no_polisi": "A 1234 BC"
    }
  ]
}
```

---

### Get Booking Detail
**GET** `/bookings/{id}`

**Response:**
```json
{
  "status": 200,
  "data": {
    "booking_id": 1,
    "customer_id": 1,
    "penyewa_nama": "John Doe",
    "penyewa_email": "john@example.com",
    "car_id": 1,
    "mobil_no_polisi": "A 1234 BC",
    "merk_nama": "Honda",
    "jenis_nama": "Sedan",
    "booking_status": "ACTIVE",
    "booking_date_from": "2024-07-15 10:00:00",
    "booking_date_to": "2024-07-17 10:00:00",
    "pickup_location": "Hotel A",
    "dropoff_location": "Airport",
    "gps_latitude": "-6.2088",
    "gps_longitude": "106.8456",
    "driver_nama": "Ahmad"
  }
}
```

---

### Update Booking
**PUT** `/bookings/{id}`

**Request Body:**
```json
{
  "booking_status": "CONFIRMED",
  "driver_id": 2,
  "booking_notes": "Updated notes"
}
```

---

### Cancel Booking
**DELETE** `/bookings/{id}`

**Response:**
```json
{
  "status": 200,
  "message": "Booking cancelled successfully"
}
```

---

### Get Customer Bookings
**GET** `/bookings/customer/{customerId}`

**Response:**
```json
{
  "status": 200,
  "data": [
    { ... },
    { ... }
  ]
}
```

---

## 💳 Payments API

### Create Payment
**POST** `/payments`

**Request Body:**
```json
{
  "booking_id": 1,
  "payment_amount": 500000,
  "payment_method": "TRANSFER",
  "payment_gateway": "midtrans"
}
```

**Response:**
```json
{
  "status": 201,
  "data": {
    "payment_id": 1,
    "message": "Payment created successfully"
  }
}
```

---

### Get All Payments
**GET** `/payments`

**Query Parameters:**
- `status` - Filter by status (PENDING, COMPLETED, FAILED, REFUNDED)
- `method` - Filter by method (TRANSFER, CARD, E_WALLET, CASH)

**Response:**
```json
{
  "status": 200,
  "data": [
    {
      "payment_id": 1,
      "booking_id": 1,
      "payment_amount": 500000,
      "payment_method": "TRANSFER",
      "payment_status": "COMPLETED",
      "payment_reference": "TXN123456",
      "penyewa_nama": "John Doe",
      "penyewa_email": "john@example.com"
    }
  ]
}
```

---

### Update Payment Status
**PUT** `/payments/{id}/status`

**Request Body:**
```json
{
  "payment_status": "COMPLETED"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "Payment status updated"
}
```

---

### Get Revenue
**GET** `/payments/revenue?from=2024-07-01&to=2024-07-31`

**Query Parameters:**
- `from` - Start date (YYYY-MM-DD)
- `to` - End date (YYYY-MM-DD)

**Response:**
```json
{
  "status": 200,
  "data": {
    "total_revenue": 5000000
  }
}
```

---

## 📊 Analytics API

### Get Analytics by Date Range
**GET** `/analytics?from=2024-07-01&to=2024-07-31`

**Query Parameters:**
- `from` - Start date (YYYY-MM-DD)
- `to` - End date (YYYY-MM-DD)

**Response:**
```json
{
  "status": 200,
  "data": [
    {
      "analytics_date": "2024-07-01",
      "total_bookings": 5,
      "total_revenue": 2500000,
      "total_customers": 4,
      "average_rating": 4.5,
      "occupancy_rate": 75.5
    }
  ]
}
```

---

### Get Latest Analytics
**GET** `/analytics/latest`

**Response:**
```json
{
  "status": 200,
  "data": {
    "analytics_date": "2024-07-08",
    "total_bookings": 8,
    "total_revenue": 4000000,
    "total_customers": 7,
    "average_rating": 4.7,
    "occupancy_rate": 80.0
  }
}
```

---

### Calculate Daily Analytics
**POST** `/analytics/calculate`

**Request Body:**
```json
{
  "date": "2024-07-08"
}
```

**Response:**
```json
{
  "status": 201,
  "message": "Analytics calculated successfully"
}
```

---

## ⭐ Reviews API

### Create Review
**POST** `/reviews`

**Request Body:**
```json
{
  "booking_id": 1,
  "rating": 5,
  "review_comment": "Great service! Driver is very friendly."
}
```

**Validation:**
- `rating` must be 1-5
- `review_comment` optional

**Response:**
```json
{
  "status": 201,
  "data": {
    "review_id": 1,
    "message": "Review created successfully"
  }
}
```

---

### Get Reviews by Booking
**GET** `/reviews/{bookingId}`

**Response:**
```json
{
  "status": 200,
  "data": [
    {
      "review_id": 1,
      "booking_id": 1,
      "rating": 5,
      "review_comment": "Great service!",
      "created_at": "2024-07-08 10:30:00"
    }
  ]
}
```

---

### Get Average Rating
**GET** `/reviews/average`

**Response:**
```json
{
  "status": 200,
  "data": {
    "average_rating": 4.6
  }
}
```

---

## 📈 Dashboard API

### Get Admin Dashboard
**GET** `/dashboard/admin`

**Response:**
```json
{
  "status": 200,
  "data": {
    "total_bookings": 45,
    "active_bookings": 8,
    "pending_bookings": 3,
    "total_revenue": 22500000,
    "total_customers": 35,
    "average_rating": 4.6,
    "total_cars": 10,
    "recent_bookings": [ ... ]
  }
}
```

---

### Get Customer Dashboard
**GET** `/dashboard/customer/{customerId}`

**Response:**
```json
{
  "status": 200,
  "data": {
    "customer": { ... },
    "total_bookings": 5,
    "completed_bookings": 4,
    "total_spent": 2000000,
    "recent_bookings": [ ... ]
  }
}
```

---

### Get Revenue Statistics
**GET** `/dashboard/revenue?period=monthly&year=2024`

**Query Parameters:**
- `period` - daily, weekly, monthly (default: monthly)
- `year` - Year (default: current year)

**Response:**
```json
{
  "status": 200,
  "data": {
    "period": "monthly",
    "year": 2024,
    "data": [
      {
        "month": 7,
        "amount": 5000000
      },
      {
        "month": 6,
        "amount": 4500000
      }
    ]
  }
}
```

---

## ❌ Error Codes

| Code | Message | HTTP Status |
|------|---------|-------------|
| 400 | Bad Request | 400 |
| 401 | Unauthorized | 401 |
| 403 | Forbidden | 403 |
| 404 | Not Found | 404 |
| 409 | Conflict | 409 |
| 422 | Unprocessable Entity | 422 |
| 500 | Internal Server Error | 500 |

---

## 🧪 Testing API

### Using cURL
```bash
# Create Booking
curl -X POST http://localhost:8080/api/v1/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "car_id": 1,
    "booking_date_from": "2024-07-15 10:00:00",
    "booking_date_to": "2024-07-17 10:00:00",
    "booking_total_price": 500000
  }'

# Get All Bookings
curl -X GET http://localhost:8080/api/v1/bookings

# Get Dashboard
curl -X GET http://localhost:8080/api/v1/dashboard/admin
```

### Using Postman
1. Import the API collection
2. Set base URL: `{{base_url}}/api/v1`
3. Add environment variable: `base_url = http://localhost:8080`
4. Test each endpoint

---

## 📱 Mobile Integration Example

```javascript
// React Native / Flutter Example
const bookingData = {
  customer_id: 1,
  car_id: 1,
  booking_date_from: new Date().toISOString(),
  booking_date_to: new Date(Date.now() + 86400000).toISOString(),
  pickup_location: 'Hotel A',
  dropoff_location: 'Airport'
};

fetch('http://api.rentalmobil.com/api/v1/bookings', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify(bookingData)
})
.then(res => res.json())
.then(data => console.log('Booking created:', data))
.catch(err => console.error('Error:', err));
```

---

## Version History

| Version | Date | Changes |
|---------|------|----------|
| 1.0 | 2024-07-08 | Initial release with booking, payment, analytics APIs |

---

**Last Updated:** 2024-07-08
