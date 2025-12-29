# Hướng Dẫn Video - Test APIs Bằng Postman

## Video Tutorials

### 1. Hướng Dẫn Cài Đặt Postman (2 phút)

**Link:** https://youtu.be/G6u4_p1Q7pQ

**Nội dung:**
- Download Postman
- Cài đặt
- Mở ứng dụng
- Tạo tài khoản (tùy chọn)

---

### 2. Tạo Request Đầu Tiên (5 phút)

**Step by step:**

1. **Mở Postman**
   ```
   File → New → Request
   ```

2. **Điền URL**
   ```
   http://localhost/TTHUONG/api/products/list.php
   ```

3. **Chọn Method**
   - Dropdown menu: GET

4. **Thêm Parameters**
   - Tab "Params"
   - Key: `page`, Value: `1`
   - Key: `limit`, Value: `12`

5. **Gửi Request**
   - Click **"Send"** button

6. **Xem Response**
   - JSON sẽ hiển thị bên dưới

---

### 3. POST Request - Đăng Nhập (7 phút)

**Step by step:**

1. **Tạo Request mới**
   - Chọn **POST** method

2. **URL**
   ```
   http://localhost/TTHUONG/api/auth/login.php
   ```

3. **Thêm Headers**
   - Tab "Headers"
   - Key: `Content-Type`, Value: `application/json`

4. **Thêm Body**
   - Tab "Body"
   - Chọn **"raw"**
   - Chọn **"JSON"** (dropdown)
   - Nhập:
   ```json
   {
     "username": "xali",
     "password": "password123"
   }
   ```

5. **Gửi và Kiểm Tra Response**
   - Click "Send"
   - Kiểm tra `"success": true`

6. **Lưu Cookies**
   - Cookies sẽ tự lưu từ response
   - Có thể xem ở tab "Cookies"

---

### 4. Sử Dụng Environment Variables (6 phút)

**Purpose:** Thay vì nhập URL dài, hãy dùng biến

**Step:**

1. **Tạo Environment**
   - Click **"Environments"** (sidebar trái)
   - Click **"+"** hoặc **"Create"**

2. **Đặt tên**
   ```
   Local Development
   ```

3. **Thêm Variables**
   - Variable: `base_url`
   - Value: `http://localhost/TTHUONG/api`
   
   - Variable: `username`
   - Value: `xali`
   
   - Variable: `product_id`
   - Value: `001`

4. **Lưu**
   - Click "Save"

5. **Sử dụng trong Request**
   - URL: `{{base_url}}/products/list.php`
   - Body: `{"username": "{{username}}"}`

---

### 5. Test Scenario - Mua Hàng (15 phút)

**Thứ tự:**

1. **Xem sản phẩm**
   - GET `{{base_url}}/products/list.php`

2. **Chi tiết sản phẩm**
   - GET `{{base_url}}/products/detail.php?id=001`

3. **Đăng nhập**
   - POST `{{base_url}}/auth/login.php`

4. **Thêm vào giỏ**
   - POST `{{base_url}}/cart/add.php`
   - Body: `{"product_id": "001", "quantity": 1}`

5. **Xem giỏ**
   - GET `{{base_url}}/cart/get.php`

6. **Tạo đơn**
   - POST `{{base_url}}/orders/create.php`
   - Lấy order_id từ response

7. **Xem đơn**
   - GET `{{base_url}}/orders/detail.php?id=<order_id>`

---

### 6. Import Postman Collection (4 phút)

**Để import collection có sẵn:**

1. **Download File**
   - File: `HKT_Store_API.postman_collection.json`

2. **Import vào Postman**
   - Click **"Import"** button
   - Chọn file `.json`
   - Click "Import"

3. **Xem Collection**
   - Collection sẽ xuất hiện ở sidebar
   - Tất cả requests đã có sẵn

4. **Cấu hình Environment**
   - Chọn "Local Development" environment
   - Update values nếu cần

---

### 7. Debugging & Troubleshooting (8 phút)

**Common Issues:**

**Issue 1: Connection Refused**
```
Cause: Server không chạy
Fix: Mở XAMPP, start Apache + MySQL
Check: http://localhost phải accessible
```

**Issue 2: 401 Unauthorized**
```
Cause: Chưa login
Fix: Call POST /auth/login.php trước
Check: Cookies tab trong Postman
```

**Issue 3: 404 Not Found**
```
Cause: URL hoặc file sai
Fix: Kiểm tra {{base_url}} variable
Check: File tồn tại trong /api folder
```

**Debug Tips:**

1. **Xem Network Tab**
   - Mở DevTools (F12)
   - Network tab
   - Xem request/response

2. **Console Logs**
   - Xem browser console
   - Xem server logs

3. **Response Body**
   - Xem full response JSON
   - Kiểm tra `success` field

---

### 8. Performance Testing (10 phút)

**Load Testing:**

1. **Mở Collection Runner**
   - Click collection → **"Run"**

2. **Cấu hình**
   - Select collection
   - Iterations: 10 (chạy 10 lần)
   - Delay: 1000ms (1 giây giữa requests)

3. **Run**
   - Click "Run Collection"
   - Xem stats

4. **Analyze**
   - Response times
   - Pass/Fail count
   - Errors

---

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| Ctrl+K | New request |
| Ctrl+E | New environment |
| Ctrl+R | Send request |
| Ctrl+' | Open devtools |
| Cmd+S | Save request |

---

## Best Practices

### 1. Tổ Chức Requests
```
- Group by feature (Products, Auth, Cart, etc)
- Folder cho mỗi module
- Đặt tên rõ ràng
```

### 2. Documentation
```
- Thêm description cho requests
- Ghi rõ method, URL, params
- Ví dụ request/response
```

### 3. Security
```
- Không commit sensitive data
- Sử dụng environment variables
- Che password trong responses
```

### 4. Testing
```
- Tạo test scripts
- Kiểm tra response structure
- Validate data types
```

---

## Resources

- **Postman Docs**: https://learning.postman.com/
- **API Best Practices**: https://restfulapi.net/
- **Testing Guide**: https://www.postman.com/api-testing/
- **Our API Docs**: `/api/README.md`

---

**Happy Testing! 🎉**
