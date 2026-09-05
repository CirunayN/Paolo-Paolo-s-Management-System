@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-pen-to-square text-cyan-500"></i> Edit Product: {{ $product->name }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Update SKU, specs, pricing, and product gallery</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-300 dark:hover:bg-dark-700 transition-colors">
            <i class="fas fa-arrow-left mr-1.5"></i> Back to Inventory
        </a>
    </div>

    @if ($errors->any())
    <div class="glass-card rounded-2xl p-4 border border-rose-300 dark:border-rose-800 bg-rose-50/70 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 text-sm space-y-1">
        <div class="font-bold flex items-center gap-2">
            <i class="fas fa-triangle-exclamation text-rose-500"></i>
            <span>Please check the following errors:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-0.5 ml-4">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6 text-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Product Code / SKU <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="product_code" value="{{ old('product_code', $product->product_code) }}" required
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Product Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Category <span class="text-rose-500">*</span>
                </label>
                <select name="category_id" required class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Vehicle Brand</label>
                <input type="text" name="vehicle_brand" value="{{ old('vehicle_brand', $product->vehicle_brand) }}"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Compatible Model</label>
                <input type="text" name="vehicle_model" value="{{ old('vehicle_model', $product->vehicle_model) }}"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Material Type</label>
                <input type="text" name="material_type" value="{{ old('material_type', $product->material_type) }}"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Unit of Measure</label>
                <input type="text" name="unit_of_measure" value="{{ old('unit_of_measure', $product->unit_of_measure) }}" required
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Low Stock Alert Level</label>
                <input type="number" name="stock_alert_level" value="{{ old('stock_alert_level', $product->stock_alert_level) }}" required min="0"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Supplier Cost (₱)</label>
                <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Retail Price (₱)</label>
                <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <!-- Current Images & Multi-Image Gallery Manager -->
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-700 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        <i class="fas fa-images text-cyan-500 mr-1.5"></i> Product Gallery Photos
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Manage photos (Max 5 photos). Click "Remove" to remove unwanted photos with confirmation.
                    </p>
                </div>
                <div class="text-xs font-bold px-3 py-1 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 self-start sm:self-auto">
                    <span id="activePhotoCount">0</span> of 5 slots used
                </div>
            </div>

            @php
                $existingImages = is_array($product->images) && count($product->images) > 0 
                    ? $product->images 
                    : ($product->image_path ? [$product->image_path] : []);
            @endphp

            @if(count($existingImages) > 0)
            <div>
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Current Photos on File (Click Remove to unlink):
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5" id="existingImagesGrid">
                    @foreach($existingImages as $rawImg)
                    <div id="imageCard{{ $loop->index }}" class="image-card group relative rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-dark-800 p-2.5 shadow-sm transition-all">
                        <!-- Thumbnail Box -->
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-700">
                            <img src="{{ asset($rawImg) }}" alt="Photo {{ $loop->iteration }}" class="w-full h-full object-cover">

                            @if($loop->first)
                            <span class="cover-tag absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-cyan-600 text-white text-[10px] font-bold shadow-sm z-10">
                                Cover Photo
                            </span>
                            @endif

                            <!-- Marked for Deletion Overlay (Hidden initially) -->
                            <div id="removalOverlay{{ $loop->index }}" class="hidden absolute inset-0 bg-rose-950/90 backdrop-blur-[2px] flex flex-col items-center justify-center text-center p-2 z-20 transition-opacity">
                                <i class="fas fa-trash-can text-rose-400 text-2xl mb-1 animate-pulse"></i>
                                <span class="text-[11px] font-black text-white leading-tight">Will be removed on save</span>
                                <span class="text-[9px] text-rose-300 mt-1 font-semibold">Click Undo to keep</span>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-2.5">
                            <!-- Normal Remove Trigger (Opens Confirmation Modal) -->
                            <button type="button" id="btnRemove{{ $loop->index }}"
                                onclick="openRemoveModal({{ $loop->index }}, '{{ addslashes($rawImg) }}', '{{ asset($rawImg) }}')"
                                class="w-full py-1.5 px-2 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fas fa-trash-alt text-[11px]"></i>
                                <span>Remove</span>
                            </button>

                            <!-- Undo Button (Shown only when marked) -->
                            <button type="button" id="btnUndo{{ $loop->index }}"
                                onclick="undoRemoval({{ $loop->index }})"
                                class="hidden w-full py-1.5 px-2 rounded-lg bg-cyan-50 hover:bg-cyan-100 dark:bg-cyan-950/40 dark:hover:bg-cyan-900/60 text-cyan-700 dark:text-cyan-300 border border-cyan-300 dark:border-cyan-700 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fas fa-rotate-left text-[11px]"></i>
                                <span>Undo</span>
                            </button>
                        </div>

                        <!-- Container for dynamic hidden input -->
                        <div id="hiddenInputContainer{{ $loop->index }}"></div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Add New Photos Input with Cumulative Multi-Image Support -->
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Upload Additional Photos (Add together or one-by-one):
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Drag &amp; drop or browse photos. You can add photos one at a time or select multiple at once.
                    </p>
                </div>

                <!-- Dropzone Area for Additional Photos -->
                <div id="editDropzone" onclick="document.getElementById('newImagesInput').click()"
                    class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-cyan-500 dark:hover:border-cyan-400 bg-white dark:bg-dark-850 rounded-2xl p-5 text-center cursor-pointer transition-all group">
                    <div class="flex flex-col items-center justify-center space-y-1.5">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 group-hover:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-lg transition-colors">
                            <i class="fas fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                            Click to browse or drag &amp; drop additional photos
                        </div>
                        <p class="text-[11px] text-slate-400">
                            JPG, PNG, WEBP up to 5MB each &bull; Accumulates selections automatically
                        </p>
                    </div>

                    <!-- Hidden Native Input connected via DataTransfer -->
                    <input type="file" name="images[]" id="newImagesInput" multiple accept="image/*" class="hidden">
                </div>

                <!-- Newly Staged Additional Photos Preview Gallery -->
                <div id="newStagedGallerySection" class="hidden space-y-2">
                    <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-circle-check"></i>
                        <span>New Photos to be Added on Save:</span>
                    </div>
                    <div id="newStagedPreviewsContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5"></div>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="flex items-center">
            <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}
                    class="w-4 h-4 rounded bg-slate-100 dark:bg-dark-900 border-slate-300 dark:border-slate-700 text-cyan-500">
                <span>Active Product (Visible on POS and Inventory)</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                Cancel
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20 cursor-pointer">
                Update Product
            </button>
        </div>
    </form>
