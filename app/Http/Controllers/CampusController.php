<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\Testimonial;

class CampusController extends Controller
{
    public function index()
    {
        $galleryItems = GalleryItem::ordered()->get();

        // Split so the view can render the large hero facility separately
        // from the two smaller stacked ones, matching the screenshot layout.
        $featuredFacility = Facility::ordered()->where('is_featured', true)->first();
        $secondaryFacilities = Facility::ordered()->where('is_featured', false)->get();

        $testimonials = Testimonial::ordered()->get();

        return view('campus.index', compact(
            'galleryItems', 'featuredFacility', 'secondaryFacilities', 'testimonials'
        ));
    }
}