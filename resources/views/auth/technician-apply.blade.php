<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Apply as a technician</h2>
    <p class="mt-2 text-sm text-slate-500">
        Submit your experience for admin review. You can sign in after applying, but job access starts once approved.
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Already applied? Sign in</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ route('technician.apply.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="ff-field">
            <label for="name" class="ff-label">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="ff-input">
        </div>

        <div class="ff-field">
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="ff-input">
        </div>

        <div class="ff-field">
            <label for="phone" class="ff-label">Phone number</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required class="ff-input" placeholder="+880 1XXX XXXXXX">
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="ff-field">
                <label for="years_experience" class="ff-label">Years of experience</label>
                <input type="number" id="years_experience" name="years_experience" min="0" max="40" value="{{ old('years_experience', '1') }}" required class="ff-input">
            </div>
            <div class="ff-field">
                <label for="certification" class="ff-label">Certification (optional)</label>
                <input type="text" id="certification" name="certification" value="{{ old('certification') }}" class="ff-input" placeholder="e.g. CompTIA A+">
            </div>
        </div>

        <div class="ff-field">
            <label for="specialties" class="ff-label">Specialties</label>
            <input type="text" id="specialties" name="specialties" value="{{ old('specialties') }}" required class="ff-input" placeholder="Smartphones, laptops, soldering, board repair">
        </div>

        <div class="ff-field">
            <label for="motivation" class="ff-label">Why do you want to join FixFlow?</label>
            <textarea id="motivation" name="motivation" rows="4" required class="ff-input" placeholder="Tell us about your repair background and why you are a good fit...">{{ old('motivation') }}</textarea>
        </div>

        <div class="ff-field">
            <label for="document" class="ff-label">Supporting document <span class="font-normal text-slate-500">(CV / certificate)</span></label>
            <div class="ff-upload-zone" id="applicant-document-dropzone">
                <div id="applicant-document-empty" class="w-full text-center">
                    <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <div class="mt-3 text-sm text-slate-600">
                        <label for="document" class="cursor-pointer font-semibold text-indigo-600 hover:text-indigo-500">
                            Upload a document
                        </label>
                        <span> or drag and drop</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">PDF, DOC, DOCX, PNG, JPG up to 5 MB</p>
                </div>
                <div id="applicant-document-preview" class="hidden w-full text-center">
                    <img id="applicant-document-thumb" src="" alt="Selected document preview" class="mx-auto hidden max-h-40 rounded-xl object-contain">
                    <div id="applicant-document-file-icon" class="mx-auto hidden flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <p id="applicant-document-name" class="mt-3 text-sm font-medium text-slate-700"></p>
                    <button type="button" id="applicant-document-clear" class="mt-2 text-sm font-semibold text-rose-600 hover:text-rose-800">Remove document</button>
                </div>
                <input id="document" name="document" type="file" class="sr-only" required accept=".pdf,.doc,.docx,image/png,image/jpeg,image/webp,application/pdf">
            </div>
            @error('document')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-password-input id="password" name="password" label="Password" autocomplete="new-password" />

        <x-password-input id="password_confirmation" name="password_confirmation" label="Confirm password" autocomplete="new-password" />

        <button type="submit" class="ff-btn-primary w-full">Submit application</button>

        <p class="text-center text-sm text-slate-500">
            Need a repair instead?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Create a customer account</a>
        </p>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('document');
            const dropzone = document.getElementById('applicant-document-dropzone');
            const emptyState = document.getElementById('applicant-document-empty');
            const preview = document.getElementById('applicant-document-preview');
            const thumb = document.getElementById('applicant-document-thumb');
            const fileIcon = document.getElementById('applicant-document-file-icon');
            const nameEl = document.getElementById('applicant-document-name');
            const clearBtn = document.getElementById('applicant-document-clear');
            const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'webp'];

            if (!input || !dropzone) {
                return;
            }

            const isAllowed = (file) => {
                const extension = file.name.split('.').pop()?.toLowerCase() ?? '';
                return allowedExtensions.includes(extension);
            };

            const showPreview = (file) => {
                if (!file || !isAllowed(file)) {
                    clearPreview();
                    return;
                }

                nameEl.textContent = file.name;
                emptyState.classList.add('hidden');
                preview.classList.remove('hidden');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        thumb.src = event.target?.result ?? '';
                        thumb.classList.remove('hidden');
                        fileIcon.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                thumb.src = '';
                thumb.classList.add('hidden');
                fileIcon.classList.remove('hidden');
            };

            const clearPreview = () => {
                input.value = '';
                thumb.src = '';
                nameEl.textContent = '';
                preview.classList.add('hidden');
                emptyState.classList.remove('hidden');
                thumb.classList.add('hidden');
                fileIcon.classList.add('hidden');
            };

            input.addEventListener('change', () => {
                const file = input.files?.[0];
                file ? showPreview(file) : clearPreview();
            });

            clearBtn?.addEventListener('click', clearPreview);

            dropzone.addEventListener('dragover', (event) => {
                event.preventDefault();
                dropzone.classList.add('ring-2', 'ring-indigo-300');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('ring-2', 'ring-indigo-300');
            });

            dropzone.addEventListener('drop', (event) => {
                event.preventDefault();
                dropzone.classList.remove('ring-2', 'ring-indigo-300');

                const file = event.dataTransfer?.files?.[0];
                if (!file || !isAllowed(file)) {
                    return;
                }

                const transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;
                showPreview(file);
            });
        });
    </script>
</x-guest-layout>
