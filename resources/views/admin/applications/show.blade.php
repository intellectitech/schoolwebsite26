<x-layouts.admin :title="'Application Review'" :active="'applications'">

    <x-admin.flash-message />
    <x-admin.page-header title="Application: {{ $application->full_name }}" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-xl border border-outline-variant/20 p-6">
                <h3 class="font-body-lg text-body-lg !text-base font-semibold text-on-surface mb-4">Personal Information</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-on-surface-variant">Full Name</dt><dd class="font-semibold">{{ $application->full_name }}</dd></div>
                    <div><dt class="text-on-surface-variant">Date of Birth</dt><dd class="font-semibold">{{ $application->date_of_birth->format('M j, Y') }}</dd></div>
                    <div><dt class="text-on-surface-variant">Email</dt><dd class="font-semibold">{{ $application->email }}</dd></div>
                    <div><dt class="text-on-surface-variant">Phone</dt><dd class="font-semibold">{{ $application->phone }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant/20 p-6">
                <h3 class="font-body-lg text-body-lg !text-base font-semibold text-on-surface mb-4">Academic History</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div><dt class="text-on-surface-variant">Current School</dt><dd class="font-semibold">{{ $application->current_school }}</dd></div>
                    <div><dt class="text-on-surface-variant">Grade Applying For</dt><dd class="font-semibold">{{ $application->grade_applying_for }}</dd></div>
                    <div><dt class="text-on-surface-variant">Current GPA</dt><dd class="font-semibold">{{ $application->current_gpa ?? '—' }}</dd></div>
                </dl>
                @if($application->transcript_path)
                    
                        href="{{ Storage::disk('public')->url($application->transcript_path) }}" target="_blank"
                        class="inline-flex items-center gap-2 text-primary hover:underline font-label-md text-label-md"
                    >
                        <x-icon name="description" class="!text-lg" /> View Uploaded Transcript
                    </a>
                @else
                    <p class="text-sm text-on-surface-variant">No transcript uploaded.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-outline-variant/20 p-6">
                <h3 class="font-body-lg text-body-lg !text-base font-semibold text-on-surface mb-4">Statement of Intent</h3>
                <p class="text-sm text-on-surface-variant whitespace-pre-line leading-relaxed">{{ $application->personal_statement }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-outline-variant/20 p-6 h-fit">
            <h3 class="font-body-lg text-body-lg !text-base font-semibold text-on-surface mb-4">Review Status</h3>
            <form method="POST" action="{{ route('admin.applications.status', $application) }}">
                @csrf
                @method('PATCH')

                <select
                    name="status"
                    class="w-full rounded-lg border border-outline-variant px-4 py-3 font-body-md text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >
                    @foreach(['pending', 'reviewed', 'accepted', 'rejected'] as $status)
                        <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>

                <x-button type="submit" variant="primary" class="w-full !py-3">Update Status</x-button>
            </form>

            <p class="text-xs text-on-surface-variant mt-4">
                Submitted {{ $application->created_at->format('M j, Y \a\t g:i A') }}
            </p>
        </div>

    </div>

</x-layouts.admin>