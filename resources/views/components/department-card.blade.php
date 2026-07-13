@props(['department'])

@if($department->is_emergency)
    {{-- Emergency styling: red background, single prominent call action --}}
    <div class="bg-error-container border border-error/30 rounded-xl p-5 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center text-error flex-shrink-0">
                <x-icon name="shield" />
            </div>
            <div>
                <h4 class="font-body-lg text-body-lg !text-base font-semibold text-on-error-container">{{ $department->name }}</h4>
                <p class="text-xs text-error font-semibold">{{ $department->description }}</p>
            </div>
        </div>
        @if($department->phone)
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $department->phone) }}" class="inline-flex items-center gap-2 bg-error text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:opacity-90 transition-opacity">
                <x-icon name="emergency" class="!text-lg" /> Call Emergency
            </a>
        @endif
    </div>
@else
    <div class="bg-white rounded-xl border border-outline-variant/20 p-5 flex flex-col gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary/5 flex items-center justify-center text-primary flex-shrink-0">
                <x-icon name="apartment" />
            </div>
            <div>
                <h4 class="font-body-lg text-body-lg !text-base font-semibold text-on-surface">{{ $department->name }}</h4>
                @if($department->description)
                    <p class="text-xs text-on-surface-variant">{{ $department->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex gap-3">
            @if($department->phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $department->phone) }}" class="flex-1 inline-flex items-center justify-center gap-2 border border-outline-variant rounded-lg px-4 py-2.5 font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
                    <x-icon name="call" class="!text-lg" /> Tap to Call
                </a>
            @endif
            @if($department->email)
                <a href="mailto:{{ $department->email }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-secondary-fixed text-on-secondary-fixed rounded-lg px-4 py-2.5 font-label-md text-label-md hover:bg-secondary-container transition-colors">
                    <x-icon name="mail" class="!text-lg" /> Email Us
                </a>
            @endif
        </div>
    </div>
@endif