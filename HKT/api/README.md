# HKT Store API Documentation

## Base URL
```
http://localhost/TTHUONG/api
```

## Response Format
Tất cả APIs trả về JSON format:

```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

## Products API

### GET /products/list.php
Lấy danh sách sản phẩm

**Parameters:**
- `page` (int, optional): Số trang (default: 1)
- `limit` (int, optional): Số item mỗi trang (default: 12, max: 100)
- `category_id` (int, optional): Lọc theo danh mục
- `search` (string, optional): Tìm kiếm sản phẩm
- `sort` (string, optional): Sắp xếp (newest, oldest, price_asc, price_desc, popular)

**Example:**
```
GET /api/products/list.php?page=1&limit=12&sort=price_desc
```

**Response:**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "product_id": "001",
      "product_name": "Product Name",
      "price": 100000,
      "stock": 50,
      "sold": 10,
      "image": "image_url",
      "category": "Category Name",
      "description": "Short description..."
    }
  ],
  "pagination": {
    "total": 100,
    "page": 1,
    "limit": 12,
    "pages": 9
  }
}
```

### GET /products/detail.php
Lấy chi tiết sản phẩm

**Parameters:**
- `id` (string, required): Product ID

**Example:**
```
GET /api/products/detail.php?id=001
```

## Cart API

### GET /cart/get.php
Lấy giỏ hàng của user hiện tại (Cần đăng nhập)

**Headers:**
- Cookies: PHPSESSID (session)

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "cart_id": 1,
        "product_id": "001",
        "product_name": "Product",
        "price": 100000,
        "quantity": 2,
        "subtotal": 200000,
        "image": "image_url",
        "stock": 50
      }
    ],
    "total": 200000,
    "count": 1
  }
}
```

### POST /cart/add.php
Thêm sản phẩm vào giỏ hàng (Cần đăng nhập)

**Body:**
```json
{
  "product_id": "001",
  "quantity": 1
}
```

## Orders API

### POST /orders/create.php
Tạo đơn hàng (Cần đăng nhập)

**Body:**
```json
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "phone": "0123456789",
  "address": "123 Main Street",
  "payment_method": "cod"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "order_id": 1,
    "total": 200000,
    "payment_method": "cod",
    "status": "Chờ xác nhận"
  }
}
```

## Authentication API

### POST /auth/login.php
Đăng nhập

**Body:**
```json
{
  "username": "user",
  "password": "password123"
}
```

### POST /auth/register.php
Đăng ký

**Body:**
```json
{
  "username": "newuser",
  "email": "user@example.com",
  "password": "password123",
  "full_name": "John Doe"
}
```

## Reviews API

### GET /reviews/list.php
Lấy danh sách đánh giá của sản phẩm

**Parameters:**
- `product_id` (string, required): Product ID
- `page` (int, optional): Số trang (default: 1)
- `limit` (int, optional): Số item mỗi trang (default: 10, max: 50)

**Example:**
```
GET /api/reviews/list.php?product_id=001&page=1&limit=10
```

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error message"
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Please login first"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Server error occurred"
}
```

## Usage Examples

### JavaScript/Fetch
```javascript
// Get products
fetch('/api/products/list.php?page=1&limit=12')
  .then(res => res.json())
  .then(data => console.log(data));

// Add to cart
fetch('/api/cart/add.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    product_id: '001',
    quantity: 1
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

### jQuery/AJAX
```javascript
// Get products
$.ajax({
  url: '/api/products/list.php',
  data: { page: 1, limit: 12 },
  success: function(data) {
    console.log(data);
  }
});
```

## Rate Limiting
Hiện tại không có rate limiting, nhưng sẽ được thêm trong tương lai.

## Authentication
Các endpoints yêu cầu authentication sẽ kiểm tra session PHP. Đảm bảo user đã login trước khi gọi.

## CORS
APIs đã enable CORS, có thể được gọi từ cross-origin requests.
