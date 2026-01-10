# Monsieur WiFi API Documentation

## Registration Flow with Captive Portal Design

This document describes the API endpoints for creating a temporary captive portal design and registering a new user account.

### Overview

The registration system supports two flows:

1. **Design-First Flow**: Users first configure their captive portal design, then register their account. The design is stored temporarily and automatically transferred to their account upon successful registration.

2. **Direct Registration Flow**: Users register directly without pre-configuring a design. They can create designs later from their dashboard.

### Design-First Registration Flow

When using the design-first flow, the process works as follows:

1. **Design Creation**: User visits `/register-with-captive-portal` and configures their captive portal design (colors, messages, logos, etc.)
2. **API Call**: The design form submits to `POST /api/temp-captive-portal-designs` which creates a temporary design record
3. **Response**: The API returns a `design_id` in the response
4. **Redirection**: The application automatically redirects to `/register?design_id={design_id}` with the design ID as a URL parameter
5. **Registration**: User fills out the registration form (name, email, password)
6. **Account Creation**: The registration form submits to `POST /api/auth/register` with the `design_id` included in the request. The user role is automatically set to `"user"` on the backend.
7. **Design Transfer**: Upon successful user creation, the system automatically:
   - Retrieves the temporary design using the `design_id`
   - Creates a permanent `CaptivePortalDesign` record linked to the new user
   - Sets both `user_id` and `owner_id` to the new user's ID
   - Deletes the temporary design record
8. **Completion**: User receives authentication token and is redirected to the dashboard

### Key Features

- **No Authentication Required**: Both endpoints are public, allowing users to create designs and register without being logged in
- **Automatic Design Transfer**: Temporary designs are seamlessly transferred to permanent designs upon registration
- **Error Handling**: If design transfer fails, registration still succeeds (error is logged but not returned to client)
- **Cleanup**: Temporary designs are automatically deleted after successful transfer
- **Flexibility**: Users can register with or without a pre-configured design

### URL Parameters

When redirecting to the registration page after design creation, the `design_id` is passed as a URL query parameter:

```
/register?design_id=123
```

The registration page automatically captures this parameter and includes it in the registration API request. If no `design_id` is provided, registration proceeds normally without design transfer.

---

## Table of Contents

