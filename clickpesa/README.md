# ClickPesa Payment System

A comprehensive PHP-based payment system for integrating with ClickPesa's USSD Push payment gateway. This system provides a complete dashboard for managing mobile money payments in Tanzania.

## Features

- **Payment Initiation**: Send USSD Push requests to customers for payment collection
- **Payment Status Tracking**: Real-time status monitoring of transactions
- **Payment History**: Complete transaction history with filtering and pagination
- **Dashboard**: Overview with statistics and recent transactions
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5
- **API Integration**: Full integration with ClickPesa's third-party APIs
- **Security**: Secure token-based authentication

## Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Web server (Apache/Nginx)
- Valid ClickPesa API credentials (Client ID and API Key)

## Installation

1. **Clone or download** the files to your web server directory
2. **Configure API credentials** in `config.php`:
   ```php
   'client_id' => 'your-actual-client-id',
   'api_key' => 'your-actual-api-key',
   ```
3. **Set proper file permissions** for web server access
4. **Configure web server** to point to the project directory

## Configuration

### API Credentials

Edit `config.php` and update the following settings:

```php
'clickpesa' => [
    'api_base_url' => 'https://api.clickpesa.com/third-parties',
    'client_id' => '<your-client-id>',        // Your ClickPesa Client ID
    'api_key' => '<your-api-key>',           // Your ClickPesa API Key
    'timeout' => 30,                          // Request timeout in seconds
    'currency' => 'TZS',                      // Default currency
],
```

### Payment Settings

```php
'payment' => [
    'default_amount' => 1000,                 // Default payment amount
    'min_amount' => 100,                      // Minimum allowed amount
    'max_amount' => 1000000,                  // Maximum allowed amount
],
```

### Callback Configuration

```php
'callback' => [
    'url' => 'http://yourdomain.com/callback.php',
    'secret_key' => '<your-callback-secret>', // Optional webhook security
],
```

## File Structure

```
clickpesa/
├── config.php                 # Configuration file
├── ClickPesaAPI.php          # API client class
├── index.php                 # Main dashboard
├── initiate_payment.php      # Payment initiation page
├── payment_status.php        # Payment status checking
├── payment_history.php       # Payment history with filters
├── callback.php              # Webhook handler (existing)
├── test_payment.php          # API testing (existing)
└── README.md                 # This documentation
```

## API Endpoints Used

### Authentication
- **Generate Token**: `POST /generate-token`
  - Generates JWT token valid for 1 hour

### Payment Operations
- **Preview USSD Push**: `POST /payments/preview-ussd-push-request`
  - Validates payment details and checks available methods
- **Initiate USSD Push**: `POST /payments/initiate-ussd-push-request`
  - Sends USSD Push to customer's phone
- **Query Payment Status**: `GET /payments/{orderReference}`
  - Retrieves payment status by order reference
- **Query All Payments**: `GET /payments/all`
  - Lists all payments with filtering and pagination

## Usage Guide

### 1. Dashboard (`index.php`)
- View payment statistics
- Monitor recent transactions
- Quick access to all features
- Auto-refreshes every 30 seconds

### 2. Initiate Payment (`initiate_payment.php`)
- Enter payment amount and customer phone number
- System validates phone number format (255xxxxxxxx)
- Preview available payment methods
- Send USSD Push request
- Get immediate transaction details

### 3. Check Payment Status (`payment_status.php`)
- Enter order reference to check status
- Real-time status updates
- Auto-refresh for processing payments
- Detailed transaction information

### 4. Payment History (`payment_history.php`)
- View all transactions
- Filter by status, date, currency, channel
- Pagination for large datasets
- Export capabilities
- Detailed transaction modal

## Phone Number Format

All phone numbers must be in Tanzania format:
- **Format**: `255712345678`
- **Prefix**: Must start with `255`
- **Digits**: 12 digits total
- **Providers**: Supports Tigo, Airtel, Halotel, Vodacom

## Payment Status Codes

| Status | Description |
|--------|-------------|
| `SUCCESS` | Payment completed successfully |
| `SETTLED` | Payment has been settled |
| `PROCESSING` | Payment is being processed |
| `PENDING` | Payment is pending customer action |
| `FAILED` | Payment failed |

## Security Features

- **JWT Token Authentication**: Secure API communication
- **Input Validation**: Server-side validation of all inputs
- **CSRF Protection**: Built-in security measures
- **HTTPS Required**: All API calls over secure connection
- **Rate Limiting**: Respects API rate limits

## Error Handling

The system includes comprehensive error handling:
- API connection errors
- Invalid phone numbers
- Amount validation
- Token expiration handling
- Network timeout management

## Customization

### Styling
- Uses Bootstrap 5 for responsive design
- Custom CSS in each file for branding
- Easy to modify colors and layout

### API Client
The `ClickPesaAPI` class can be extended:
```php
$api = new ClickPesaAPI($config);
$token = $api->generateToken();
$payment = $api->initiateUSSDPush($amount, $reference, $phone);
```

## Testing

Use the existing `test_payment.php` to test API connectivity:
1. Update credentials in `config.php`
2. Access `test_payment.php` in browser
3. Follow the test instructions

## Troubleshooting

### Common Issues

1. **Token Generation Fails**
   - Check Client ID and API Key
   - Verify API credentials are correct
   - Ensure server can reach ClickPesa API

2. **Payment Initiation Fails**
   - Validate phone number format
   - Check amount limits
   - Verify customer has mobile money account

3. **Status Check Fails**
   - Ensure order reference is correct
   - Check if payment exists in system
   - Verify API token is valid

### Debug Mode

Enable debug in `config.php`:
```php
'app' => [
    'debug' => true,  // Shows detailed error messages
],
```

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Mobile Support

The system is fully responsive and works on:
- iOS Safari
- Chrome Mobile
- Samsung Internet
- Other modern mobile browsers

## API Rate Limits

ClickPesa imposes rate limits:
- Token generation: 100 requests/hour
- Payment operations: 1000 requests/hour
- Status queries: 1000 requests/hour

## Support

For ClickPesa API support:
- Documentation: https://docs.clickpesa.com
- Support: support@clickpesa.com
- Status: https://status.clickpesa.com

## License

This project is provided as-is for integration with ClickPesa services. Please ensure compliance with ClickPesa's terms of service and applicable regulations.

## Version History

- **v1.0.0**: Initial release with full payment system functionality
- Complete dashboard and payment management
- USSD Push integration
- Real-time status tracking
- Comprehensive filtering and search

---

**Note**: Ensure you have valid ClickPesa API credentials before using this system. Contact ClickPesa for API access if needed.
