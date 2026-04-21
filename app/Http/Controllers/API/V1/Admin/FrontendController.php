<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\FrontendSetting;
use App\Models\HomePageSection;
use App\Models\PromotionalBanner;
use App\Models\Slider;
use App\Models\SliderSlide;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function settings()
    {
        $settings = FrontendSetting::pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|string',
            'site_favicon' => 'nullable|string',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'currency_id' => 'nullable|exists:currencies,id',
            'language_id' => 'nullable|exists:languages,id',
            'footer_text' => 'nullable|string',
            'copyright_text' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'twitter_url' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'linkedin_url' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            FrontendSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }

    public function homeSections()
    {
        $sections = HomePageSection::with('items')->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $sections,
        ]);
    }

    public function storeHomeSection(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:home_page_sections,key',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $section = HomePageSection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section created successfully',
            'data' => $section,
        ], 201);
    }

    public function updateHomeSection(Request $request, int $id)
    {
        $section = HomePageSection::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'key' => 'string|unique:home_page_sections,key,'.$id,
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully',
            'data' => $section,
        ]);
    }

    public function destroyHomeSection(int $id)
    {
        $section = HomePageSection::findOrFail($id);
        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully',
        ]);
    }

    public function sliders()
    {
        $sliders = Slider::with('slides')->orderBy('position')->get();

        return response()->json([
            'success' => true,
            'data' => $sliders,
        ]);
    }

    public function storeSlider(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slider = Slider::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Slider created successfully',
            'data' => $slider,
        ], 201);
    }

    public function addSlide(Request $request, int $sliderId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'required|string',
            'link' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slider_id'] = $sliderId;

        $slide = SliderSlide::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Slide created successfully',
            'data' => $slide,
        ], 201);
    }

    public function updateSlide(Request $request, int $id)
    {
        $slide = SliderSlide::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'string',
            'link' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slide->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Slide updated successfully',
            'data' => $slide,
        ]);
    }

    public function deleteSlide(int $id)
    {
        $slide = SliderSlide::findOrFail($id);
        $slide->delete();

        return response()->json([
            'success' => true,
            'message' => 'Slide deleted successfully',
        ]);
    }

    public function banners()
    {
        $banners = PromotionalBanner::orderBy('position')->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|string',
            'link' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $banner = PromotionalBanner::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully',
            'data' => $banner,
        ], 201);
    }

    public function updateBanner(Request $request, int $id)
    {
        $banner = PromotionalBanner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'image' => 'string',
            'link' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $banner->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully',
            'data' => $banner,
        ]);
    }

    public function deleteBanner(int $id)
    {
        $banner = PromotionalBanner::findOrFail($id);
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully',
        ]);
    }
}
