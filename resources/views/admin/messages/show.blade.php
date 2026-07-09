<x-layouts.admin :title="'Message'" :active="'messages'">

    <x-admin.page-header title="Message from {{ $message->full_name }}" />

    <div class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-2xl">
        <dl class="grid grid-cols-2 gap-4 text-sm mb-6 pb-6 border-b border-outline-variant/20">
            <div><dt class="text-on-surface-variant">From</dt><dd class="font-semibold">{{ $message->full_name }}</dd></div>
            <div><dt class="text-on-surface-variant">Email</dt><dd class="font-semibold">{{ $message->email }}</dd></div>
            <div><dt class="text-on-surface-variant">Subject</dt><dd class="font-semibold">{{ $message->subject ?? '—' }}</dd></div>
            <div><dt class="text-on-surface-variant">Received</dt><dd class="font-semibold">{{ $message->created_at->format('M j, Y \a\t g:i A') }}</dd></div>
        </dl>

        <p class="text-sm text-on-surface-variant whitespace-pre-line leading-relaxed mb-6">{{ $message->message }}</p>

        
            href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject ?? 'Your inquiry to Lyceum Academy' }}"
            class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors"
        >
            <x-icon name="reply" class="!text-lg" /> Reply by Email
        </a>
    </div>

</x-layouts.admin>