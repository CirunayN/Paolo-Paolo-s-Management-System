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
            <i class="fas fa-arrow-left mr-1.5"></i> Back to Catalog
        </a>
    </div>

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

        <!-- Current Images & Multi-Image Upload (Up to 5 images) -->
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-700 space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    <i class="fas fa-images text-cyan-500 mr-1.5"></i> Product Photos (Up to 5 Images)
                </label>
                <span class="text-xs text-slate-400">Max 5MB each &bull; JPG, PNG, WEBP</span>
            </div>

            <!-- Current Images Display -->
            @php $current = $product->all_images; @endphp
            @if(count($current) > 0)
            <div class="flex items-center gap-3">
                @foreach($current as $cImg)
                <div class="w-16 h-16 rounded-xl border border-slate-300 dark:border-slate-700 overflow-hidden shadow-sm">
                    <img src="{{ $cImg }}" class="w-full h-full object-cover">
                </div>
                @endforeach
            </div>
            @endif

            <input type="file" name="images[]" multiple accept="image/*"
                class="w-full py-2 px-3 bg-white dark:bg-dark-850 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-500 file:text-white hover:file:bg-cyan-600">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="flex items-center">
            <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300">
                <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}
                    class="w-4 h-4 rounded bg-slate-100 dark:bg-dark-900 border-slate-300 dark:border-slate-700 text-cyan-500">
                <span>Active Product (Visible on POS and Catalog)</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                Cancel
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
