<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Global Sustainability Summit',
                'event_date' => now()->addDays(14),
                'event_time' => '10:00',
                'location' => 'Auden Auditorium',
                'description' => 'A student-led summit on environmental leadership and climate policy.',
            ],
            [
                'title' => "Founders' Day Convocation",
                'event_date' => now()->addDays(21),
                'event_time' => '14:00',
                'location' => 'Chapel Green',
                'description' => "Annual ceremony honoring Starlight's founding and academic traditions.",
            ],
            [
                'title' => 'AI in Education Seminar',
                'event_date' => now()->addDays(35),
                'event_time' => '16:30',
                'location' => 'Innovation Center',
                'description' => 'Faculty and guest researchers discuss the future of adaptive learning.',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}