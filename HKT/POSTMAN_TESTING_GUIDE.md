# Hướng Dẫn Kiểm Thử APIs Bằng Postman

## 1. Cài Đặt Postman

### Download
- Truy cập: https://www.postman.com/downloads/
- Download phiên bản phù hợp với OS của bạn (Windows/Mac/Linux)
- Cài đặt và mở Postman

### Đăng nhập (tùy chọn)
- Tạo tài khoản Postman hoặc đăng nhập
- Điều này giúp lưu collections và sử dụng trên nhiều thiết bị

---

## 2. Tạo Collection

### Bước 1: Tạo workspace mới
1. Click **"New"** ở góc trái
2. Chọn **"Collection"**
3. Đặt tên: `HKT Store API`
4. Click **"Create"**

### Bước 2: Thêm Environment
1. Click **"Environments"** ở sidebar trái
2. Click **"Create"** hoặc **"+"**
3. Đặt tên: `Local Development`
4. Thêm variables:
   ```
   base_url: http://localhost/TTHUONG/api
   username: xali
   password: password123
   user_id: 3
   product_id: 001
   ```
5. Click **"Save"**

---

## 3. Test Từng API

### 3.1 Products API

#### GET - Danh sách sản phẩm

**Step:**
1. Click **"New"** → **"Request"**
2. Đặt tên: `GET Products List`
3. Method: **GET**
4. URL: `{{base_url}}/products/list.php`
5. Vào tab **"Params"** thêm:
   - `page`: 1
   - `limit`: 12
   - `sort`: newest
6. Click **"Send"**

**Expected Response:**
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
      "category": "Category",
      "description": "..."
    }
  ],
  "pagination": {
    "total": 15,
    "page": 1,
    "limit": 12,
    "pages": 2
  }
}
```

#### GET - Chi tiết sản phẩm

**Step:**
1. Click **"New"** → **"Request"**
2. Đặt tên: `GET Product Detail`
3. Method: **GET**
4. URL: `{{base_url}}/products/detail.php?id={{product_id}}`
5. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "product_id": "001",
    "product_name": "Product",
    "price": 100000,
    "stock": 50,
    "sold": 10,
    "image": "image_url",
    "category_id": 1,
    "category_name": "Category",
    "description": "Full description",
    "in_stock": true
  }
}
```

---

### 3.2 Authentication API

#### POST - Đăng nhập

**Step:**
1. Click **"New"** → **"Request"**
2. Đặt tên: `POST Login`
3. Method: **POST**
4. URL: `{{base_url}}/auth/login.php`
5. Tab **"Body"**, chọn **"raw"** + **"JSON"**
6. Nhập:
```json
{
  "username": "xali",
  "password": "password123"
}
```
7. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user_id": 3,
    "username": "xali",
    "email": "dubu2k4@gmail.com",
    "full_name": "Trần Thanh Thưởng"
  }
}
```

#### POST - Đăng ký

**Step:**
1. Click **"New"** → **"Request"**
2. Đặt tên: `POST Register`
3. Method: **POST**
4. URL: `{{base_url}}/auth/register.php`
5. Body (raw JSON):
```json
{
  "username": "testuser123",
  "email": "test@example.com",
  "password": "password123",
  "full_name": "Test User"
}
```
6. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user_id": 6,
    "username": "testuser123",
    "email": "test@example.com",
    "full_name": "Test User"
  }
}
```

---

### 3.3 Cart API

#### POST - Thêm vào giỏ hàng

⚠️ **YÊU CẦU**: Phải đăng nhập trước

**Step:**
1. Click **"New"** → **"Request"**
2. Đặt tên: `POST Add to Cart`
3. Method: **POST**
4. URL: `{{base_url}}/cart/add.php`
5. Tab **"Cookies"**: Kiểm tra có PHPSESSID chưa
   - Nếu đã login thì sẽ có
   - Nếu chưa, phải login trước (xem 3.2)