1. [Create Temporary Captive Portal Design](#1-create-temporary-captive-portal-design)
2. [Register User Account](#2-register-user-account)
3. [Complete Flow Examples](#3-complete-flow-examples)

---

## 1. Create Temporary Captive Portal Design

Create a temporary captive portal design before user registration. This design will be automatically transferred to the user's account upon successful registration.

### Endpoint

```
POST /api/temp-captive-portal-designs
```

### Authentication

**No authentication required** - This is a public endpoint.

### Request Headers

```
Content-Type: multipart/form-data
X-CSRF-TOKEN: {csrf_token}  (Required for web requests)
```

### Request Body (Form Data)

| Field | Type | Required | Description | Default |
|-------|------|----------|-------------|---------|
| `name` | string | Yes | Design name (max 255 characters) | - |
| `description` | string | No | Design description | - |
| `theme_color` | string | Yes | Theme color in hex format (e.g., #7367f0) | - |
| `welcome_message` | string | Yes | Welcome message displayed on login page (max 255 characters) | - |
| `login_instructions` | string | No | Instructions for users on how to connect | - |
| `button_text` | string | Yes | Text displayed on the connect button (max 100 characters) | - |
| `show_terms` | boolean | No | Whether to show terms and conditions checkbox | false |
| `terms_content` | string | No | Terms and conditions content | - |
| `privacy_content` | string | No | Privacy policy content | - |
| `location_logo` | file | No | Logo image file (max 2MB, image formats) | - |
| `background_image` | file | No | Background image file (max 5MB, image formats) | - |
| `background_color_gradient_start` | string | No | Gradient start color in hex format | - |
| `background_color_gradient_end` | string | No | Gradient end color in hex format | - |
| `additional_settings` | JSON | No | Additional settings as JSON object | - |
| `is_default` | boolean | No | Whether this should be the default design | false |

### Example Request (cURL)

```bash
curl -X POST https://portal.monsieur-wifi.com/api/temp-captive-portal-designs \
  -H "Content-Type: multipart/form-data" \
  -F "name=My WiFi Portal" \
  -F "description=Custom WiFi portal design" \
  -F "theme_color=#7367f0" \
  -F "welcome_message=Welcome to our WiFi" \
  -F "login_instructions=Please connect to access the internet" \
  -F "button_text=Connect to WiFi" \
  -F "show_terms=1" \
  -F "terms_content=By connecting, you agree to our terms..." \
  -F "privacy_content=We respect your privacy..." \
  -F "location_logo=@/path/to/logo.png" \
  -F "background_image=@/path/to/background.jpg" \
  -F "background_color_gradient_start=#7367f0" \
  -F "background_color_gradient_end=#17c1e8" \
  -F "is_default=0"
```

### Example Request (JavaScript/Fetch)

```javascript
const formData = new FormData();
formData.append('name', 'My WiFi Portal');
formData.append('description', 'Custom WiFi portal design');
formData.append('theme_color', '#7367f0');
formData.append('welcome_message', 'Welcome to our WiFi');
formData.append('login_instructions', 'Please connect to access the internet');
formData.append('button_text', 'Connect to WiFi');
formData.append('show_terms', '1');
formData.append('terms_content', 'By connecting, you agree to our terms...');
formData.append('privacy_content', 'We respect your privacy...');

// Add files if available
const logoFile = document.getElementById('location_logo').files[0];
if (logoFile) {
    formData.append('location_logo', logoFile);
}

const bgFile = document.getElementById('background_image').files[0];
if (bgFile) {
    formData.append('background_image', bgFile);
}

formData.append('background_color_gradient_start', '#7367f0');
formData.append('background_color_gradient_end', '#17c1e8');
formData.append('is_default', '0');

fetch('/api/temp-captive-portal-designs', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    console.log('Design created:', data);
    // Redirect to registration with design_id
    window.location.href = `/register?design_id=${data.data.design_id}`;
})
.catch(error => {
    console.error('Error:', error);
});
```

### Success Response (201 Created)

```json
{
    "success": true,
    "message": "Temporary captive portal design created successfully",
    "data": {
        "id": 123,
        "design_id": 123
    }
}
```

**Important**: After receiving a successful response, you should redirect the user to the registration page with the `design_id` as a URL parameter:

```javascript
// After successful design creation
if (response.success && response.data.design_id) {
    // Redirect to registration page with design_id
    window.location.href = `/register?design_id=${response.data.design_id}`;
}
```

The registration page will automatically capture the `design_id` from the URL and include it in the registration request. This ensures the temporary design is transferred to the user's account upon successful registration.

### Error Response (422 Validation Error)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "theme_color": [
            "The theme color field is required."
        ],
        "welcome_message": [
            "The welcome message field is required."
        ]
    }
}
```

### Error Response (500 Server Error)

```json
{
    "message": "Server Error"
}
```

### Notes

- **Redirection Flow**: After successful design creation, redirect the user to `/register?design_id={design_id}`. The registration page will automatically capture the `design_id` from the URL parameter and include it in the registration request.

- **Design Transfer**: The temporary design will be automatically transferred to the user's account upon successful registration and then deleted from the temporary table.

- **File Storage**: File uploads are stored in `storage/app/public/captive-portals/logos/` and `storage/app/public/captive-portals/backgrounds/`. These files are preserved when the design is transferred to the permanent table.

- **Design ID Usage**: The `design_id` returned in the response should be passed to the registration endpoint (`POST /api/auth/register`) as a parameter. If omitted, registration will proceed normally without design transfer.

- **Temporary Design Lifecycle**: Temporary designs are only deleted after successful transfer to a user account. If a user abandons the registration process, the temporary design will remain in the database (consider implementing cleanup for abandoned designs).

---

## 2. Register User Account

Register a new user account. Optionally include a `design_id` to automatically transfer a temporary captive portal design to the new user.

### Endpoint

```
POST /api/auth/register
```

### Authentication

**No authentication required** - This is a public endpoint.

### Request Headers

```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}  (Required for web requests)
```

### Request Body (JSON)

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | User's full name (max 255 characters) |
| `email` | string | Yes | User's email address (must be unique, max 255 characters) |
| `password` | string | Yes | User's password (minimum 8 characters) |
| `password_confirmation` | string | Yes | Password confirmation (must match password) |
| `design_id` | integer | No | ID of temporary design to transfer (must exist in `temp_captive_portal_designs` table) |

**Note**: The `role` field is **not** required and is automatically set to `"user"` on the backend. All users created through this endpoint will have the role `"user"`. Admin users must be created through other means (e.g., by existing admins via the admin panel).

### Example Request (cURL)

```bash
curl -X POST https://portal.monsieur-wifi.com/api/auth/register \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "SecurePassword123!",
    "password_confirmation": "SecurePassword123!",
    "design_id": 123
  }'
