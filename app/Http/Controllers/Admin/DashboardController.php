<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStep;
use App\Models\Event;
use App\Models\FacultyMember;
use App\Models\Facility;
use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Pathway;
use App\Models\Testimonial;

class DashboardController extends Controller
{
    public function index()
    {
        // One stat card per content model, each linking to its (upcoming) admin
        // index page — grouped roughly by which public page they power.
        $stats = [
            ['label' => 'News Posts', 'count' => NewsPost::count(), 'icon' => 'newspaper', 'route' => 'admin.news.index'],
            ['label' => 'Upcoming Events', 'count' => Event::upcoming()->count(), 'icon' => 'event', 'route' => 'admin.events.index'],
            ['label' => 'Pathways', 'count' => Pathway::count(), 'icon' => 'route', 'route' => 'admin.pathways.index'],
            ['label' => 'Faculty Members', 'count' => FacultyMember::count(), 'icon' => 'groups', 'route' => 'admin.faculty.index'],
            ['label' => 'Admission Steps', 'count' => AdmissionStep::count(), 'icon' => 'how_to_reg', 'route' => 'admin.admission-steps.index'],
            ['label' => 'FAQs', 'count' => FaqItem::count(), 'icon' => 'quiz', 'route' => 'admin.faqs.index'],
            ['label' => 'Gallery Items', 'count' => GalleryItem::count(), 'icon' => 'photo_library', 'route' => 'admin.gallery.index'],
            ['label' => 'Facilities', 'count' => Facility::count(), 'icon' => 'apartment', 'route' => 'admin.facilities.index'],
            ['label' => 'Testimonials', 'count' => Testimonial::count(), 'icon' => 'format_quote', 'route' => 'admin.testimonials.index'],
        ];

        $recentNews = NewsPost::latestFirst()->take(5)->get();
        $upcomingEvents = Event::upcoming()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentNews', 'upcomingEvents'));
    }
}