6. Body (raw JSON):
```json
{
  "product_id": "001",
  "quantity": 2
}
```
7. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "message": "Added to cart successfully",
  "data": {
    "product_name": "Product Name",
    "quantity": 2
  }
}
```

#### GET - Lấy giỏ hàng

**Step:**
1. Đặt tên: `GET Cart`
2. Method: **GET**
3. URL: `{{base_url}}/cart/get.php`
4. Click **"Send"**

**Expected Response:**
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

---

### 3.4 Orders API

#### POST - Tạo đơn hàng

⚠️ **YÊU CẦU**: 
- Phải đăng nhập
- Giỏ hàng phải có sản phẩm

**Step:**
1. Đặt tên: `POST Create Order`
2. Method: **POST**
3. URL: `{{base_url}}/orders/create.php`
4. Body (raw JSON):
```json
{
  "full_name": "Trần Thanh Thưởng",
  "email": "dubu2k4@gmail.com",
  "phone": "0392656499",
  "address": "Cầu Ngang, Trà Vinh",
  "payment_method": "cod"
}
```
5. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "order_id": 8,
    "total": 200000,
    "payment_method": "cod",
    "status": "Chờ xác nhận"
  }
}
```

#### GET - Danh sách đơn hàng

**Step:**
1. Đặt tên: `GET Orders List`
2. Method: **GET**
3. URL: `{{base_url}}/orders/list.php`
4. Params (tùy chọn):
   - `page`: 1
   - `limit`: 10
   - `status`: Chờ xác nhận
5. Click **"Send"**

#### GET - Chi tiết đơn hàng

**Step:**
1. Đặt tên: `GET Order Detail`
2. Method: **GET**
3. URL: `{{base_url}}/orders/detail.php?id=1`
4. Click **"Send"**

---

### 3.5 Wishlist API

#### POST - Thêm vào wishlist

**Step:**
1. Đặt tên: `POST Add to Wishlist`
2. Method: **POST**
3. URL: `{{base_url}}/wishlist/add.php`
4. Body:
```json
{
  "product_id": "001"
}
```
5. Click **"Send"**

#### GET - Lấy wishlist

**Step:**
1. Đặt tên: `GET Wishlist`
2. Method: **GET**
3. URL: `{{base_url}}/wishlist/get.php`
4. Click **"Send"**

#### POST - Xóa khỏi wishlist

**Step:**
1. Đặt tên: `POST Remove from Wishlist`
2. Method: **POST**
3. URL: `{{base_url}}/wishlist/remove.php`
4. Body:
```json
{
  "product_id": "001"
}
```

---

### 3.6 User API

#### GET - Lấy thông tin cá nhân

**Step:**
1. Đặt tên: `GET User Profile`
2. Method: **GET**
3. URL: `{{base_url}}/user/profile.php`
4. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "user_id": 3,
    "username": "xali",
    "email": "dubu2k4@gmail.com",
    "full_name": "Trần Thanh Thưởng",
    "phone": "0392656499",
    "address": "Cầu Ngang, Trà Vinh",
    "created_at": "2024-12-04 17:49:41"
  }
}
```

#### POST - Cập nhật thông tin

**Step:**
1. Đặt tên: `POST Update Profile`
2. Method: **POST**
3. URL: `{{base_url}}/user/update-profile.php`
4. Body:
```json
{
  "full_name": "Trần Thanh Thưởng",
  "phone": "0392656499",
  "address": "123 New Address"
}
```
5. Click **"Send"**

---

### 3.7 Categories API

#### GET - Danh sách danh mục

**Step:**
1. Đặt tên: `GET Categories`
2. Method: **GET**
3. URL: `{{base_url}}/categories/list.php`
4. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "category_id": 1,
      "category_name": "Tượng",
      "description": "Tượng trang trí",
      "product_count": 5
    }
  ]
}
```

---

### 3.8 Reviews API

#### GET - Danh sách đánh giá

**Step:**
1. Đặt tên: `GET Reviews`
2. Method: **GET**
3. URL: `{{base_url}}/reviews/list.php?product_id=001&page=1&limit=10`
4. Click **"Send"**

#### POST - Tạo đánh giá

