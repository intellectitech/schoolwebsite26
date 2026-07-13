<footer class="w-full mb-16 md:mb-0 bg-tertiary border-t-2 border-secondary-fixed flex flex-col items-center p-8 gap-4 text-center">
    <h2 class="font-headline-md text-headline-md text-secondary-fixed">Starlight Academy</h2>

    <nav class="flex flex-wrap justify-center gap-6">
        <a href="#" class="font-caption text-caption text-on-tertiary/70 hover:text-secondary-fixed transition-colors">Privacy Policy</a>
        <a href="#" class="font-caption text-caption text-on-tertiary/70 hover:text-secondary-fixed transition-colors">Contact Us</a>
        <a href="#" class="font-caption text-caption text-on-tertiary/70 hover:text-secondary-fixed transition-colors">Campus Map</a>
        <a href="{{ route('contact.show') }}" class="font-caption text-caption text-on-tertiary/70 hover:text-secondary-fixed transition-colors">Contact Us</a>
    </nav>

    <p class="font-caption text-caption text-on-tertiary/50">
        &copy; {{ now()->year }} Starlight Academy. All rights reserved.
    </p>

    <div class="flex gap-4 mt-2">
        <x-icon name="public" class="text-on-tertiary/70 cursor-pointer hover:text-secondary-fixed" />
        <x-icon name="school" class="text-on-tertiary/70 cursor-pointer hover:text-secondary-fixed" />
        <x-icon name="mail" class="text-on-tertiary/70 cursor-pointer hover:text-secondary-fixed" />
    </div>
</footer>