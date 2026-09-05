@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-plus-circle text-cyan-500"></i> Add New Product
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Physical floor matting, trunk liners, or accessories</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-300 dark:hover:bg-dark-700 transition-colors">
            <i class="fas fa-arrow-left mr-1.5"></i> Back to Inventory
        </a>
    </div>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6 text-sm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- SKU / Product Code -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Product Code / SKU <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="product_code" value="{{ old('product_code') }}" required placeholder="e.g. MAT-TY-FORT-01"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Product Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Product Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Toyota Fortuner 3-Row Deep Dish Matting"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Category -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Category <span class="text-rose-500">*</span>
                </label>
                <select name="category_id" required class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Vehicle Brand -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Vehicle Brand</label>
                <input type="text" name="vehicle_brand" value="{{ old('vehicle_brand', 'Toyota') }}" placeholder="e.g. Toyota, Mitsubishi, Ford, Universal"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Vehicle Model / Year -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Compatible Model / Year</label>
                <input type="text" name="vehicle_model" value="{{ old('vehicle_model') }}" placeholder="e.g. Fortuner 2016-2024"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Material Type -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Material Type</label>
                <input type="text" name="material_type" value="{{ old('material_type') }}" placeholder="e.g. TPE Deep Dish, 5D Leatherette, PVC Coil"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Unit of Measure -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Unit of Measure <span class="text-rose-500">*</span>
                </label>
                <select name="unit_of_measure" required class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
                    <option value="Set" selected>Set (Front + Rear Cabin)</option>
                    <option value="Pc">Pc (Single Piece / Mat)</option>
                    <option value="Roll">Roll (Coil / Noodle Matting)</option>
                    <option value="Pair">Pair (Front Mats Only)</option>
                </select>
            </div>

            <!-- Stock Alert Level -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Low Stock Threshold <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="stock_alert_level" value="{{ old('stock_alert_level', 4) }}" required min="0"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Cost Price -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Supplier Cost (₱) <span class="text-rose-500">*</span>
                </label>
                <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" required placeholder="0.00"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Retail Selling Price -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Retail Selling Price (₱) <span class="text-rose-500">*</span>
                </label>
                <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" required placeholder="0.00"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-sm focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Initial Stock on Hand -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Initial Stock on Hand
                </label>
                <input type="number" step="0.01" name="initial_stock" value="{{ old('initial_stock', 0) }}" min="0"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-sm focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <!-- Multi-Image Upload (Up to 5 images with cumulative selection) -->
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-700 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        <i class="fas fa-images text-cyan-500 mr-1.5"></i> Product Photos (Up to 5 Images)
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Upload photos together or one-by-one. Drag &amp; drop supported.
                    </p>
                </div>
                <div class="text-xs font-bold px-3 py-1 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 self-start sm:self-auto">
                    <span id="stagedCountDisplay">0</span> of 5 photos selected
                </div>
            </div>

            <!-- Dropzone Area -->
            <div id="createDropzone" onclick="document.getElementById('imgUploadInput').click()"
                class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-cyan-500 dark:hover:border-cyan-400 bg-white dark:bg-dark-850 rounded-2xl p-6 text-center cursor-pointer transition-all group">
                <div class="flex flex-col items-center justify-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 group-hover:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl transition-colors">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <div class="text-sm font-bold text-slate-800 dark:text-slate-200">
                        Click to browse or drag &amp; drop photos here
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm">
                        Select multiple photos at once or add photos one-by-one (JPG, PNG, WEBP up to 5MB each)
                    </p>
                </div>

                <!-- Hidden Native Input connected via DataTransfer -->
                <input type="file" name="images[]" id="imgUploadInput" multiple accept="image/*" class="hidden">
            </div>

            <!-- Staged Images Live Preview Gallery -->
            <div id="stagedGallerySection" class="hidden space-y-2">
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Selected Photos (First photo will be Main Cover):
                </div>
                <div id="imagePreviewsContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5"></div>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Description &amp; Key Features</label>
            <textarea name="description" rows="3" placeholder="Product details, coverage area, thickness, anti-slip features..."
                class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                Cancel
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20 cursor-pointer">
                Create Product
            </button>
        </div>
    </form>
</div>

<script>
// Cumulative DataTransfer to accumulate multi-image selections across multiple file dialogs
const stagedDataTransfer = new DataTransfer();
const maxAllowedImages = 5;

const fileInput = document.getElementById('imgUploadInput');
const dropzone = document.getElementById('createDropzone');
const gallerySection = document.getElementById('stagedGallerySection');
const previewsContainer = document.getElementById('imagePreviewsContainer');
const countDisplay = document.getElementById('stagedCountDisplay');

function handleNewFiles(fileList) {
    let addedAny = false;
    let exceededMax = false;

    for (let i = 0; i < fileList.length; i++) {
        const file = fileList[i];
        if (!file.type.startsWith('image/')) continue;

        if (stagedDataTransfer.items.length < maxAllowedImages) {
            stagedDataTransfer.items.add(file);
            addedAny = true;
        } else {
            exceededMax = true;
        }
    }

    if (exceededMax) {
        alert('Maximum of 5 photos reached. Only the first 5 photos were kept.');
    }

    // Sync input files with accumulated DataTransfer
    fileInput.files = stagedDataTransfer.files;
    renderStagedGallery();
}

function removeStagedFile(index) {
    stagedDataTransfer.items.remove(index);
    fileInput.files = stagedDataTransfer.files;
    renderStagedGallery();
}

function renderStagedGallery() {
    previewsContainer.innerHTML = '';
    const files = stagedDataTransfer.files;
    countDisplay.textContent = files.length;

    if (files.length === 0) {
        gallerySection.classList.add('hidden');
        return;
    }

    gallerySection.classList.remove('hidden');

    Array.from(files).forEach((file, index) => {
        const card = document.createElement('div');
        card.className = 'group relative rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-dark-800 p-2.5 shadow-sm transition-all';

        const sizeKb = (file.size / 1024).toFixed(0);
        const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
        const sizeText = file.size > 1024 * 1024 ? `${sizeMb} MB` : `${sizeKb} KB`;

        const objectUrl = URL.createObjectURL(file);

        card.innerHTML = `
            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-700">
                <img src="${objectUrl}" class="w-full h-full object-cover">
                ${index === 0 ? '<span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-cyan-600 text-white text-[10px] font-bold shadow-sm">Cover</span>' : ''}
                <span class="absolute bottom-1.5 right-1.5 px-1.5 py-0.5 rounded bg-black/70 text-white text-[9px] font-mono">${sizeText}</span>
            </div>
            <div class="mt-2 flex items-center justify-between gap-1">
                <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 truncate max-w-[80px]" title="${file.name}">${file.name}</span>
                <button type="button" onclick="removeStagedFile(${index})" 
                    class="p-1 px-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all cursor-pointer" title="Remove photo">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        previewsContainer.appendChild(card);
    });
}

// File input change listener
fileInput.addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        handleNewFiles(this.files);
    }
});

// Drag & Drop event listeners
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
</script>
@endsection