</div>

<!-- Safety Confirmation Modal to Prevent Human Error -->
<div id="removeConfirmModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 animate-scale-up">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-rose-500/15 text-rose-500 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="text-base font-bold font-display text-slate-900 dark:text-white">Confirm Photo Removal</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">Avoid accidental loss of product photos</p>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-700 flex items-center gap-4">
            <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-200 dark:bg-dark-800 flex-shrink-0 border border-slate-300 dark:border-slate-700">
                <img id="modalImgPreview" src="" alt="To remove" class="w-full h-full object-cover">
            </div>
            <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                <p class="font-bold text-slate-900 dark:text-white">Remove this image from gallery?</p>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed text-[11px]">
                    This photo will be removed from <strong class="text-slate-700 dark:text-slate-200">{{ $product->name }}</strong> when you submit this form. You can still undo before saving.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" onclick="closeRemoveModal()" 
                class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-dark-700 dark:hover:bg-dark-600 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer">
                <i class="fas fa-times mr-1"></i> Cancel / Keep Photo
            </button>
            <button type="button" id="modalConfirmBtn" 
                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-trash-alt"></i>
                <span>Yes, Remove Photo</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let targetIndex = null;
let targetPath = null;
const totalInitialImages = {{ count($existingImages) }};
let removedCount = 0;

// DataTransfer accumulator for new photos in edit mode
const newStagedDataTransfer = new DataTransfer();
const fileInput = document.getElementById('newImagesInput');
const dropzone = document.getElementById('editDropzone');
const newStagedSection = document.getElementById('newStagedGallerySection');
const newStagedPreviews = document.getElementById('newStagedPreviewsContainer');

function updateSlotCounter() {
    const activeExisting = Math.max(0, totalInitialImages - removedCount);
    const newStaged = newStagedDataTransfer.items.length;
    const totalUsed = activeExisting + newStaged;
    
    const counterEl = document.getElementById('activePhotoCount');
    if (counterEl) {
        counterEl.textContent = totalUsed;
    }
}

function openRemoveModal(index, rawPath, assetUrl) {
    targetIndex = index;
    targetPath = rawPath;
    document.getElementById('modalImgPreview').src = assetUrl;
    document.getElementById('removeConfirmModal').classList.remove('hidden');
}

function closeRemoveModal() {
    targetIndex = null;
    targetPath = null;
    document.getElementById('removeConfirmModal').classList.add('hidden');
}

