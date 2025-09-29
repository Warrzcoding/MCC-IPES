# Login Monitor with Geolocation Mapping

This feature enhances the login monitoring system with accurate geolocation tracking and interactive mapping capabilities.

## Features

✅ **Accurate Geolocation**: Automatically detects user location based on IP address
✅ **Interactive Maps**: Google Maps integration showing exact login locations
✅ **Enhanced UI**: Centered modal with detailed user information
✅ **Multiple Data Sources**: Uses multiple geolocation APIs for reliability
✅ **Caching**: Caches location data to improve performance
✅ **Privacy Aware**: Handles localhost/private IPs appropriately

## Setup Instructions

### 1. Database Migration
The geolocation columns have been added to the `login_attempts` table:
```bash
php artisan migrate
```

### 2. Google Maps API Key (Optional but Recommended)

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable the "Maps JavaScript API"
4. Create credentials (API Key)
5. Restrict the API key to your domain for security
6. Add to your `.env` file:
```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

### 3. Test Data (Optional)
Generate sample login attempts with geolocation data:
```bash
php artisan db:seed --class=LoginAttemptsSeeder
```

## How It Works

### Backend Components

1. **GeolocationService** (`app/Services/GeolocationService.php`)
   - Fetches location data from multiple APIs (ipapi.co, ipinfo.io)
   - Handles caching for performance
   - Manages private/localhost IPs appropriately

2. **LoginController Updates**
   - All login attempts now include geolocation data
   - Uses the GeolocationService automatically

3. **Database Schema**
   - `latitude` (decimal): GPS latitude coordinate
   - `longitude` (decimal): GPS longitude coordinate  
   - `location` (string): Human-readable location (e.g., "New York, NY, USA")

### Frontend Components

1. **Enhanced Modal**
   - Centered design with larger map area
   - Displays comprehensive user information
   - Interactive Google Maps with markers

2. **Improved UI**
   - Location data in timeline and table views
   - "View Details" buttons for each login attempt
   - Responsive design for all screen sizes

## API Services Used

### Primary: ipapi.co
- Free tier: 1,000 requests/day
- Provides accurate location data
- No API key required

### Fallback: ipinfo.io  
- Free tier: 50,000 requests/month
- Reliable backup service
- No API key required

## Privacy & Security

- **Localhost Detection**: Local IPs (127.0.0.1, ::1) are marked as "Local/Private Network"
- **Private IP Handling**: Private network IPs are handled appropriately
- **Data Caching**: Location data is cached for 24 hours to reduce API calls
- **Error Handling**: Graceful fallbacks when services are unavailable

## Configuration

### Environment Variables
```env
# Google Maps (optional - for interactive mapping)
GOOGLE_MAPS_API_KEY=your_google_maps_api_key

# Cache settings (optional - defaults work well)
CACHE_STORE=database
```

### Config Files
- `config/services.php`: Google Maps API configuration
- `config/cache.php`: Caching configuration

## Usage

### Viewing Login Monitor
1. Navigate to Dashboard → Login Monitor
2. View login attempts in timeline or table format
3. Click "View Details" button on any attempt
4. See detailed information and location mapping

### Search & Filter
- Search by user, email, IP, location, or user agent
- Filter by success/failed status
- Real-time filtering without page reload

## Troubleshooting

### Maps Not Loading
1. Check if `GOOGLE_MAPS_API_KEY` is set in `.env`
2. Verify API key has Maps JavaScript API enabled
3. Check browser console for API errors
4. Ensure domain is whitelisted in Google Cloud Console

### Location Data Missing
1. Check internet connectivity for API calls
2. Verify geolocation services are accessible
3. Check Laravel logs for API errors
4. Test with the seeder data first

### Performance Issues
1. Ensure caching is enabled (`CACHE_STORE=database`)
2. Check cache hit rates in logs
3. Consider upgrading to paid API tiers for higher limits

## Testing

### Manual Testing
1. Perform login attempts from different locations/devices
2. Check login monitor for accurate location data
3. Test the interactive map functionality

### Sample Data
Use the seeder to create test data:
```bash
php artisan db:seed --class=LoginAttemptsSeeder
```

## Support

The system gracefully handles:
- Missing API keys (shows fallback messages)
- Network connectivity issues
- API service outages
- Invalid/private IP addresses
- Browser compatibility issues

All errors are logged for debugging purposes while maintaining user experience.