```

**Note**: The `role` field is not included in the request. All users created through this endpoint are automatically assigned the role `"user"`.

### Example Request (JavaScript/Fetch)

```javascript
const registerData = {
    name: 'John Doe',
    email: 'john.doe@example.com',
    password: 'SecurePassword123!',
    password_confirmation: 'SecurePassword123!',
    design_id: 123  // Optional: from previous design creation
    // Note: role is automatically set to "user" on the backend
};

fetch('/api/auth/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(registerData)
})
.then(response => response.json())
.then(data => {
    if (data.access_token) {
        // Store token and user data
        localStorage.setItem('token', data.access_token);
        localStorage.setItem('user', JSON.stringify(data.user));
        
        // Redirect to dashboard
        window.location.href = '/dashboard';
    }
})
.catch(error => {
    console.error('Registration error:', error);
});
```

### Success Response (200 OK)

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "role": "user",
        "profile_picture": null,
        "email_verified_at": null,
        "created_at": "2024-01-10T12:00:00.000000Z",
        "updated_at": "2024-01-10T12:00:00.000000Z"
    }
}
```

### Error Response (422 Validation Error)

```json
{
    "email": [
        "The email has already been taken."
    ],
    "password": [
        "The password confirmation does not match."
    ],
    "design_id": [
        "The selected design id is invalid."
    ]
}
```

### Error Response (500 Server Error)

```json
{
    "message": "Server Error"
}
```

### Notes

- **Role Assignment**: All users created through this endpoint are automatically assigned the role `"user"`. The role cannot be specified in the registration request and is hardcoded on the backend for security reasons.

- **Design Transfer**: If `design_id` is provided and valid, the temporary design will be automatically transferred to the new user account

- **Cleanup**: The temporary design will be deleted after successful transfer

- **Error Handling**: If design transfer fails, registration will still succeed (error is logged but not returned to client)

- **Authentication**: The `access_token` should be included in subsequent authenticated requests as: `Authorization: Bearer {access_token}`

---

## 3. Complete Flow Examples

### Flow 1: Registration with Design Selection

**Step 1: Create Temporary Design**

```javascript
// User fills design form and submits
const designFormData = new FormData();
designFormData.append('name', 'My WiFi Portal');
designFormData.append('theme_color', '#7367f0');
designFormData.append('welcome_message', 'Welcome to our WiFi');
designFormData.append('button_text', 'Connect to WiFi');
// ... other fields

fetch('/api/temp-captive-portal-designs', {
    method: 'POST',
    body: designFormData
})
.then(response => response.json())
.then(data => {
    // Redirect to registration page with design_id
    window.location.href = `/register?design_id=${data.data.design_id}`;
});
```

**Step 2: Register User with Design**

```javascript
// On registration page, design_id is captured from URL
const urlParams = new URLSearchParams(window.location.search);
const designId = urlParams.get('design_id');

// User fills registration form and submits
const registerData = {
    name: 'John Doe',
    email: 'john@example.com',
    password: 'SecurePassword123!',
    password_confirmation: 'SecurePassword123!',
    design_id: designId  // Include design_id from URL
    // Note: role is automatically set to "user" on the backend
};

fetch('/api/auth/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(registerData)
})
.then(response => response.json())
.then(data => {
    // User is registered and design is transferred
    // Redirect to dashboard
    window.location.href = '/dashboard';
});
```

### Flow 2: Direct Registration (No Design)

```javascript
// User registers directly without design selection
const registerData = {
    name: 'Jane Smith',
    email: 'jane@example.com',
    password: 'SecurePassword123!',
    password_confirmation: 'SecurePassword123!'
    // No design_id provided
    // Note: role is automatically set to "user" on the backend
};

fetch('/api/auth/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(registerData)
})
.then(response => response.json())
.then(data => {
    // User is registered without design
    // User can create designs later from dashboard
    window.location.href = '/dashboard';
});
```

---

## Error Handling

### Common Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 201 | Created (Design created successfully) |
| 422 | Validation Error (Check error messages) |
| 500 | Server Error |

### Error Response Format

All error responses follow this format:

```json
{
    "message": "Error message",
    "errors": {
        "field_name": [
            "Error message for field"
        ]
    }
}
```

---

## Rate Limiting

Currently, there are no rate limits on these endpoints. However, it's recommended to implement reasonable limits in production.

---

## Security Considerations

1. **CSRF Protection**: Web requests require CSRF token in header
2. **Password Requirements**: Minimum 8 characters (enforced by validation)
3. **File Upload Limits**: 
   - Logo: Max 2MB
   - Background Image: Max 5MB
4. **Email Uniqueness**: Email addresses must be unique across all users
5. **Token Expiration**: JWT tokens expire after the configured TTL (default: 60 minutes)

---

## Support

For API support or questions, please contact the development team or refer to the main project documentation.