document.getElementById('modalConfirmBtn').addEventListener('click', () => {
    if (targetIndex === null || targetPath === null) return;

    // 1. Show removal overlay
    document.getElementById('removalOverlay' + targetIndex).classList.remove('hidden');
    // 2. Toggle buttons
    document.getElementById('btnRemove' + targetIndex).classList.add('hidden');
    document.getElementById('btnUndo' + targetIndex).classList.remove('hidden');
    // 3. Inject hidden input
    const container = document.getElementById('hiddenInputContainer' + targetIndex);
    container.innerHTML = `<input type="hidden" name="remove_images[]" value="${targetPath}">`;
    // 4. Highlight card border
    const card = document.getElementById('imageCard' + targetIndex);
    card.classList.add('border-rose-400', 'dark:border-rose-700', 'ring-2', 'ring-rose-500/20');
    // 5. Update slot count
    removedCount++;
    updateSlotCounter();

    closeRemoveModal();
});

function undoRemoval(index) {
    // 1. Hide removal overlay
    document.getElementById('removalOverlay' + index).classList.add('hidden');
    // 2. Toggle buttons
    document.getElementById('btnRemove' + index).classList.remove('hidden');
    document.getElementById('btnUndo' + index).classList.add('hidden');
    // 3. Clear hidden input
    document.getElementById('hiddenInputContainer' + index).innerHTML = '';
    // 4. Reset card border
    const card = document.getElementById('imageCard' + index);
    card.classList.remove('border-rose-400', 'dark:border-rose-700', 'ring-2', 'ring-rose-500/20');
    // 5. Update slot count
    removedCount--;
    updateSlotCounter();
}

// Handle adding new files cumulatively (one-by-one or multiple at once)
function handleNewFiles(fileList) {
    const activeExisting = Math.max(0, totalInitialImages - removedCount);
    let exceededMax = false;

    for (let i = 0; i < fileList.length; i++) {
        const file = fileList[i];
        if (!file.type.startsWith('image/')) continue;

        const currentTotal = activeExisting + newStagedDataTransfer.items.length;
        if (currentTotal < 5) {
            newStagedDataTransfer.items.add(file);
        } else {
            exceededMax = true;
        }
    }

    if (exceededMax) {
        alert('Maximum of 5 photos reached across existing and newly added photos.');
    }

    fileInput.files = newStagedDataTransfer.files;
    renderNewStagedGallery();
    updateSlotCounter();
}

function removeNewStagedFile(index) {
    newStagedDataTransfer.items.remove(index);
    fileInput.files = newStagedDataTransfer.files;
    renderNewStagedGallery();
    updateSlotCounter();
}

function renderNewStagedGallery() {
    newStagedPreviews.innerHTML = '';
    const files = newStagedDataTransfer.files;

    if (files.length === 0) {
        newStagedSection.classList.add('hidden');
        return;
    }

    newStagedSection.classList.remove('hidden');

    Array.from(files).forEach((file, index) => {
        const card = document.createElement('div');
        card.className = 'group relative rounded-2xl border border-emerald-300 dark:border-emerald-700/60 bg-white dark:bg-dark-800 p-2.5 shadow-sm transition-all';

        const sizeKb = (file.size / 1024).toFixed(0);
        const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
        const sizeText = file.size > 1024 * 1024 ? `${sizeMb} MB` : `${sizeKb} KB`;
        const objectUrl = URL.createObjectURL(file);

        card.innerHTML = `
            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-700">
                <img src="${objectUrl}" class="w-full h-full object-cover">
                <span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[10px] font-bold shadow-sm">New</span>
                <span class="absolute bottom-1.5 right-1.5 px-1.5 py-0.5 rounded bg-black/70 text-white text-[9px] font-mono">${sizeText}</span>
            </div>
            <div class="mt-2 flex items-center justify-between gap-1">
                <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 truncate max-w-[80px]" title="${file.name}">${file.name}</span>
                <button type="button" onclick="removeNewStagedFile(${index})" 
                    class="p-1 px-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all cursor-pointer" title="Remove photo">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        newStagedPreviews.appendChild(card);
    });
}

// File input change listener
if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            handleNewFiles(this.files);
        }
    });
}

// Drag & Drop listeners for edit dropzone
if (dropzone) {
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('border-cyan-500', 'bg-cyan-500/5');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-cyan-500', 'bg-cyan-500/5');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            handleNewFiles(dt.files);
        }
    }, false);
}

// Close modal on escape key or backdrop click
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeRemoveModal();
});

document.getElementById('removeConfirmModal').addEventListener('click', (e) => {
    if (e.target.id === 'removeConfirmModal') closeRemoveModal();
});

document.addEventListener('DOMContentLoaded', () => {
    updateSlotCounter();
});
</script>
@endpush
