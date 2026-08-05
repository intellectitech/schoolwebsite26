<?php
// ============================================================
//  includes/seo-config.php
//  Central SEO metadata for every static route on the site —
//  one entry per page, keyed by the clean route name used in
//  .htaccess (see project root) and by renderSeoTags() in
//  includes/functions.php.
//
//  Individual news articles do NOT live here — article.php
//  builds its title/description straight from the database row
//  (real per-article SEO beats a shared template) and passes
//  them to renderSeoTags() as overrides instead.
//
//  Keep every title under ~60 characters and every description
//  between 150-160 characters so Google doesn't truncate them.
// ============================================================

return [
    'home' => [
        'title' => "Uganda Martyrs Primary School, Namugongo | Official Website",
        'description' => "Uganda Martyrs Primary School in Namugongo, Wakiso District offers "
            . "Primary One to Primary Seven education with boarding and day options.",
    ],
    'about' => [
        'title' => "About Us | Uganda Martyrs Primary School, Namugongo",
        'description' => "The history, mission and values of Uganda Martyrs Primary School in "
            . "Namugongo, Wakiso District — a P1 to P7 school raised in the shadow of the shrines.",
    ],
    'admissions' => [
        'title' => "Admissions | Uganda Martyrs Primary School, Namugongo",
        'description' => "How to apply to Uganda Martyrs Primary School, Namugongo. Admission "
            . "process, requirements and fees for Primary One through Primary Seven.",
    ],
    'contact' => [
        'title' => "Contact Us | Uganda Martyrs Primary School, Namugongo",
        'description' => "Get in touch with Uganda Martyrs Primary School, Namugongo. Address, "
            . "phone, email and directions from Kampala and the Namugongo shrine.",
    ],
    'gallery' => [
        'title' => "Gallery | Uganda Martyrs Primary School, Namugongo",
        'description' => "Photos of school life at Uganda Martyrs Primary School, Namugongo — "
            . "classrooms, sports, ceremonies and events.",
    ],
    'news' => [
        'title' => "News | Uganda Martyrs Primary School, Namugongo",
        'description' => "Latest news, events and announcements from Uganda Martyrs Primary "
            . "School, Namugongo.",
    ],
    'staff' => [
        'title' => "Our Staff | Uganda Martyrs Primary School, Namugongo",
        'description' => "Meet the teachers and staff of Uganda Martyrs Primary School, "
            . "Namugongo — the people who make the school what it is.",
    ],
];
