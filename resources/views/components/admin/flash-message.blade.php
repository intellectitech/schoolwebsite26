@if(session('success'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        x-transition
        class="mb-6 flex items-center gap-3 bg-secondary-fixed/20 border border-secondary-fixed/40 text-on-secondary-fixed rounded-lg px-4 py-3"
    >
        <x-icon name="check_circle" class="text-secondary" />
        <p class="font-body-md text-sm">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 flex items-center gap-3 bg-error-container border border-error/30 text-on-error-container rounded-lg px-4 py-3">
        <x-icon name="error" class="text-error" />
        <p class="font-body-md text-sm">{{ session('error') }}</p>
    </div>
@endif