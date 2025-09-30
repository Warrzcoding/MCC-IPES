{{-- Login Monitoring Tracker UI (Frontend-only enhancement) --}}
<style>
  .enhanced-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
  .tab-separator { border: none; height: 2px; background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%); margin: 0.5rem 2rem 0.75rem 2rem; border-radius: 2px; }
  .back-btn { background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; border: none !important; border-radius: 10px !important; padding: 0.75rem 1.5rem !important; font-weight: 600 !important; transition: all 0.3s ease !important; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3) !important; color: white !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; min-width: 120px !important; }
  .back-btn:hover { transform: translateY(-2px) !important; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4) !important; color: white !important; text-decoration: none !important; }
  .search-container { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 1.25rem; }

  /* Summary cards */
  .stat-card { background: #fff; border-radius: 12px; padding: 1rem; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
  .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; }
  .bg-gradient-primary { background: linear-gradient(135deg, #4f46e5, #6366f1); }
  .bg-gradient-success { background: linear-gradient(135deg, #10b981, #34d399); }
  .bg-gradient-danger { background: linear-gradient(135deg, #ef4444, #f87171); }

  /* Tabs */
  .tab-nav .btn { border-radius: 10px; font-weight: 600; }
  .tab-nav .btn.active { background: #0d6efd; color: #fff; box-shadow: 0 6px 20px rgba(13,110,253,0.35); }

  /* Timeline */
  .timeline { position: relative; margin: 0; padding: 0 0 0 28px; list-style: none; }
  .timeline::before { content: ""; position: absolute; top: 0; left: 10px; width: 2px; height: 100%; background: #e9ecef; }
  .timeline-item { position: relative; margin-bottom: 18px; background: #fff; border-radius: 12px; padding: 0.85rem 1rem 0.85rem 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
  .timeline-item .marker { position: absolute; left: -6px; top: 14px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .marker-success { background: #16a34a; }
  .marker-failed { background: #dc2626; }
  .attempt-meta { color: #6b7280; font-size: 0.9rem; }
  .ua-muted { color: #9ca3af; font-size: 0.85rem; }
  .status-badge { font-size: 0.75rem; padding: 0.35rem 0.55rem; border-radius: 999px; font-weight: 700; letter-spacing: .2px; }
  .status-success { background: rgba(34,197,94,0.12); color: #15803d; border: 1px solid rgba(34,197,94,0.25); }
  .status-failed { background: rgba(239,68,68,0.12); color: #b91c1c; border: 1px solid rgba(239,68,68,0.25); }

  /* Utilities */
  .truncate-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
  .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

@php
  // Build base collection from paginator or array
  $collection = isset($loginAttempts) ? (method_exists($loginAttempts, 'getCollection') ? $loginAttempts->getCollection() : collect($loginAttempts)) : collect();

  // Deduplicate attempts by a stable fingerprint to avoid double-success display
  $collection = $collection->unique(function ($item) {
      return strtolower(
          ($item->status ?? '-') . '|' .
          ($item->email ?? '-') . '|' .
          ($item->ip_address ?? '-') . '|' .
          ($item->user_agent ?? '-') . '|' .
          optional($item->created_at)->format('Y-m-d H:i:s')
      );
  })->values();

  // Page-level counts (based on deduped items)
  $pageSuccess = $collection->where('status', 'success')->count();
  $pageFailed  = $collection->where('status', 'failed')->count();

  // Keep the global total from paginator if available
  $totalCount  = isset($loginAttempts) && method_exists($loginAttempts, 'total') ? $loginAttempts->total() : $collection->count();
@endphp

<div class="row page-full-width">
  <div class="col-12">
    <div class="enhanced-card">
      <div class="card-body p-0">
        <div class="p-4 pb-0">
          <h5 class="mb-2">
            <i class="fas fa-user-shield me-2"></i> Login Monitoring
          </h5>
          <p class="text-muted mb-3">Visual tracker of recent login activity with filters and detailed view.</p>
        </div>

        <hr class="tab-separator">

        <div class="p-4 pt-0">
          {{-- Summary Stats --}}
          <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Total Attempts</div>
                  <div class="h5 mb-0">{{ number_format($totalCount) }}</div>
                </div>
                <div class="stat-icon bg-gradient-primary"><i class="fas fa-list"></i></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Page Success</div>
                  <div class="h5 mb-0 text-success">{{ number_format($pageSuccess) }}</div>
                </div>
                <div class="stat-icon bg-gradient-success"><i class="fas fa-check"></i></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Page Failed</div>
                  <div class="h5 mb-0 text-danger">{{ number_format($pageFailed) }}</div>
                </div>
                <div class="stat-icon bg-gradient-danger"><i class="fas fa-times"></i></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Showing</div>
                  <div class="h5 mb-0">{{ $collection->count() }} / {{ number_format($totalCount) }}</div>
                </div>
                <div class="stat-icon bg-gradient-primary"><i class="fas fa-eye"></i></div>
              </div>
            </div>
          </div>

          {{-- Filters --}}
          <div class="search-container">
            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary mb-1"><i class="fas fa-search me-2"></i>Search</label>
                <input id="lm-search" type="text" class="form-control" placeholder="Search by user, email, IP, location or agent...">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold text-primary mb-1">Status</label>
                <select id="lm-status" class="form-select">
                  <option value="">All</option>
                  <option value="success">Success</option>
                  <option value="failed">Failed</option>
                </select>
              </div>
              <div class="col-md-3 text-end">
                <a href="{{ route('dashboard', ['page' => 'login-monitor']) }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Refresh</a>
              </div>
            </div>
          </div>

          {{-- Tabs --}}
          <div class="d-flex gap-2 tab-nav mb-3">
            <button type="button" class="btn btn-light active" data-tab="timeline"><i class="fas fa-stream me-1"></i> Timeline</button>
            <button type="button" class="btn btn-light" data-tab="table"><i class="fas fa-table me-1"></i> Table</button>
          </div>

          {{-- Timeline View --}}
          <div id="tab-timeline">
            <ul class="timeline" id="timeline-list">
              @forelse($collection as $attempt)
                @php
                  $isSuccess = ($attempt->status === 'success');
                  $name = $attempt->user->full_name ?? null;
                  $email = $attempt->email ?? '-';
                  $displayName = $name ?: $email;
                  $ua = $attempt->user_agent ?? '-';
                  $ip = $attempt->ip_address ?? '-';
                  $when = $attempt->created_at?->diffForHumans() ?? '-';
                  $rawText = strtolower(trim(($name ? ($name.' ') : '') . ($email.' ') . ($ip.' ') . ($ua)));
                @endphp
                <li class="timeline-item lm-item" data-status="{{ $attempt->status }}" data-text="{{ $rawText }}">
                  <span class="marker {{ $isSuccess ? 'marker-success' : 'marker-failed' }}"></span>
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="me-2">
                      <div class="fw-semibold truncate-1">
                        <i class="fas {{ $isSuccess ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-1"></i>
                        {{ $displayName }}
                        <span class="status-badge {{ $isSuccess ? 'status-success' : 'status-failed' }} ms-2 text-uppercase">{{ $attempt->status }}</span>
                      </div>
                      <div class="attempt-meta mt-1">
                        <i class="fas fa-envelope me-1"></i> {{ $email }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-network-wired me-1"></i> {{ $ip }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-map-marker-alt me-1"></i> {{ $attempt->location ?? 'Unknown' }}
                        <span class="mx-2">|</span>
                        <i class="far fa-clock me-1"></i> {{ $when }}
                      </div>
                      <div class="ua-muted truncate-2 mt-1"><i class="fas fa-desktop me-1"></i>{{ \Illuminate\Support\Str::limit($ua, 160) }}</div>
                    </div>
                    <div class="align-self-start">
                      <button class="btn btn-sm btn-outline-primary view-details" data-id="{{ $attempt->id ?? $loop->index }}" data-email="{{ $email }}" data-ip="{{ $ip }}" data-location="{{ $attempt->location ?? 'Unknown' }}" data-latitude="{{ $attempt->latitude ?? '' }}" data-longitude="{{ $attempt->longitude ?? '' }}" data-usertype="{{ $attempt->user->usertype ?? 'N/A' }}" data-ua="{{ $ua }}">
                        <i class="fas fa-eye"></i> View
                      </button>
                    </div>
                  </div>
                </li>
              @empty
                <li class="timeline-item">
                  <span class="marker marker-success"></span>
                  <div class="text-muted">No login attempts recorded yet.</div>
                </li>
              @endforelse
            </ul>

            @if(isset($loginAttempts) && method_exists($loginAttempts, 'hasPages') && $loginAttempts->hasPages())
              <nav class="mt-3" aria-label="Timeline pagination">
                <ul class="pagination justify-content-end mb-0">
                  <li class="page-item {{ $loginAttempts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $loginAttempts->previousPageUrl() }}" rel="prev">
                      <i class="fas fa-chevron-left me-1"></i> Previous
                    </a>
                  </li>
                  <li class="page-item {{ $loginAttempts->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $loginAttempts->nextPageUrl() }}" rel="next">
                      Next <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            @endif
          </div>

          {{-- Table View --}}
          <div id="tab-table" class="d-none">
            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="attempts-table">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Location</th>
                    <th>User Agent</th>
                    <th>Result</th>
                    <th>When</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(($loginAttempts ?? []) as $attempt)
                    @php
                      $name = $attempt->user->full_name ?? '-';
                      $ua = $attempt->user_agent ?? '-';
                    @endphp
                    <tr class="lm-item" data-status="{{ $attempt->status }}" data-text="{{ strtolower(trim(($name.' ') . (($attempt->email ?? '-') . ' ') . (($attempt->ip_address ?? '-') . ' ') . ($ua) . ' ' . ($attempt->location ?? ''))) }}">
                      <td>{{ ($loginAttempts->firstItem() ?? 1) + $loop->index }}</td>
                      <td class="truncate-1" title="{{ $name }}">{{ $name }}</td>
                      <td>{{ $attempt->email ?? '-' }}</td>
                      <td>{{ $attempt->ip_address ?? '-' }}</td>
                      <td>{{ $attempt->location ?? '-' }}</td>
                      <td class="truncate-1" title="{{ $ua }}">{{ \Illuminate\Support\Str::limit($ua, 80) }}</td>
                      <td>
                        @if($attempt->status === 'success')
                          <span class="badge bg-success">Success</span>
                        @else
                          <span class="badge bg-danger">Failed</span>
                        @endif
                      </td>
                      <td>{{ $attempt->created_at?->diffForHumans() }}</td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary view-details" data-id="{{ $attempt->id ?? $loop->index }}" data-email="{{ $attempt->email ?? '-' }}" data-ip="{{ $attempt->ip_address ?? '-' }}" data-location="{{ $attempt->location ?? 'Unknown' }}" data-latitude="{{ $attempt->latitude ?? '' }}" data-longitude="{{ $attempt->longitude ?? '' }}" data-usertype="{{ $attempt->user->usertype ?? 'N/A' }}" data-ua="{{ $ua }}">
                          <i class="fas fa-eye"></i> View
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted">No login attempts recorded yet.</td>
                    </tr>
                  @endforelse
                </tbody>
                @if(isset($loginAttempts) && method_exists($loginAttempts, 'hasPages') && $loginAttempts->hasPages())
                <tfoot>
                  <tr>
                    <td colspan="9">
                      <nav aria-label="Table pagination">
                        <ul class="pagination justify-content-end mb-0">
                          <li class="page-item {{ $loginAttempts->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $loginAttempts->previousPageUrl() }}" rel="prev">
                              <i class="fas fa-chevron-left me-1"></i> Previous
                            </a>
                          </li>
                          <li class="page-item {{ $loginAttempts->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $loginAttempts->nextPageUrl() }}" rel="next">
                              Next <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                          </li>
                        </ul>
                      </nav>
                    </td>
                  </tr>
                </tfoot>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="text-start mt-2 pb-3 px-4">
        <a href="{{ route('dashboard', ['page' => 'add-students']) }}" class="btn back-btn">
          <i class="fas fa-arrow-left me-2"></i>
          Back to Students Management
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Login Attempt Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-5">
            <h6>Attempt Information</h6>
            <table class="table table-sm">
              <tr>
                <th>Email:</th>
                <td id="modal-email">-</td>
              </tr>
              <tr>
                <th>IP Address:</th>
                <td id="modal-ip">-</td>
              </tr>
              <tr>
                <th>Location:</th>
                <td id="modal-location">-</td>
              </tr>
              <tr>
                <th>User Type:</th>
                <td id="modal-usertype">-</td>
              </tr>
              <tr>
                <th>User Agent:</th>
                <td id="modal-ua">-</td>
              </tr>
            </table>
          </div>
          <div class="col-md-7">
            <h6>Geolocation Mapping</h6>
            <div id="map" style="height: 400px; width: 100%; border-radius: 8px; overflow: hidden; position: relative; background: #f8f9fa;"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Map Styling -->
<style>
  /* Ensure map container has proper dimensions */
  #map {
    min-height: 400px !important;
    position: relative !important;
  }
  
  /* Fix for Leaflet in modals */
  .modal.show #map .leaflet-container {
    height: 600px !important;
  }

  /* Fix for Mapbox in modals */
  .modal.show #map .mapboxgl-map {
    height: 600px !important;
  }
  
  /* Loading state */
  #map:empty::before {
    content: "Loading map...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6c757d;
    font-size: 14px;
  }
</style>

<!-- Enhanced Mapping with Multiple Providers -->
@if(config('services.google_maps.api_key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}"></script>
@elseif(config('services.mapbox.access_token'))
<!-- Mapbox GL JS (High-quality satellite imagery) -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
@else
<!-- Multiple Free Mapping Providers with Satellite Support -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Esri Leaflet for ArcGIS satellite imagery -->
<script src="https://unpkg.com/esri-leaflet@3.0.10/dist/esri-leaflet.js"></script>
@endif
<script>
  (function(){
    const $ = (s,ctx=document)=>ctx.querySelector(s);
    const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));

    let map;
    let marker;

    function initMap(lat, lng) {
      console.log('Initializing map with coordinates:', lat, lng);
      
      if (!lat || !lng || lat === 'null' || lng === 'null') {
        $('#map').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location data not available</p></div>';
        return;
      }
      
      const latitude = parseFloat(lat);
      const longitude = parseFloat(lng);
      
      if (isNaN(latitude) || isNaN(longitude)) {
        $('#map').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Invalid coordinates</p></div>';
        return;
      }
      
      // Clear any existing map content
      $('#map').innerHTML = '';
      
      console.log('Using coordinates:', latitude, longitude);
      
      // Check if Google Maps is available
      if (typeof google !== 'undefined' && google.maps) {
        // Use Google Maps
        const location = { lat: latitude, lng: longitude };
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: location,
          mapTypeControl: true,
          streetViewControl: true,
          fullscreenControl: true
        });
        marker = new google.maps.Marker({
          position: location,
          map: map,
          title: 'Login Location'
        });
      } else if (typeof mapboxgl !== 'undefined') {
        // Use Mapbox GL JS (High-quality satellite imagery)
        mapboxgl.accessToken = '{{ config("services.mapbox.access_token") }}';
        $('#map').innerHTML = ''; // Clear any existing content
        
        map = new mapboxgl.Map({
          container: 'map',
          style: 'mapbox://styles/mapbox/satellite-streets-v12', // Satellite view by default
          center: [longitude, latitude],
          zoom: 12
        });
        
        // Add navigation controls
        map.addControl(new mapboxgl.NavigationControl());
        
        // Add style switcher
        map.on('load', function() {
          // Add layer switcher
          const layerList = document.createElement('div');
          layerList.className = 'mapboxgl-ctrl mapboxgl-ctrl-group';
          layerList.style.position = 'absolute';
          layerList.style.top = '10px';
          layerList.style.right = '10px';
          layerList.style.background = 'white';
          layerList.style.borderRadius = '4px';
          layerList.style.padding = '5px';
          layerList.innerHTML = `
            <select onchange="map.setStyle('mapbox://styles/mapbox/' + this.value)" style="border:none;background:transparent;">
              <option value="satellite-streets-v12" selected>Satellite</option>
              <option value="streets-v12">Streets</option>
              <option value="outdoors-v12">Outdoors</option>
              <option value="light-v11">Light</option>
              <option value="dark-v11">Dark</option>
            </select>
          `;
          document.getElementById('map').appendChild(layerList);
        });
        
        // Add marker
        marker = new mapboxgl.Marker()
          .setLngLat([longitude, latitude])
          .setPopup(new mapboxgl.Popup().setHTML(`
            <div class="text-center">
              <strong>Login Location</strong><br>
              <small class="text-muted">Lat: ${latitude.toFixed(6)}<br>Lng: ${longitude.toFixed(6)}</small>
            </div>
          `))
          .addTo(map);
      } else if (typeof L !== 'undefined') {
        // Use Enhanced Leaflet with Multiple Providers
        console.log('Initializing Leaflet map');
        $('#map').innerHTML = ''; // Clear any existing content
        
        try {
          map = L.map('map', {
            center: [latitude, longitude],
            zoom: 12,
            zoomControl: true,
            attributionControl: true
          });
        
        // Define multiple tile layers
        const baseLayers = {
          "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }),
          
          "Satellite (Esri)": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© <a href="https://www.esri.com/">Esri</a>, DigitalGlobe, GeoEye, Earthstar Geographics, CNES/Airbus DS, USDA, USGS, AeroGRID, IGN, and the GIS User Community'
          }),
          
          "Terrain": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://opentopomap.org/">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
          }),
          
          "CartoDB Positron": L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>'
          }),
          
          "Stamen Toner": L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}{r}.png', {
            attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> — Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          })
        };
        
        // Add default layer (Satellite)
        baseLayers["Satellite (Esri)"].addTo(map);
        
        // Add layer control for switching between map types
        L.control.layers(baseLayers).addTo(map);
        
          // Add marker with enhanced popup
          marker = L.marker([latitude, longitude])
            .addTo(map)
            .bindPopup(`
              <div class="text-center">
                <strong>Login Location</strong><br>
                <small class="text-muted">Lat: ${latitude.toFixed(6)}<br>Lng: ${longitude.toFixed(6)}</small>
              </div>
            `)
            .openPopup();
            
          console.log('Leaflet map initialized successfully');
          
        } catch (error) {
          console.error('Error initializing Leaflet map:', error);
          $('#map').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Error loading map</p></div>';
        }
      } else {
        // No mapping library available
        $('#map').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted mb-0"><i class="fas fa-exclamation-triangle me-2"></i>No mapping service available</p></div>';
      }
    }

    const search = $('#lm-search');
    const status = $('#lm-status');
    const items = $$('.lm-item'); // both timeline li and table tr have this

    function applyFilter(){
      const q = (search.value || '').toLowerCase().trim();
      const st = (status.value || '').toLowerCase();
      items.forEach(el=>{
        const txt = (el.getAttribute('data-text')||'').toLowerCase();
        const s = (el.getAttribute('data-status')||'').toLowerCase();
        const matchQ = !q || txt.includes(q);
        const matchS = !st || s === st;
        el.style.display = (matchQ && matchS) ? '' : 'none';
      });
    }

    if(search) search.addEventListener('input', applyFilter);
    if(status) status.addEventListener('change', applyFilter);

    // Tabs toggle
    const tabButtons = $$('.tab-nav .btn');
    tabButtons.forEach(btn=>{
      btn.addEventListener('click', ()=>{
        tabButtons.forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const tab = btn.getAttribute('data-tab');
        $('#tab-timeline').classList.toggle('d-none', tab !== 'timeline');
        $('#tab-table').classList.toggle('d-none', tab !== 'table');
      });
    });

    // View details modal
    const viewButtons = $$('.view-details');
    viewButtons.forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const email = btn.getAttribute('data-email');
        const ip = btn.getAttribute('data-ip');
        const location = btn.getAttribute('data-location');
        const usertype = btn.getAttribute('data-usertype');
        const ua = btn.getAttribute('data-ua');
        const lat = btn.getAttribute('data-latitude');
        const lng = btn.getAttribute('data-longitude');

        $('#modal-email').textContent = email;
        $('#modal-ip').textContent = ip;
        $('#modal-location').textContent = location;
        $('#modal-usertype').textContent = usertype;
        $('#modal-ua').textContent = ua;

        // Show modal first, then initialize map when modal is fully shown
        const modal = new bootstrap.Modal($('#detailsModal'));
        
        // Store coordinates for later use
        $('#detailsModal').setAttribute('data-lat', lat);
        $('#detailsModal').setAttribute('data-lng', lng);
        
        modal.show();
      });
    });

    // Handle modal events for proper map initialization
    const detailsModal = $('#detailsModal');
    if (detailsModal) {
      detailsModal.addEventListener('shown.bs.modal', function() {
        // Modal is fully shown, now initialize the map
        const lat = this.getAttribute('data-lat');
        const lng = this.getAttribute('data-lng');
        
        if (lat && lng) {
          // Small delay to ensure DOM is ready
          setTimeout(() => {
            initMap(lat, lng);
            
            // Force map resize after initialization (for Leaflet)
            if (map && typeof map.invalidateSize === 'function') {
              setTimeout(() => map.invalidateSize(), 100);
            }
            
            // Force map resize for Mapbox
            if (map && typeof map.resize === 'function') {
              setTimeout(() => map.resize(), 100);
            }
          }, 100);
        }
      });
      
      // Clean up map when modal is hidden
      detailsModal.addEventListener('hidden.bs.modal', function() {
        if (map) {
          // Clean up map instance
          if (typeof map.remove === 'function') {
            map.remove(); // Leaflet cleanup
          } else if (typeof map.destroy === 'function') {
            map.destroy(); // Other map libraries
          }
          map = null;
          marker = null;
        }
        
        // Clear map container
        const mapContainer = $('#map');
        if (mapContainer) {
          mapContainer.innerHTML = '';
        }
      });
    }
  })();
</script>