<?php

namespace App\Http\Controllers;

use App\Models\TrackedPlugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrackedPluginController extends Controller
{
    /**
     * Display plugin tracking dashboard & site list.
     */
    public function index(Request $request)
    {
        $query = TrackedPlugin::query();

        // Search filter (URL, Site ID, Admin Email, or Plugin Name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('site_url', 'like', "%{$search}%")
                  ->orWhere('site_id', 'like', "%{$search}%")
                  ->orWhere('admin_email', 'like', "%{$search}%")
                  ->orWhere('plugin_name', 'like', "%{$search}%");
            });
        }

        // Plugin filter
        if ($request->filled('plugin_name')) {
            $query->where('plugin_name', $request->input('plugin_name'));
        }

        // Status filter
        if ($request->input('status') === 'active') {
            $query->active(30);
        } elseif ($request->input('status') === 'inactive') {
            $query->inactive(30);
        } elseif ($request->input('status') === 'deactivated') {
            $query->where('status', 'deactivated');
        }

        // Version filter
        if ($request->filled('version')) {
            $query->where('plugin_version', $request->input('version'));
        }

        // Stats summary
        $totalRegistered = TrackedPlugin::count();
        $currentlyActive = TrackedPlugin::active(30)->count();
        $deactivatedCount = TrackedPlugin::where('status', 'deactivated')->count();
        $inactiveCount = TrackedPlugin::inactive(30)->count();
        $woocommerceActive = TrackedPlugin::active(30)
            ->whereNotNull('woocommerce_version')
            ->where('woocommerce_version', '!=', '')
            ->count();

        // Breakdown stats by Plugin Name
        $pluginBreakdown = TrackedPlugin::select('plugin_name', DB::raw('count(*) as count'))
            ->groupBy('plugin_name')
            ->orderBy('count', 'desc')
            ->get();

        // Unique plugin names for filter dropdown
        $allPlugins = TrackedPlugin::distinct()->pluck('plugin_name')->filter()->sort();

        // Version breakdown
        $versionBreakdown = TrackedPlugin::select('plugin_version', DB::raw('count(*) as count'))
            ->groupBy('plugin_version')
            ->orderBy('count', 'desc')
            ->get();

        // Unique plugin versions list for filter dropdown
        $allVersions = TrackedPlugin::distinct()->pluck('plugin_version')->filter()->sortDesc();

        $sites = $query->orderBy('last_seen', 'desc')->paginate(15)->withQueryString();

        return view('tracked_plugins.index', compact(
            'sites',
            'totalRegistered',
            'currentlyActive',
            'deactivatedCount',
            'inactiveCount',
            'woocommerceActive',
            'pluginBreakdown',
            'allPlugins',
            'versionBreakdown',
            'allVersions'
        ));
    }

    /**
     * Delete a tracked site telemetry record.
     */
    public function destroy(TrackedPlugin $trackedPlugin)
    {
        $trackedPlugin->delete();

        return redirect()->route('tracked-plugins.index')
            ->with('success', 'Plugin telemetry record removed successfully.');
    }

    /**
     * Public API endpoint to receive telemetry pings and activation/deactivation events from WordPress plugin installations.
     */
    public function ping(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id'             => 'required|string|max:191',
            'site_url'            => 'required|string|max:255',
            'admin_email'         => 'nullable|string|max:255',
            'email'               => 'nullable|string|max:255',
            'plugin_name'         => 'nullable|string|max:191',
            'plugin_slug'         => 'nullable|string|max:191',
            'plugin_version'      => 'nullable|string|max:50',
            'wordpress_version'  => 'nullable|string|max:50',
            'php_version'        => 'nullable|string|max:50',
            'woocommerce_version' => 'nullable|string|max:50',
            'action'              => 'nullable|string|max:50',
            'event'               => 'nullable|string|max:50',
            'status'              => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $siteId = $request->input('site_id');
        $siteUrl = $request->input('site_url');
        $adminEmail = $request->input('admin_email') ?? $request->input('email');
        $pluginName = $request->input('plugin_name') ?? $request->input('plugin_slug') ?? 'assertivlogix-local-currency-display-woocommerce';

        $actionInput = strtolower($request->input('action') ?? $request->input('event') ?? $request->input('status') ?? 'ping');
        $isDeactivate = in_array($actionInput, ['deactivate', 'deactivated']);

        $existing = TrackedPlugin::where('site_id', $siteId)->where('plugin_name', $pluginName)->first();

        if ($isDeactivate) {
            $status = 'deactivated';
            $activatedAt = $existing ? ($existing->activated_at ?? now()) : now();
            $deactivatedAt = now();
        } else {
            $status = 'active';
            $activatedAt = $existing ? ($existing->activated_at ?? now()) : now();
            $deactivatedAt = null;
        }

        $record = TrackedPlugin::updateOrCreate(
            [
                'site_id'     => $siteId,
                'plugin_name' => $pluginName,
            ],
            [
                'site_url'            => $siteUrl,
                'admin_email'         => $adminEmail,
                'plugin_version'      => $request->input('plugin_version', $existing->plugin_version ?? '1.0.0'),
                'wordpress_version'  => $request->input('wordpress_version', $existing->wordpress_version ?? 'Unknown'),
                'php_version'        => $request->input('php_version', $existing->php_version ?? 'Unknown'),
                'woocommerce_version' => $request->input('woocommerce_version', $existing->woocommerce_version ?? null),
                'status'              => $status,
                'activated_at'        => $activatedAt,
                'deactivated_at'      => $deactivatedAt,
                'last_seen'           => now(),
                'ip_address'          => $request->ip(),
            ]
        );

        return response()->json([
            'status'        => 'success',
            'message'       => $isDeactivate ? 'Deactivation recorded.' : 'Telemetry ping received successfully.',
            'site_id'       => $record->site_id,
            'plugin_name'   => $record->plugin_name,
            'plugin_status' => $record->status,
            'activated_at'  => $record->activated_at ? $record->activated_at->toIso8601String() : null,
            'deactivated_at'=> $record->deactivated_at ? $record->deactivated_at->toIso8601String() : null,
            'last_seen'     => $record->last_seen->toIso8601String(),
        ], 200);
    }
}