**Step:**
1. Đặt tên: `POST Create Review`
2. Method: **POST**
3. URL: `{{base_url}}/reviews/create.php`
4. Body:
```json
{
  "product_id": "001",
  "rating": 5,
  "content": "Sản phẩm rất tốt, chất lượng cao, giao hàng nhanh. Sẽ mua lại lần tới!"
}
```
5. Click **"Send"**

---

### 3.9 Search API

#### GET - Tìm kiếm sản phẩm

**Step:**
1. Đặt tên: `GET Search Products`
2. Method: **GET**
3. URL: `{{base_url}}/products/search.php`
4. Params:
   - `q`: tượng
   - `category`: 1 (tùy chọn)
   - `min_price`: 100000 (tùy chọn)
   - `max_price`: 500000 (tùy chọn)
   - `page`: 1
   - `limit`: 12
5. Click **"Send"**

---

### 3.10 Stats API

#### GET - Thống kê dashboard

**Step:**
1. Đặt tên: `GET Dashboard Stats`
2. Method: **GET**
3. URL: `{{base_url}}/stats/dashboard.php`
4. Click **"Send"**

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "total_orders": 5,
    "total_spent": 1500000,
    "wishlist_count": 3,
    "reviews_count": 2
  }
}
```

---

## 4. Test Cases Scenario

### Scenario 1: Mua Hàng Hoàn Chỉnh

**Thứ tự thực hiện:**

1. ✅ **Xem danh sách sản phẩm**
   - GET `/api/products/list.php`

2. ✅ **Xem chi tiết sản phẩm**
   - GET `/api/products/detail.php?id=001`

3. ✅ **Đăng nhập**
   - POST `/api/auth/login.php`
   - Lưu PHPSESSID từ cookies

4. ✅ **Thêm vào giỏ hàng**
   - POST `/api/cart/add.php`
   - Body: `{"product_id": "001", "quantity": 1}`

5. ✅ **Xem giỏ hàng**
   - GET `/api/cart/get.php`

6. ✅ **Tạo đơn hàng**
   - POST `/api/orders/create.php`
   - Lấy order_id từ response

7. ✅ **Xem đơn hàng**
   - GET `/api/orders/list.php`
   - GET `/api/orders/detail.php?id=<order_id>`

8. ✅ **Đánh giá sản phẩm** (sau khi nhận hàng)
   - POST `/api/reviews/create.php`

### Scenario 2: Quản Lý Wishlist

**Thứ tự:**

1. ✅ Đăng nhập
2. ✅ Thêm vào wishlist: POST `/api/wishlist/add.php`
3. ✅ Xem wishlist: GET `/api/wishlist/get.php`
4. ✅ Xóa khỏi wishlist: POST `/api/wishlist/remove.php`

### Scenario 3: Quản Lý Tài Khoản

**Thứ tự:**

1. ✅ Đăng nhập: POST `/api/auth/login.php`
2. ✅ Xem profile: GET `/api/user/profile.php`
3. ✅ Cập nhật profile: POST `/api/user/update-profile.php`
4. ✅ Xem thống kê: GET `/api/stats/dashboard.php`

---

## 5. Xử Lý Errors

### Error Handling

| Status | Meaning | Example |
|--------|---------|---------|
| 200 | Thành công | ✅ Success response |
| 201 | Tạo mới thành công | POST request thành công |
| 400 | Bad Request | Dữ liệu không hợp lệ |
| 401 | Unauthorized | Chưa đăng nhập |
| 404 | Not Found | Resource không tồn tại |
| 405 | Method Not Allowed | Sai HTTP method |
| 500 | Server Error | Lỗi server |

### Example Error Response

```json
{
  "success": false,
  "message": "Unauthorized",
  "code": 401
}
```

---

## 6. Tips & Tricks

### Lưu Cookies (Session)

Postman sẽ tự động lưu cookies từ login response.

**Kiểm tra:**
1. Sau khi login, vào tab **"Cookies"**
2. Bạn sẽ thấy `PHPSESSID`
3. Nó sẽ tự động gửi kèm request tiếp theo

### Sử dụng Environment Variables

**Thay vì:**
```
http://localhost/TTHUONG/api/products/list.php
```

**Hãy dùng:**
```
{{base_url}}/products/list.php
```

**Cách thêm variable:**
1. Click Environment (trên)
2. Thêm: `base_url` = `http://localhost/TTHUONG/api`

