<?php

namespace App\Http\Controllers;

use App\Models\BlockedDomain;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Location;
use App\Models\Device;
use Log;

class DomainBlockingController extends Controller
{
    /**
     * Display a listing of the resource (API endpoint).
     */
    public function index(Request $request)
    {
        $categories = Category::withCount('blockedDomains')->ordered()->get();

        $query = BlockedDomain::with('category')
            ->when($request->filled('category'), function ($q) use ($request) {
                return $q->byCategory($request->category);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                return $q->search($request->search);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'active') {
                    return $q->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    return $q->where('is_active', false);
                }
            });

        $domains = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $domains->items(),
            'pagination' => [
                'current_page' => $domains->currentPage(),
                'last_page' => $domains->lastPage(),
                'per_page' => $domains->perPage(),
                'total' => $domains->total(),
            ]
        ]);
    }

    /**
     * Display the domain blocking page (Web view).
     */
    public function show_page(Request $request, $locale)
    {
        $categories = Category::withCount('blockedDomains')->ordered()->get();

        $query = BlockedDomain::with('category')
            ->when($request->filled('category'), function ($q) use ($request) {
                return $q->byCategory($request->category);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                return $q->search($request->search);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'active') {
                    return $q->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    return $q->where('is_active', false);
                }
            });

        $domains = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('domain-blocking-' . $locale, compact('categories', 'domains', 'locale'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string|max:1000',
            'block_subdomains' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $domain = BlockedDomain::create([
                'domain' => $request->domain,
                'category_id' => $request->category_id,
                'notes' => $request->notes,
                'block_subdomains' => $request->boolean('block_subdomains', true),
                'is_active' => true,
                'source' => 'manual',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Domain added successfully',
                'domain' => $domain->load('category')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Domain validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add domain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockedDomain $blockedDomain)
    {
        $blockedDomain->load('category');
        
        return response()->json([
            'success' => true,
            'domain' => $blockedDomain
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockedDomain $blockedDomain)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'notes' => 'nullable|string|max:1000',
            'block_subdomains' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blockedDomain->update($request->only([
                'category_id', 'notes', 'block_subdomains', 'is_active'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Domain updated successfully',
                'domain' => $blockedDomain->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update domain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockedDomain $blockedDomain)
    {
        try {
            $domain = $blockedDomain->domain;
            $blockedDomain->delete();

            return response()->json([
                'success' => true,
                'message' => "Domain '{$domain}' has been deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete domain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete domains.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_ids' => 'required|array',
            'domain_ids.*' => 'exists:blocked_domains,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = BlockedDomain::whereIn('id', $request->domain_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} domains deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete domains: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import domains from file or text.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'domains' => 'required_without:file|string',
            'file' => 'required_without:domains|file|mimes:txt,csv|max:2048',
            'block_subdomains' => 'boolean',
            'overwrite' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $domains = [];

            if ($request->hasFile('file')) {
                $content = file_get_contents($request->file('file')->path());
                $domains = array_filter(array_map('trim', explode("\n", $content)));
            } else {
                $domains = array_filter(array_map('trim', explode("\n", $request->domains)));
            }

            if (empty($domains)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid domains found in the input'
                ], 422);
            }

            $options = [
                'block_subdomains' => $request->boolean('block_subdomains', true),
                'overwrite' => $request->boolean('overwrite', false),
                'notes' => $request->notes,
                'source' => 'import',
            ];

            $result = BlockedDomain::bulkImport($domains, $request->category_id, $options);

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$result['imported']} domains imported.",
                'imported' => $result['imported'],
                'errors' => $result['errors']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export domains to file.
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'format' => 'in:txt,csv,json',
            'active_only' => 'nullable|in:true,false,1,0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = BlockedDomain::with('category')
                ->when($request->filled('category_id'), function ($q) use ($request) {
                    return $q->where('category_id', $request->category_id);
                })
                ->when($request->boolean('active_only'), function ($q) {
                    return $q->where('is_active', true);
                });

            $domains = $query->get();
            $format = $request->get('format', 'txt');
            $timestamp = now()->format('Y-m-d_H-i-s');

            switch ($format) {
                case 'csv':
                    $content = "Domain,Category,Block Subdomains,Status,Created At,Notes\n";
                    foreach ($domains as $domain) {
                        $content .= sprintf(
                            "%s,%s,%s,%s,%s,\"%s\"\n",
                            $domain->domain,
                            $domain->category->name,
                            $domain->block_subdomains ? 'Yes' : 'No',
                            $domain->is_active ? 'Active' : 'Inactive',
                            $domain->created_at->format('Y-m-d H:i:s'),
                            str_replace('"', '""', $domain->notes ?? '')
                        );
                    }
                    $filename = "blocked_domains_{$timestamp}.csv";
                    $mimeType = 'text/csv';
                    break;

                case 'json':
                    $content = json_encode($domains->toArray(), JSON_PRETTY_PRINT);
                    $filename = "blocked_domains_{$timestamp}.json";
                    $mimeType = 'application/json';
                    break;

                default: // txt
                    $content = $domains->pluck('domain')->implode("\n");
                    $filename = "blocked_domains_{$timestamp}.txt";
                    $mimeType = 'text/plain';
                    break;
            }

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle category status.
     */
    public function toggleCategory(Request $request, Category $category)
    {
        Log::info('toggleCategory');
        Log::info($category);
        try {
            $category->update(['is_enabled' => !$category->is_enabled]);

            // Find all locations which have that category enabled and increment 
            // config_version of their devices.
            $locationSettings = \App\Models\LocationSettingsV2::where('web_filter_enabled', true)
                ->whereJsonContains('web_filter_categories', (string)$category->id)
                ->with(['location.device'])
                ->get();

            $devicesUpdated = 0;
            foreach ($locationSettings as $settings) {
                if ($settings->location_id) {
                    $device_id = Location::where('id', $settings->location_id)->first()->device_id;
                    if ($device_id) {
                        $device = Device::where('id', $device_id)->first();
                        if ($device) {
                            $device->increment('config_version');
                        }
                        $devicesUpdated++;
                    }
                }
            }

            $message = $category->is_enabled 
                ? "Category '{$category->name}' enabled" 
                : "Category '{$category->name}' disabled";
            
            if ($devicesUpdated > 0) {
                $message .= " and {$devicesUpdated} device(s) configuration updated";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'category' => $category,
                'devices_updated' => $devicesUpdated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get domain statistics.
     */
    public function stats()
    {
        try {
            $stats = [
                'total_domains' => BlockedDomain::count(),
                'active_domains' => BlockedDomain::where('is_active', true)->count(),
                'total_categories' => Category::count(),
                'enabled_categories' => Category::where('is_enabled', true)->count(),
                'domains_by_category' => Category::withCount('blockedDomains')->get(),
                'recent_additions' => BlockedDomain::with('category')
                    ->latest()
                    ->limit(5)
                    ->get(),
                'top_categories' => Category::withCount('blockedDomains')
                    ->orderBy('blocked_domains_count', 'desc')
                    ->limit(5)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a domain is blocked.
     */
    public function checkDomain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $domain = BlockedDomain::normalizeDomain($request->domain);
            
            $blockedDomains = BlockedDomain::with('category')
                ->whereHas('category', function ($q) {
                    $q->where('is_enabled', true);
                })
                ->where('is_active', true)
                ->get();

            $matches = [];
            foreach ($blockedDomains as $blockedDomain) {
                if ($blockedDomain->blocks($domain)) {
                    $matches[] = $blockedDomain;
                }
            }

            return response()->json([
                'success' => true,
                'domain' => $domain,
                'is_blocked' => !empty($matches),
                'matches' => $matches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Check failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
