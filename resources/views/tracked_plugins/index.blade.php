@extends('layouts.app')

@section('title', 'Track Plugins')
@section('page-title', 'Track Plugins & Site Telemetry')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-plug text-primary me-2"></i> Track Plugins</h4>
        <small class="text-muted">Monitor WordPress plugin activations, deactivations, versions, environment statistics & live telemetry across all plugins</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#telemetryGuideModal">
            <i class="fa-solid fa-code me-1"></i> API Integration Snippet
        </button>
        <a href="{{ route('tracked-plugins.index') }}" class="btn btn-light border btn-sm rounded-pill px-3">
            <i class="fa-solid fa-rotate-right me-1"></i> Refresh
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted fw-semibold text-uppercase fs-12">Total Registered Sites</small>
                <h3 class="fw-bold text-slate-800 mb-0 mt-1">{{ number_format($totalRegistered) }}</h3>
                <small class="text-muted fs-12"><i class="fa-solid fa-database me-1"></i> All unique site-plugin records</small>
            </div>
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-globe"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted fw-semibold text-uppercase fs-12">Currently Active</small>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($currentlyActive) }}</h3>
                <small class="text-success fs-12"><i class="fa-solid fa-circle-check me-1"></i> Active & seen in last 30d</small>
            </div>
            <div class="stat-icon bg-success-subtle text-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted fw-semibold text-uppercase fs-12">Deactivated Plugins</small>
                <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($deactivatedCount) }}</h3>
                <small class="text-danger fs-12"><i class="fa-solid fa-power-off me-1"></i> Deactivation triggered</small>
            </div>
            <div class="stat-icon bg-danger-subtle text-danger">
                <i class="fa-solid fa-plug-circle-xmark"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted fw-semibold text-uppercase fs-12">WooCommerce Sites</small>
                <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($woocommerceActive) }}</h3>
                <small class="text-info fs-12"><i class="fa-solid fa-store me-1"></i> Active WooCommerce</small>
            </div>
            <div class="stat-icon bg-info-subtle text-info">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Plugin Breakdown -->
@if(isset($pluginBreakdown) && $pluginBreakdown->count() > 0)
<div class="card-custom p-3 mb-4 bg-white">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-cubes text-primary me-2"></i> Activations by Plugin Name</h6>
        <span class="badge bg-light text-muted border">Tracked Plugins Catalog</span>
    </div>
    <div class="d-flex flex-wrap gap-2 pt-1">
        @foreach($pluginBreakdown as $p)
        <a href="{{ route('tracked-plugins.index', ['plugin_name' => $p->plugin_name]) }}" class="text-decoration-none">
            <span class="badge {{ request('plugin_name') === $p->plugin_name ? 'bg-primary text-white' : 'bg-dark-subtle text-dark' }} border px-3 py-2 fs-13 rounded-pill">
                <i class="fa-solid fa-puzzle-piece me-1"></i> <strong>{{ $p->plugin_name }}</strong>: {{ $p->count }} site{{ $p->count > 1 ? 's' : '' }}
            </span>
        </a>
        @endforeach
        @if(request('plugin_name'))
            <a href="{{ route('tracked-plugins.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted py-1 fs-13">Clear Plugin Filter</a>
        @endif
    </div>
</div>
@endif