### Lưu Response vào Variable

**Trong request, vào tab "Tests":**
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("product_id", jsonData.data[0].product_id);
    pm.environment.set("order_id", jsonData.data.order_id);
}
```

### Test Automation

**Tạo test script tự động:**
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has success true", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.success).to.be.true;
});

pm.test("Response has data", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data).to.exist;
});
```

---

## 7. Export & Share Collection

### Export Collection

1. Click collection name → **"..."** (3 dots)
2. Chọn **"Export"**
3. Chọn format **"Collection v2.1"**
4. Lưu file `.json`

### Import Collection

1. Click **"Import"**
2. Chọn file `.json`
3. Postman sẽ tạo collection

---

## 8. Troubleshooting

### Lỗi: "Could not connect"

**Nguyên nhân:** Server không chạy

**Giải pháp:**
1. Mở XAMPP
2. Khởi động Apache và MySQL
3. Kiểm tra `http://localhost` có hoạt động không

### Lỗi: "401 Unauthorized"

**Nguyên nhân:** Chưa đăng nhập

**Giải pháp:**
1. Gọi POST `/api/auth/login.php` trước
2. Kiểm tra cookies trong Postman

### Lỗi: "404 Not Found"

**Nguyên nhân:** URL sai hoặc file API không tồn tại

**Giải pháp:**
1. Kiểm tra base_url: `http://localhost/TTHUONG/api`
2. Kiểm tra file tồn tại trong folder `/api`

---

## 9. Performance Testing

### Load Testing

**Sử dụng Postman Collection Runner:**

1. Click **"Collections"**
2. Chọn collection
3. Click **"Run"** (play button)
4. Đặt số lần chạy (iterations)
5. Click **"Run Collection"**

Postman sẽ:
- Chạy tất cả requests
- Hiển thị stats
- Tính response time

---

## 10. API Documentation

Xem file: `/api/README.md` để có thêm thông tin chi tiết về mỗi endpoint.

---

## 11. Ví Dụ Thực Tế Từng Bước (Đăng Nhập → Thêm Vào Giỏ Hàng → Tạo Đơn Hàng → Tải Lên Chứng Từ)

Ví dụ này cho thấy cài đặt Postman chính xác và các script kiểm tra/trước yêu cầu nhỏ để bạn có thể tự động nối các yêu cầu với nhau.

1) Các biến môi trường (tạo "HKT Local")
- base_url = http://localhost/PTUDHDV/HKT/api
- web_base = http://localhost/PTUDHDV/HKT
- username = xali
- password = password123
- product_id = 001
- PHPSESSID = (để trống)
- order_id = (để trống)

2) Yêu cầu A — Đăng nhập (lưu PHPSESSID + user_id)
- Method: POST
- URL: `{{base_url}}/auth/login.php`
- Headers:
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "username": "{{username}}",
  "password": "{{password}}"
}
```
- Tests (tab "Tests") — sẽ lưu cookie PHPSESSID và user_id:
```javascript
// lưu các trường phản hồi JSON
let res = pm.response.json();
if (res && res.success && res.data) {
    pm.environment.set("user_id", res.data.user_id);
    pm.environment.set("username", res.data.username);
}
// lưu cookie PHPSESSID được thiết lập bởi server vào môi trường
let sid = pm.cookies.get("PHPSESSID");
if (sid) pm.environment.set("PHPSESSID", sid);

