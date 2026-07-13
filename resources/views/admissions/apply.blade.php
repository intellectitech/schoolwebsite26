<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply to Starlight Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background font-body-md text-on-surface antialiased">

    {{-- ============ SLIM TOP BAR ============ --}}
    <header class="bg-surface sticky top-0 z-50 border-b-2 border-secondary-container shadow-sm">
        <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop h-16 max-w-container mx-auto">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <x-icon name="school" class="text-primary" />
                <h1 class="font-headline-md text-headline-md text-primary tracking-tight">Starlight Academy</h1>
            </a>
            <div class="hidden md:flex gap-6 items-center">
                <span class="font-label-md text-label-md text-on-surface-variant">Application Portal</span>
                <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center">
                    <x-icon name="person" class="text-on-secondary-container !text-base" />
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen pt-2 pb-32" x-data="applicationForm()" x-init="loadDraft()">
        <div class="max-w-3xl mx-auto px-margin-mobile md:px-0">

            {{-- ============ HERO ============ --}}
            <section class="py-12 text-center">
                <h2 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-4">
                    Join Our Legacy
                </h2>
                <div class="w-16 h-1 bg-secondary-container mx-auto mb-6"></div>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mx-auto">
                    Begin your journey toward academic excellence. Complete the form below to apply for the upcoming academic year.
                </p>
            </section>

            {{-- ============ FLASH MESSAGE ============ --}}
            @if(session('success'))
                <div class="mb-8 flex items-center gap-3 bg-secondary-fixed/20 border border-secondary-fixed/40 text-on-secondary-fixed rounded-lg px-4 py-3">
                    <x-icon name="check_circle" class="text-secondary" />
                    <p class="font-body-md text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-8 flex items-start gap-3 bg-error-container border border-error/30 text-on-error-container rounded-lg px-4 py-3">
                    <x-icon name="error" class="text-error flex-shrink-0" />
                    <div>
                        <p class="font-body-md text-sm font-semibold mb-1">Please fix the following before submitting:</p>
                        <ul class="text-sm list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div x-show="draftRestored" x-cloak class="mb-8 rounded-3xl border border-primary/20 bg-primary/10 px-5 py-4 text-sm text-primary">
                <p class="font-semibold">Draft restored</p>
                <p class="mt-1 text-on-surface-variant">Your saved application progress has been restored from your browser. Click “Save Progress” again after making changes.</p>
            </div>

            <div class="md:grid md:grid-cols-[280px_1fr] md:gap-8">
                <aside class="hidden md:block">
                    <div class="sticky top-24 rounded-[32px] bg-surface-container-lowest border border-outline-variant/40 p-6 shadow-sm">
                        <div class="mb-8">
                            <p class="text-xs uppercase tracking-[0.3em] font-semibold text-on-surface-variant">Application progress</p>
                            <div class="mt-4 h-2 rounded-full bg-surface-container-highest overflow-hidden">
                                <div class="h-full bg-primary transition-all" :style="`width: ${((step - 1) / 2) * 100}%`"></div>
                            </div>
                            <p class="mt-3 text-sm text-on-surface-variant">
                                <span class="font-semibold text-primary" x-text="Math.round(((step - 1) / 2) * 100) + '%'">0%</span> complete
                            </p>
                        </div>

                        <nav class="space-y-3">
                            <button
                                type="button"
                                @click="goToStep(1)"
                                class="w-full rounded-3xl border px-4 py-4 text-left transition-all duration-200"
                                :class="step === 1 ? 'border-primary bg-primary/10 text-primary shadow-sm' : 'border-surface-container-highest bg-surface'"
                            >
                                <p class="font-semibold">1. Personal Details</p>
                                <p class="text-sm text-on-surface-variant mt-1">Basic contact and identity information.</p>
                            </button>

                            <button
                                type="button"
                                @click="goToStep(2)"
                                class="w-full rounded-3xl border px-4 py-4 text-left transition-all duration-200"
                                :class="step === 2 ? 'border-primary bg-primary/10 text-primary shadow-sm' : 'border-surface-container-highest bg-surface'"
                            >
                                <p class="font-semibold">2. Academic Records</p>
                                <p class="text-sm text-on-surface-variant mt-1">School, grade, GPA, and documents.</p>
                            </button>

                            <button
                                type="button"
                                @click="goToStep(3)"
                                class="w-full rounded-3xl border px-4 py-4 text-left transition-all duration-200"
                                :class="step === 3 ? 'border-primary bg-primary/10 text-primary shadow-sm' : 'border-surface-container-highest bg-surface'"
                            >
                                <p class="font-semibold">3. Intent</p>
                                <p class="text-sm text-on-surface-variant mt-1">Personal statement and motivation.</p>
                            </button>
                        </nav>

                        <div class="mt-8 pt-6 border-t border-outline-variant/50">
                            <p class="text-sm font-semibold text-on-surface-variant">Need help?</p>
                            <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">Contact admissions at <a href="mailto:admissions@starlight.academy" class="text-primary underline">admissions@starlight.academy</a>.</p>
                        </div>
                    </div>
                </aside>

                <div>
                    <div class="mb-10 md:hidden relative">
                        <div class="flex justify-between items-center relative z-10">
                            <template x-for="s in 3" :key="s">
                                <div class="flex flex-col items-center gap-2">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold border-4 border-surface shadow-md transition-all duration-300"
                                        :class="s < step ? 'bg-primary text-white' : (s === step ? 'bg-primary text-white' : 'bg-surface-container-highest text-on-surface-variant')"
                                    >
                                        <span x-show="s < step" class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check</span>
                                        <span x-show="s >= step" x-text="s"></span>
                                    </div>
                                    <span
                                        class="font-label-md text-label-md transition-colors"
                                        :class="s <= step ? 'text-primary font-bold' : 'text-on-surface-variant'"
                                        x-text="['Personal', 'Academic', 'Intent'][s - 1]"
                                    ></span>
                                </div>
                            </template>
                        </div>
                        <div class="absolute top-5 left-0 w-full h-[2px] bg-surface-container-highest -z-0">
                            <div
                                class="h-full bg-primary transition-all duration-500"
                                :style="`width: ${((step - 1) / 2) * 100}%`"
                            ></div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest rounded-[32px] p-6 md:p-8 border border-outline-variant/30 shadow-sm">
                        <form method="POST" action="{{ route('apply.store') }}" enctype="multipart/form-data" class="space-y-8" @submit="submitting = true">
                            @csrf

                            {{-- ---------- STEP 1: Personal Information ---------- --}}
                            <div x-show="step === 1" x-cloak class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <x-icon name="person_outline" class="text-secondary" />
                                    <h3 class="font-headline-md text-headline-md text-primary">Personal Information</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-admin.form-field name="full_name" label="Full Legal Name" required placeholder="John Doe" />
                                    <x-admin.form-field name="date_of_birth" label="Date of Birth" type="date" required />
                                    <x-admin.form-field name="email" label="Email Address" type="email" required placeholder="john@example.com" />
                                    <x-admin.form-field name="phone" label="Phone Number" type="tel" required placeholder="+1 (555) 000-0000" />
                                </div>
                            </div>

                            {{-- ---------- STEP 2: Academic History ---------- --}}
                            <div x-show="step === 2" x-cloak class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <x-icon name="history_edu" class="text-secondary" />
                                    <h3 class="font-headline-md text-headline-md text-primary">Academic History</h3>
                                </div>

                                <x-admin.form-field name="current_school" label="Current School" required placeholder="Greenfield Academy" />

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="grade_applying_for" value="Grade Applying For *" />
                                        <select
                                            id="grade_applying_for" name="grade_applying_for"
                                            required
                                            class="w-full px-4 py-3 rounded-lg border border-outline-variant focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none bg-surface-bright appearance-none"
                                        >
                                            <option value="">Select Grade</option>
                                            @foreach(['Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $grade)
                                                <option value="{{ $grade }}" @selected(old('grade_applying_for') === $grade)>{{ $grade }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('grade_applying_for')" class="mt-1" />
                                    </div>

                                    <x-admin.form-field name="current_gpa" label="Current GPA (if applicable)" placeholder="4.0" />
                                </div>

                                <div class="space-y-4 pt-4">
                                    <x-input-label value="Transcripts &amp; Recommendations" />
                                    <div
                                        @click="$refs.transcriptInput.click()"
                                        class="border-2 border-dashed border-outline-variant rounded-3xl p-8 text-center hover:border-secondary transition-colors cursor-pointer group"
                                    >
                                        <x-icon name="cloud_upload" class="text-primary !text-4xl mb-2 group-hover:scale-110 transition-transform inline-block" />
                                        <p class="text-body-md text-sm text-on-surface-variant mb-1" x-text="fileName || 'Click to upload or drag and drop'"></p>
                                        <p class="font-caption text-caption text-outline">PDF, DOCX or JPEG (Max 10MB)</p>
                                        <input
                                            x-ref="transcriptInput"
                                            @change="fileName = $event.target.files[0]?.name ?? ''"
                                            class="hidden" id="transcript" name="transcript" type="file"
                                            accept=".pdf,.docx,.jpg,.jpeg"
                                        >
                                    </div>
                                    <x-input-error :messages="$errors->get('transcript')" class="mt-1" />
                                </div>
                            </div>

                            {{-- ---------- STEP 3: Statement of Intent ---------- --}}
                            <div x-show="step === 3" x-cloak class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <x-icon name="edit_note" class="text-secondary" />
                                    <h3 class="font-headline-md text-headline-md text-primary">Statement of Intent</h3>
                                </div>

                                <div class="p-4 bg-primary-container/5 rounded-lg border-l-4 border-secondary-container">
                                    <p class="font-body-md text-sm italic text-primary">
                                        "Explain how your presence at Starlight Academy will contribute to our community and how we can support your long-term ambitions."
                                    </p>
                                </div>

                                <div>
                                    <x-input-label for="personal_statement" value="Personal Statement *" />
                                    <textarea
                                        id="personal_statement" name="personal_statement" rows="10"
                                        required minlength="50" maxlength="8000"
                                        class="w-full px-4 py-3 rounded-lg border border-outline-variant focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none bg-surface-bright resize-none"
                                        placeholder="Type your personal statement here..."
                                    >{{ old('personal_statement') }}</textarea>
                                    <p class="font-caption text-caption text-right text-outline mt-1">Recommended length: 500-1000 words</p>
                                    <x-input-error :messages="$errors->get('personal_statement')" class="mt-1" />
                                </div>
                            </div>

                            {{-- ---------- NAVIGATION ---------- --}}
                            <div class="flex flex-col gap-3 md:flex-row md:justify-between md:items-center pt-8 border-t border-outline-variant/30">
                                <button
                                type="button" @click="saveDraft()"
                                class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-outline-variant bg-surface text-on-surface transition-all hover:border-primary hover:text-primary"
                            >
                                <x-icon name="save" class="!text-lg" /> Save Progress
                            </button>

                            <button
                                    x-show="step > 1"
                                    type="button"
                                    @click="step--"
                                    class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-label-md text-label-md text-primary hover:bg-surface-container-low transition-colors"
                                >
                                    <x-icon name="arrow_back" class="!text-lg" /> Back
                                </button>
                                <div class="flex-1"></div>

                                <button
                                    type="button" @click="nextStep()"
                                    x-show="step < 3"
                                    class="flex items-center justify-center gap-2 px-8 py-3 bg-primary text-white rounded-lg font-label-md text-label-md shadow-md hover:bg-primary-container transition-all active:scale-95"
                                >
                                    Next Step <x-icon name="arrow_forward" class="!text-lg" />
                                </button>

                                <button
                                    type="submit"
                                    x-show="step === 3"
                                    :disabled="submitting"
                                    class="flex items-center justify-center gap-2 px-8 py-3 bg-secondary text-white rounded-lg font-label-md text-label-md shadow-md hover:opacity-90 transition-all active:scale-95 disabled:opacity-50"
                                >
                                    <span x-show="!submitting">Submit Application</span>
                                    <span x-show="submitting">Processing...</span>
                                    <x-icon name="send" class="!text-lg" x-show="!submitting" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============ FOOTER QUOTE ============ --}}
            <div class="mt-16 text-center pb-20">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-secondary-container/20 text-secondary mb-6">
                    <x-icon name="format_quote" />
                </div>
                <blockquote class="font-display-lg text-headline-md italic text-on-surface mb-4">
                    "Wisdom begins with wonder, and excellence is the result of continuous striving."
                </blockquote>
                <cite class="font-label-md text-label-md uppercase tracking-widest text-secondary not-italic">— The Starlight Charter</cite>
            </div>
        </div>
    </main>

    <x-public.mobile-nav active="admissions" />

    <script>
        function applicationForm() {
            return {
                // If validation failed server-side, land back on step 1 so
                // errors (which could belong to any step) are visible —
                // simplest correct behavior without per-step error routing.
                step: {{ $errors->any() ? 1 : 1 }},
                fileName: '',
                submitting: false,
                draftRestored: false,
                loadDraft() {
                    const raw = localStorage.getItem('applicationDraft');
                    if (!raw) {
                        return;
                    }

                    try {
                        const draft = JSON.parse(raw);
                        if (typeof draft !== 'object' || draft === null) {
                            return;
                        }

                        ['full_name', 'date_of_birth', 'email', 'phone', 'current_school', 'grade_applying_for', 'current_gpa', 'personal_statement'].forEach((key) => {
                            if (draft[key] !== undefined && document.querySelector(`[name="${key}"]`)) {
                                document.querySelector(`[name="${key}"]`).value = draft[key];
                            }
                        });

                        if (draft.fileName) {
                            this.fileName = draft.fileName;
                        }

                        this.draftRestored = true;
                    } catch (error) {
                        localStorage.removeItem('applicationDraft');
                    }
                },
                saveDraft() {
                    const draft = {
                        full_name: document.querySelector('[name="full_name"]').value,
                        date_of_birth: document.querySelector('[name="date_of_birth"]').value,
                        email: document.querySelector('[name="email"]').value,
                        phone: document.querySelector('[name="phone"]').value,
                        current_school: document.querySelector('[name="current_school"]').value,
                        grade_applying_for: document.querySelector('[name="grade_applying_for"]').value,
                        current_gpa: document.querySelector('[name="current_gpa"]').value,
                        personal_statement: document.querySelector('[name="personal_statement"]').value,
                        fileName: this.fileName,
                    };

                    localStorage.setItem('applicationDraft', JSON.stringify(draft));
                    this.draftRestored = true;
                    alert('Application progress saved locally.');
                },
                fieldsForStep(step) {
                    return {
                        1: ['full_name', 'date_of_birth', 'email', 'phone'],
                        2: ['current_school', 'grade_applying_for'],
                        3: ['personal_statement'],
                    }[step] ?? [];
                },
                validateStep() {
                    const invalidField = this.fieldsForStep(this.step)
                        .map((name) => document.querySelector(`[name="${name}"]`))
                        .filter(Boolean)
                        .find((field) => !field.checkValidity());

                    if (!invalidField) {
                        return true;
                    }

                    invalidField.reportValidity();
                    invalidField.focus();

                    return false;
                },
                nextStep() {
                    if (this.step < 3 && this.validateStep()) {
                        this.step++;
                    }
                },
                goToStep(targetStep) {
                    if (targetStep <= this.step) {
                        this.step = targetStep;
                        return;
                    }

                    while (this.step < targetStep) {
                        if (!this.validateStep()) {
                            return;
                        }

                        this.step++;
                    }
                },
            }
        }
    </script>
</body>
</html>
