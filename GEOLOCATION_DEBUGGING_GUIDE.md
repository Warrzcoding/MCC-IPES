# Geolocation Mapping Implementation - Complete Debugging Guide

## Overview
The login monitoring system now captures and displays geolocation data for all login attempts (both success and failed) using a two-tier approach:
1. **Browser Geolocation API** - High accuracy GPS/WiFi data (when user grants permission)
2. **IP-based Geolocation** - Fallback via ipapi.co or ipinfo.io services

## System Components

### 1. Database
- **Table**: `login_attempts`
- **Columns**: 
  - `latitude` (decimal 10,8) - ±0.00000001° ≈ 1.1mm precision
  - `longitude` (decimal 11,8) - ±0.00000001° ≈ 0.8mm precision
  - `location` (string) - Human-readable location (City, Region, Country)

### 2. Services
**GeolocationService** (`app/Services/GeolocationService.php`)
- Queries ipapi.co and ipinfo.io for IP-based coordinates
- Caches results for 24 hours per IP address
- Falls back to "Unknown Location" on failure
- Handles private/localhost IPs appropriately

### 3. Login Flow
**LoginController** (`app/Http/Controllers/Auth/LoginController.php`)
- Calls `createLoginAttempt()` for ALL login attempts (success + failure)
- Captures browser geolocation (latitude/longitude from request)
- Falls back to IP-based geolocation if browser data unavailable
- Merges both sources for accurate location naming

### 4. Frontend Capture
**login.blade.php**
- `loginGeolocationManager` - JavaScript module handling client-side geolocation
- Applies geolocation to hidden form fields before submission
- Supports admin, staff, and student login forms
- Gracefully degrades if user denies permission

### 5. Modal Display
**login-monitor.blade.php**
- Details modal shows login attempt information + map
- Leaflet map with multiple tile layers (OpenStreetMap, Satellite, Terrain, etc.)
- Displays marker at login location OR default world view (20°N, 0°E)
- Responsive design for mobile devices

## Testing Procedures

### A. Client-Side Geolocation Capture

#### Step 1: Open Browser Console
1. Go to login page
2. Press `F12` or `Ctrl+Shift+I` to open developer tools
3. Go to "Console" tab

#### Step 2: Monitor Geolocation Requests
Before submitting login form, watch console for:
```
requestCoordinates called: {forceRequest: true, canUseGeolocation: true}
Requesting device geolocation...
```

#### Step 3: Grant Permission
- Browser will ask "Allow location access?"
- Click "Allow" or "Allow while using this site"

#### Step 4: Verify Form Fields
After permission granted, you should see:
```
Geolocation success: {lat: "10.7769", lng: "106.6966", accuracy: 45.2}
Applied latitude to #admin-login-latitude: 10.7769
Applied longitude to #admin-login-longitude: 106.6966
Coordinate fields updated: 2
```

#### Step 5: Troubleshooting Client-Side Issues

**Issue: "Geolocation requires HTTPS or localhost"**
- Ensure you're accessing via HTTPS or http://localhost
- IP addresses (192.168.x.x) won't work

**Issue: "Already requesting coordinates, skipping duplicate request"**
- This is normal behavior - waiting for previous request

**Issue: No console messages**
- Check if browser supports Geolocation API
- Verify JavaScript is enabled
- Try in Chrome/Firefox (most reliable)

### B. Server-Side Logging

#### Step 1: Check Application Logs
```bash
# Monitor logs in real-time
tail -f storage/logs/laravel.log

# Or view recent entries
cat storage/logs/laravel.log | grep "LoginAttempt"
```