// kiểm tra nhanh
pm.test("Đăng nhập thành công", () => pm.expect(res.success).to.eql(true));
```

3) Yêu cầu B — Thêm vào giỏ hàng (sử dụng cookie đã lưu)
- Method: POST
- URL: `{{base_url}}/cart/add.php`
- Headers:
  - Content-Type: application/json
  - Cookie: PHPSESSID={{PHPSESSID}}          <-- thêm header này nếu Postman không tự động gửi cookie
- Body:
```json
{
  "product_id": "{{product_id}}",
  "quantity": 1
}
```
- Tests (tùy chọn):
```javascript
let r = pm.response.json();
pm.test("Thêm vào giỏ hàng thành công", () => pm.expect(r.success).to.eql(true));
```

4) Yêu cầu C — Tạo đơn hàng (sử dụng cookie, đọc giỏ hàng từ server)
- Method: POST
- URL: `{{base_url}}/orders/create.php`
- Headers:
  - Content-Type: application/json
  - Cookie: PHPSESSID={{PHPSESSID}}
- Body:
```json
{
  "full_name": "Test User",
  "email": "test@example.com",
  "phone": "0392656499",
  "address": "Địa chỉ test",
  "payment_method": "cod"
}
```
- Tests — lưu order_id:
```javascript
let res = pm.response.json();
pm.test("Đơn hàng đã được tạo", () => pm.expect(res.success).to.eql(true));
if (res && res.data && res.data.order_id) {
    pm.environment.set("order_id", res.data.order_id);
}
```

5) Yêu cầu D — (Tùy chọn) Tải lên chứng từ thanh toán nếu chuyển khoản ngân hàng
- Lưu ý: điểm cuối này nằm ngoài /api trong dự án này.
- Method: POST
- URL: `{{web_base}}/upload_payment_proof.php`
- Body: form-data
  - order_id : {{order_id}}
  - payment_proof : <chọn tệp>  (loại = Tệp)
- Không đặt thủ công header Content-Type; Postman sẽ thiết lập multipart/form-data.
- Tests:
```javascript
let r = pm.response.json();
pm.test("Kết quả tải lên", () => pm.expect(r.success).to.be.oneOf([true, false]));
```

6) Yêu cầu E — Xác minh chi tiết đơn hàng
- Method: GET
- URL: `{{base_url}}/orders/detail.php?id={{order_id}}`
- Headers:
  - Cookie: PHPSESSID={{PHPSESSID}}
- Tests:
```javascript
let r = pm.response.json();
pm.test("Đơn hàng tồn tại", () => pm.expect(r.success).to.eql(true));
pm.test("Có chứa các mục", () => pm.expect(r.data.items.length).to.be.above(0));
```

7) Chạy dưới dạng luồng nối tiếp (Trình chạy bộ sưu tập)
- Đưa các yêu cầu theo thứ tự này vào một bộ sưu tập: A: Đăng Nhập → B: Thêm vào Giỏ Hàng → C: Tạo Đơn Hàng → E: Chi Tiết Đơn Hàng → D: Tải Lên (nếu cần).
- Chạy bộ sưu tập trong Trình chạy bộ sưu tập:
  - Số lần lặp lại: 1
  - Đảm bảo môi trường "HKT Local" được chọn (để các biến tồn tại)
  - Trình chạy sẽ sử dụng các biến môi trường đã lưu giữa các yêu cầu.

8) Mẹo khắc phục sự cố
- Nếu bạn nhận được 401 trên B/C/E: kiểm tra tab Cookie sau khi đăng nhập; sao chép PHPSESSID vào môi trường thủ công.
- Nếu giỏ hàng trống khi tạo đơn hàng: đảm bảo phản hồi thành công khi thêm vào giỏ hàng và giỏ hàng thuộc về người dùng đã đăng nhập.
- Đối với lỗi tải tệp: xác minh giới hạn tải lên PHP trong php.ini và quyền thư mục uploads.

9) Script trước yêu cầu nhanh (toàn cầu) để tự động chèn header Cookie nếu PHPSESSID env tồn tại
- Thêm vào "Script trước yêu cầu" cấp bộ sưu tập (tùy chọn):
```javascript
let sid = pm.environment.get("PHPSESSID");
if (sid) {
    pm.request.headers.upsert({key: "Cookie", value: "PHPSESSID=" + sid});
}
```

Chỉ vậy thôi — làm theo trình tự, kiểm tra kết quả Kiểm tra và kiểm tra tab Cookies nếu xuất hiện sự cố phiên.