<!-- Filters & Search -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('tracked-plugins.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search URL, Email, Plugin, or Site ID..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="plugin_name" class="form-select">
                <option value="">-- All Plugins --</option>
                @foreach($allPlugins as $plg)
                    <option value="{{ $plg }}" {{ request('plugin_name') === $plg ? 'selected' : '' }}>{{ $plg }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- All Statuses --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Last 30d)</option>
                <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive (>30d)</option>
            </select>
        </div>
        <div class="col-md-1">
            <select name="version" class="form-select">
                <option value="">-- Version --</option>
                @foreach($allVersions as $v)
                    <option value="{{ $v }}" {{ request('version') === $v ? 'selected' : '' }}>v{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-medium"><i class="fa-solid fa-filter me-1"></i> Filter</button>
            <a href="{{ route('tracked-plugins.index') }}" class="btn btn-light border" title="Reset Filters"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </form>
</div>

<!-- Tracked Sites Data Table -->
<div class="card-custom">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-slate-800"><i class="fa-solid fa-list-check me-2 text-primary"></i> Tracked Plugin Installations & Activation History</h6>
        <span class="text-muted fs-13">Showing {{ $sites->firstItem() ?? 0 }} - {{ $sites->lastItem() ?? 0 }} of {{ $sites->total() }} entries</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th>Site Details</th>
                    <th>Plugin Name</th>
                    <th>Plugin Version</th>
                    <th>WP Version</th>
                    <th>PHP Version</th>
                    <th>WooCommerce</th>
                    <th>Activation History & Last Seen</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sites as $site)
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">
                            <a href="{{ Str::startsWith($site->site_url, ['http://', 'https://']) ? $site->site_url : 'http://' . $site->site_url }}" target="_blank" class="text-decoration-none text-dark fw-bold hover-primary">
                                {{ $site->site_url }} <i class="fa-solid fa-arrow-up-right-from-square fs-12 text-muted ms-1"></i>
                            </a>
                        </div>
                        @if($site->admin_email)
                            <div class="fs-12 text-primary my-1"><i class="fa-regular fa-envelope me-1"></i>{{ $site->admin_email }}</div>
                        @endif
                        <small class="text-muted font-monospace fs-12" title="Site ID">ID: {{ $site->site_id }}</small>
                    </td>
                    <td>
                        <span class="badge bg-dark-subtle text-dark border px-2 py-1 fw-semibold">
                            <i class="fa-solid fa-puzzle-piece me-1 text-primary"></i> {{ $site->plugin_name }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold">
                            v{{ $site->plugin_version }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark fw-medium fs-13">
                            <i class="fa-brands fa-wordpress text-primary me-1"></i> {{ $site->wordpress_version ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-dark border px-2 py-1 font-monospace">
                            PHP {{ $site->php_version ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        @if($site->woocommerce_version)
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-12">
                                <i class="fa-solid fa-cart-shopping me-1"></i> {{ $site->woocommerce_version }}
                            </span>
                        @else
                            <span class="text-muted fs-12">Not Installed</span>
                        @endif
                    </td>
                    <td>
                        <div class="fs-13 fw-semibold text-dark mb-1">
                            <i class="fa-solid fa-clock text-muted me-1"></i> Last Ping: {{ $site->last_seen ? $site->last_seen->diffForHumans() : 'Never' }}
                        </div>
                        @if($site->activated_at)
                            <div class="fs-12 text-success"><i class="fa-solid fa-play me-1 fs-10"></i> Activated: {{ $site->activated_at->format('M d, Y H:i') }}</div>
                        @endif
                        @if($site->deactivated_at)
                            <div class="fs-12 text-danger"><i class="fa-solid fa-stop me-1 fs-10"></i> Deactivated: {{ $site->deactivated_at->format('M d, Y H:i') }}</div>
                        @endif
                    </td>
                    <td>
                        @if($site->status === 'deactivated')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fs-12">
                                <i class="fa-solid fa-power-off me-1 fs-10"></i> Deactivated
                            </span>
                        @elseif($site->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fs-12">
                                <i class="fa-solid fa-circle me-1 fs-10"></i> Active
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill fs-12">
                                <i class="fa-solid fa-circle me-1 fs-10"></i> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <form action="{{ route('tracked-plugins.destroy', $site) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove telemetry for site {{ $site->site_url }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete Telemetry Record">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="fa-solid fa-satellite-dish fs-1 mb-2 text-muted opacity-50"></i>
                        <h6>No plugin telemetry pings received yet.</h6>
                        <small class="text-muted">Once your WordPress plugins send telemetry data to the API endpoint, site records will automatically appear here.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sites->hasPages())
    <div class="p-3 border-top">
        {{ $sites->links() }}
    </div>
    @endif
</div>

<!-- API Integration Snippet Modal -->
<div class="modal fade" id="telemetryGuideModal" tabindex="-1" aria-labelledby="telemetryGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="telemetryGuideModalLabel"><i class="fa-solid fa-code text-primary me-2"></i> WordPress Telemetry Activation & Deactivation Integration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">Add this snippet to your WordPress plugin to track activations, periodic pings, and deactivations:</p>

                <div class="bg-light p-3 rounded border mb-3">
                    <small class="fw-bold text-uppercase text-muted d-block mb-1">API Telemetry Endpoint URL:</small>
                    <code class="fs-14 text-primary">{{ url('/api/telemetry/ping') }}</code>
                </div>

                <div class="bg-dark text-light p-3 rounded overflow-auto font-monospace fs-13 mb-3">
<pre class="mb-0"><code>// Helper function to send telemetry to Assertivlogix server
function send_assertivlogix_telemetry($action = 'ping') {
    $site_id = get_option('assertivlogix_site_id');
    if (!$site_id) {
        $site_id = wp_generate_uuid4();
        update_option('assertivlogix_site_id', $site_id);
    }

    $body = array(
        'site_id'             => $site_id,
        'site_url'            => get_site_url(),
        'admin_email'         => get_option('admin_email'),
        'plugin_name'         => 'your-plugin-slug-name', // e.g. assertivlogix-local-currency-display-woocommerce
        'plugin_version'      => '1.0.0',
        'wordpress_version'  => get_bloginfo('version'),
        'php_version'        => PHP_VERSION,
        'woocommerce_version' => class_exists('WooCommerce') ? WC()->version : null,
        'action'              => $action, // 'activate', 'ping', or 'deactivate'
    );

    wp_remote_post('{{ url('/api/telemetry/ping') }}', array(
        'method'      => 'POST',
        'timeout'     => 15,
        'headers'     => array('Content-Type' => 'application/json'),
        'body'        => wp_json_encode($body),
        'data_format' => 'body',
    ));
}

// 1. Activation Hook
register_activation_hook(__FILE__, function() {
    send_assertivlogix_telemetry('activate');
});

// 2. Deactivation Hook (Triggers when user deactivates the plugin)
register_deactivation_hook(__FILE__, function() {
    send_assertivlogix_telemetry('deactivate');
});

// 3. Weekly Cron Telemetry Ping
if (!wp_next_scheduled('assertivlogix_weekly_telemetry_event')) {
    wp_schedule_event(time(), 'weekly', 'assertivlogix_weekly_telemetry_event');
}
add_action('assertivlogix_weekly_telemetry_event', function() {
    send_assertivlogix_telemetry('ping');
});</code></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