#### Step 2: Expected Log Entries
You should see entries like:
```
[2025-10-26 XX:XX:XX] local.INFO: LoginAttempt: Starting geolocation capture {
  "email": "student@example.com",
  "status": "success",
  "client_lat": "10.7769",
  "client_lng": "106.6966",
  "ip_address": "203.x.x.x"
}

[2025-10-26 XX:XX:XX] local.INFO: LoginAttempt: Final geolocation data {
  "email": "student@example.com",
  "status": "success",
  "final_lat": "10.7769",
  "final_lng": "106.6966",
  "location": "Ho Chi Minh City, Ho Chi Minh, Vietnam"
}

[2025-10-26 XX:XX:XX] local.INFO: LoginAttempt: Successfully recorded {
  "email": "student@example.com",
  "status": "success"
}
```

#### Step 3: Troubleshooting Server-Side Issues

**Issue: Client coordinates empty, using IP API**
```
"client_lat": "",
"client_lng": "",
```
- User likely denied browser geolocation permission
- System will use IP-based geolocation instead (fallback working correctly)

**Issue: IP API failed, null coordinates**
```
"final_lat": null,
"final_lng": null,
"location": "Unknown Location"
```
- Both client geolocation AND IP API failed
- Map will show default world view (expected behavior)
- Check internet connection and API endpoints

### C. Database Verification

#### Step 1: Check Database Records
```sql
SELECT id, email, status, latitude, longitude, location, created_at 
FROM login_attempts 
ORDER BY created_at DESC 
LIMIT 10;
```

#### Step 2: Expected Results
```
| id | email | status | latitude | longitude | location | created_at |
|----|-------|--------|----------|-----------|----------|-----------|
| 15 | user@test.com | success | 10.776900 | 106.696600 | Ho Chi Minh City, Ho Chi Minh, Vietnam | 2025-10-26 11:23:45 |
| 14 | user@test.com | failed | (NULL) | (NULL) | Unknown Location | 2025-10-26 11:23:10 |
| 13 | admin@test.com | success | 10.777000 | 106.697000 | Ho Chi Minh City, Ho Chi Minh, Vietnam | 2025-10-26 11:22:30 |
```

#### Step 3: Analyzing Results

**All coordinates NULL = potential issue**
- Check if geolocation was requested on client
- Verify GeolocationService is callable
- Ensure IP API endpoints are accessible

**Mixed NULL and populated = normal**
- Indicates user sometimes denies permission
- System correctly falls back to IP-based data

**All populated = working perfectly**
- Client geolocation working reliably
- IP-based fallback available as backup

### D. Map Display Testing

#### Step 1: Navigate to Login Monitor
1. Dashboard → Admin Features → Login Monitor

#### Step 2: View Login Attempt Details
1. Find a login attempt in the list
2. Click "View" button
3. Modal should open showing:
   - Email, IP, Location, User Type, User Agent
   - Map with login location marker (if coordinates available)
   - Or world map if no location data

#### Step 3: Check Browser Console
During modal open, watch for:
```
Modal shown event - coordinates: 10.7769, 106.6966
Map container before init: {offsetHeight: 320, offsetWidth: ...}
Initializing map from modal event
Leaflet map object created, adding tiles
Leaflet map initialized successfully at [10.7769, 106.6966]
Calling invalidateSize for Leaflet
```

#### Step 4: Troubleshooting Map Issues

**Issue: Modal opens but map is blank/gray**
- Check console for errors
- Verify Leaflet is loading (should see network request)
- Confirm map container has proper dimensions

**Issue: Map shows error message**
- Check browser console for specific error
- Verify tile layer URLs are accessible
- Try different map provider (OpenStreetMap vs Satellite)

**Issue: Marker not visible**
- Zoom level might be too high/low
- Try zooming with mouse wheel or + - buttons
- If world map showing, user's coordinates were NULL

### E. End-to-End Testing Checklist

#### Test Case 1: Student Login with Geolocation Permission
- [ ] Student enters school ID → identifies student
- [ ] Student enters email/password
- [ ] Browser geolocation prompt appears
- [ ] Click "Allow"
- [ ] Login succeeds/fails
- [ ] Check database: latitude/longitude populated
- [ ] Open Login Monitor → View Details → Map shows location

