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

        <!-- Multi-Image Upload (Up to 5 images) -->
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-700 space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    <i class="fas fa-images text-cyan-500 mr-1.5"></i> Product Photos (Up to 5 Images)
                </label>
                <span class="text-xs text-slate-400">Max 5MB each &bull; JPG, PNG, WEBP &bull; 800x800px recommended</span>
            </div>

            <input type="file" name="images[]" id="imgUploadInput" multiple accept="image/*" onchange="previewSelectedImages(this)"
                class="w-full py-2 px-3 bg-white dark:bg-dark-850 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-500 file:text-white hover:file:bg-cyan-600">

            <!-- Live Previews Container -->
            <div id="imagePreviewsContainer" class="flex flex-wrap gap-3 pt-3"></div>
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
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20">
                Create Product
            </button>
        </div>
    </form>
</div>

<script>
function previewSelectedImages(input) {
    const container = document.getElementById('imagePreviewsContainer');
    container.innerHTML = '';

    if (input.files) {
        const filesArray = Array.from(input.files).slice(0, 5);
        if (input.files.length > 5) {
            alert('A maximum of 5 images can be uploaded per product. Only the first 5 images will be used.');
        }

        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative w-20 h-20 rounded-xl border border-slate-300 dark:border-slate-700 overflow-hidden shadow-sm';
                preview.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">#${index + 1}</span>
                `;
                container.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endsection
