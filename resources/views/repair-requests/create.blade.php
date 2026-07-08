<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Create Repair Request" description="Submit a new device for repair">
        <x-slot name="actions">
            <x-back-link :href="url('/repair-requests')" label="Back to list" />
        </x-slot>
    </x-page-header>

    <x-dashboard-card>
        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('repair-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="ff-field">
                    <label for="device_type" class="ff-label">Device Type</label>
                    <select id="device_type" name="device_type" class="ff-input">
                        <option value="">Select device type</option>
                        @foreach (['Smartphone', 'Laptop', 'Tablet', 'Desktop', 'Other'] as $type)
                            <option value="{{ $type }}" @selected(old('device_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="ff-field">
                    <label for="priority" class="ff-label">Priority</label>
                    <select id="priority" name="priority" class="ff-input">
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                    </select>
                </div>

                <div class="ff-field">
                    <label for="brand" class="ff-label">Brand</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand') }}" placeholder="e.g. Apple, Samsung, Dell" class="ff-input">
                </div>

                <div class="ff-field">
                    <label for="model" class="ff-label">Model</label>
                    <input type="text" id="model" name="model" value="{{ old('model') }}" placeholder="e.g. iPhone 14 Pro, MacBook Air M2" class="ff-input">
                </div>

                <div class="ff-field sm:col-span-2">
                    <label for="serial_number" class="ff-label">Serial Number</label>
                    <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" placeholder="Device serial number (if available)" class="ff-input">
                </div>

                <div class="ff-field sm:col-span-2">
                    <label for="issue_description" class="ff-label">Issue Description</label>
                    <textarea id="issue_description" name="issue_description" rows="4" placeholder="Describe the problem in detail..." class="ff-input">{{ old('issue_description') }}</textarea>
                </div>

                <div class="ff-field sm:col-span-2">
                    <label for="device_image" class="ff-label">Device Image Upload</label>
                    <div class="ff-upload-zone" id="device-image-dropzone">
                        <div id="device-image-empty" class="w-full text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <div class="mt-3 text-sm text-slate-600">
                                <label for="device_image" class="cursor-pointer font-semibold text-indigo-600 hover:text-indigo-500">
                                    Upload a file
                                </label>
                                <span> or drag and drop</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">PNG, JPG up to 2 MB</p>
                        </div>
                        <div id="device-image-preview" class="hidden w-full text-center">
                            <img id="device-image-thumb" src="" alt="Selected device image preview" class="mx-auto max-h-48 rounded-xl object-contain">
                            <p id="device-image-name" class="mt-3 text-sm font-medium text-slate-700"></p>
                            <button type="button" id="device-image-clear" class="mt-2 text-sm font-semibold text-rose-600 hover:text-rose-800">Remove image</button>
                        </div>
                        <input id="device_image" name="device_image" type="file" class="sr-only" accept="image/*">
                    </div>
                    @error('device_image')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="ff-card-actions">
                <a href="{{ url('/repair-requests') }}" class="ff-btn-secondary">Cancel</a>
                <button type="submit" class="ff-btn-primary">Submit Request</button>
            </div>
        </form>
    </x-dashboard-card>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('device_image');
            const dropzone = document.getElementById('device-image-dropzone');
            const emptyState = document.getElementById('device-image-empty');
            const preview = document.getElementById('device-image-preview');
            const thumb = document.getElementById('device-image-thumb');
            const nameEl = document.getElementById('device-image-name');
            const clearBtn = document.getElementById('device-image-clear');

            if (!input || !dropzone) {
                return;
            }

            const showPreview = (file) => {
                if (!file || !file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    thumb.src = event.target?.result ?? '';
                    nameEl.textContent = file.name;
                    emptyState.classList.add('hidden');
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            };

            const clearPreview = () => {
                input.value = '';
                thumb.src = '';
                nameEl.textContent = '';
                preview.classList.add('hidden');
                emptyState.classList.remove('hidden');
            };

            input.addEventListener('change', () => {
                const file = input.files?.[0];
                if (file) {
                    showPreview(file);
                } else {
                    clearPreview();
                }
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
                if (!file) {
                    return;
                }

                const transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;
                showPreview(file);
            });
        });
    </script>
</x-app-layout>