#### Test Case 2: Student Login Denying Geolocation
- [ ] Repeat above steps
- [ ] Click "Deny" on geolocation prompt
- [ ] Login succeeds/fails
- [ ] Check database: latitude/longitude NULL, location from IP
- [ ] Open Login Monitor → View Details → Map shows default world view

#### Test Case 3: Admin OTP Login with Geolocation
- [ ] Admin enters email/password
- [ ] Browser geolocation prompt appears
- [ ] Grant permission
- [ ] OTP sent → enter OTP code
- [ ] Login succeeds
- [ ] Check database: success record with coordinates
- [ ] Open Login Monitor → View Details → Map shows location

#### Test Case 4: Multiple Failed Attempts
- [ ] Try logging in with wrong password 3 times
- [ ] Check database: 3 failed login attempts recorded
- [ ] Verify each has geolocation data (from browser or IP)
- [ ] Open Login Monitor → View each attempt → Maps work

#### Test Case 5: Localhost Testing
- [ ] If testing on http://localhost:8000
- [ ] Browser geolocation should still work (localhost is allowed)
- [ ] Database should show coordinates
- [ ] Map should display markers

## Performance Monitoring

### 1. Geolocation API Caching
- **Cache Duration**: 24 hours per IP address
- **Cache Key Format**: `geolocation_{ip_address}`
- **Storage**: Laravel Cache (Redis/File)

Check cache:
```php
// In tinker or command
Cache::get('geolocation_203.x.x.x')
```

### 2. API Rate Limits
- **ipapi.co**: Free tier (unlimited)
- **ipinfo.io**: Free tier (50k requests/month)
- **Fallback**: Both services in use, so redundancy built-in

### 3. Performance Metrics
- **Client-side geolocation**: ~2-5 seconds (user-dependent)
- **IP API lookup**: ~0.5-2 seconds (cached after first request)
- **Map rendering**: ~1-2 seconds (Leaflet initialization)
- **Total flow**: ~3-7 seconds

## Common Issues & Solutions

### Issue: Coordinates always NULL
```sql
SELECT * FROM login_attempts WHERE latitude IS NULL AND longitude IS NULL LIMIT 5;
```

**Solution**:
1. Verify GeolocationService is working:
   ```bash
   php artisan tinker
   >>> app(App\Services\GeolocationService::class)->getLocationData('8.8.8.8')
   ```
2. Check IP API endpoints accessibility
3. Review logs for API errors

### Issue: Map not rendering in modal
**Solution**:
1. Open browser console (F12)
2. Look for JavaScript errors
3. Check if Leaflet library loaded (Network tab)
4. Verify map container has dimensions:
   ```js
   document.getElementById('map').offsetHeight // should be > 0
   ```

### Issue: "Location showing but map blank"
**Solution**:
1. Tile layers might not be loading
2. Firewall might block OSM or Esri tile servers
3. Try satellite view instead of street view
4. Check network tab for 403/404 errors

### Issue: Same location for all logins
**Solution**:
- Most likely IP-based geolocation (caching per IP)
- Normal if all users from same office/location
- Browser geolocation would show variety if user grants permission

## Development Notes

### To Disable Logging
Comment out or modify `\Log::info()` calls in `LoginController.php` if logs get too verbose

### To Change Cache Duration
Modify `GeolocationService.php` line 43:
```php
// From: now()->addHours(24)
// To: now()->addHours(48) // or any duration
```

### To Add New Tile Layers
Edit `login-monitor.blade.php` around line 784-805 to add more Leaflet tile providers

### To Switch Map Provider
Modify the order in `login-monitor.blade.php` lines 685-843 (Google → Mapbox → Leaflet priority)

## Support

If geolocation mapping still isn't displaying:
1. Check all console logs above
2. Verify database records are being created with coordinates
3. Check browser geolocation permission settings
4. Ensure HTTPS or localhost access
5. Verify IP API endpoints are accessible
6. Review Laravel application logs
