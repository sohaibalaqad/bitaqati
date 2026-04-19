<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Plan;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    // ── List all sections ─────────────────────────────────────────
    public function index()
    {
        $sections = HomepageSection::orderBy('sort_order')->get();
        $plans    = Plan::orderBy('price')->get();

        return view('superadmin.homepage.index', compact('sections', 'plans'));
    }

    // ── Show edit form ────────────────────────────────────────────
    public function edit(HomepageSection $section)
    {
        $plans = Plan::orderBy('price')->get();
        return view('superadmin.homepage.edit', compact('section', 'plans'));
    }

    // ── Save section changes ──────────────────────────────────────
    public function update(Request $request, HomepageSection $section)
    {
        $request->validate([
            'label'     => 'required|string|max:120',
            'is_active' => 'boolean',
        ]);

        $content = $this->buildContent($request, $section->type);

        $section->update([
            'label'     => strip_tags($request->label),
            'content'   => $content,
            'is_active' => $request->boolean('is_active'),
        ]);

        HomepageSection::clearCache();

        return back()->with('success', 'تم حفظ التغييرات بنجاح ✓');
    }

    // ── Toggle active ─────────────────────────────────────────────
    public function toggle(HomepageSection $section)
    {
        $section->update(['is_active' => ! $section->is_active]);
        HomepageSection::clearCache();

        return response()->json(['active' => $section->is_active]);
    }

    // ── Reorder (drag-drop) ───────────────────────────────────────
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $sort => $id) {
            HomepageSection::where('id', $id)->update(['sort_order' => $sort + 1]);
        }

        HomepageSection::clearCache();

        return response()->json(['ok' => true]);
    }

    // ── Delete section ────────────────────────────────────────────
    public function destroy(HomepageSection $section)
    {
        $section->delete();
        HomepageSection::clearCache();

        return back()->with('success', 'تم حذف القسم.');
    }

    // ── Plan management ───────────────────────────────────────────
    public function plans()
    {
        $plans = Plan::orderBy('price')->get();
        return view('superadmin.homepage.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'price'      => 'required|numeric|min:0',
            'max_cards'  => 'nullable|integer|min:0',
            'max_users'  => 'nullable|integer|min:0',
            'is_popular' => 'boolean',
            'features'   => 'nullable|string',
        ]);

        $data['features']  = $this->parseFeatures($request->features);
        $data['is_active'] = true;

        Plan::create($data);
        \Illuminate\Support\Facades\Cache::forget('homepage_plans');

        return back()->with('success', 'تم إضافة الخطة بنجاح.');
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'price'      => 'required|numeric|min:0',
            'max_cards'  => 'nullable|integer|min:0',
            'max_users'  => 'nullable|integer|min:0',
            'is_popular' => 'boolean',
            'is_active'  => 'boolean',
            'features'   => 'nullable|string',
        ]);

        $data['features']   = $this->parseFeatures($request->features);
        $data['is_active']  = $request->boolean('is_active');
        $data['is_popular'] = $request->boolean('is_popular');

        $plan->update($data);
        \Illuminate\Support\Facades\Cache::forget('homepage_plans');
        HomepageSection::clearCache();

        return back()->with('success', 'تم تحديث الخطة.');
    }

    public function destroyPlan(Plan $plan)
    {
        $plan->delete();
        \Illuminate\Support\Facades\Cache::forget('homepage_plans');

        return back()->with('success', 'تم حذف الخطة.');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function buildContent(Request $request, string $type): array
    {
        $s = fn($v) => strip_tags((string) ($v ?? ''));

        return match ($type) {
            'hero' => [
                'badge'         => $s($request->badge),
                'title'         => $s($request->title),
                'subtitle'      => $s($request->subtitle),
                'cta_primary'   => ['text' => $s($request->cta_primary_text),   'url' => $s($request->cta_primary_url)],
                'cta_secondary' => ['text' => $s($request->cta_secondary_text), 'url' => $s($request->cta_secondary_url)],
                'trust_items'   => array_filter(array_map('strip_tags', explode("\n", $request->trust_items ?? ''))),
            ],
            'features' => [
                'title'    => $s($request->title),
                'subtitle' => $s($request->subtitle),
                'items'    => $this->parseFeatureItems($request),
            ],
            'pricing' => [
                'title'    => $s($request->title),
                'subtitle' => $s($request->subtitle),
                'note'     => $s($request->note),
            ],
            'about' => [
                'title'    => $s($request->title),
                'subtitle' => $s($request->subtitle),
                'stats'    => $this->parseStats($request),
            ],
            'testimonials' => [
                'title'    => $s($request->title),
                'subtitle' => $s($request->subtitle),
                'items'    => $this->parseTestimonials($request),
            ],
            'faq' => [
                'title'    => $s($request->title),
                'subtitle' => $s($request->subtitle),
                'items'    => $this->parseFaqItems($request),
            ],
            'cta' => [
                'title'         => $s($request->title),
                'subtitle'      => $s($request->subtitle),
                'cta_primary'   => ['text' => $s($request->cta_primary_text),   'url' => $s($request->cta_primary_url)],
                'cta_secondary' => ['text' => $s($request->cta_secondary_text), 'url' => $s($request->cta_secondary_url)],
                'note'          => $s($request->note),
            ],
            default => $request->only(['title', 'subtitle']),
        };
    }

    private function parseFeatures(mixed $raw): array
    {
        if (is_array($raw)) return array_values(array_filter($raw));
        return array_values(array_filter(array_map('trim', explode("\n", $raw ?? ''))));
    }

    private function parseFeatureItems(Request $request): array
    {
        $icons  = $request->input('item_icon', []);
        $titles = $request->input('item_title', []);
        $descs  = $request->input('item_desc', []);
        $items  = [];
        foreach ($titles as $i => $title) {
            if (trim($title) === '') continue;
            $items[] = [
                'icon'  => strip_tags($icons[$i]  ?? 'bolt'),
                'title' => strip_tags($title),
                'desc'  => strip_tags($descs[$i] ?? ''),
            ];
        }
        return $items;
    }

    private function parseStats(Request $request): array
    {
        $values = $request->input('stat_value', []);
        $labels = $request->input('stat_label', []);
        $stats  = [];
        foreach ($values as $i => $val) {
            if (trim($val) === '') continue;
            $stats[] = ['value' => strip_tags($val), 'label' => strip_tags($labels[$i] ?? '')];
        }
        return $stats;
    }

    private function parseTestimonials(Request $request): array
    {
        $names = $request->input('t_name', []);
        $roles = $request->input('t_role', []);
        $texts = $request->input('t_text', []);
        $stars = $request->input('t_stars', []);
        $items = [];
        foreach ($names as $i => $name) {
            if (trim($name) === '') continue;
            $items[] = [
                'name'  => strip_tags($name),
                'role'  => strip_tags($roles[$i] ?? ''),
                'text'  => strip_tags($texts[$i] ?? ''),
                'stars' => (int) ($stars[$i] ?? 5),
            ];
        }
        return $items;
    }

    private function parseFaqItems(Request $request): array
    {
        $qs    = $request->input('faq_q', []);
        $as    = $request->input('faq_a', []);
        $items = [];
        foreach ($qs as $i => $q) {
            if (trim($q) === '') continue;
            $items[] = ['q' => strip_tags($q), 'a' => strip_tags($as[$i] ?? '')];
        }
        return $items;
    }
}
