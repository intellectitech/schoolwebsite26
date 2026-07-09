@props(['emptyMessage' => 'No records found.'])

<div class="bg-white rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            {{ $slot }}
        </table>
    </div>
</div>