@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-layer-group text-cyan-500"></i> Products &amp; Matting Catalog
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Physical floor matting, trunk trays, and automotive accessories</p>
        </div>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Add New Product</span>
        </a>
        @endif
    </div>

    <!-- Filters & Search -->
    <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search SKU, matting model, vehicle..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div>
                <select name="category_id" onchange="this.form.submit()"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="vehicle_brand" onchange="this.form.submit()"
                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Vehicle Brands</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand }}" {{ request('vehicle_brand') == $brand ? 'selected' : '' }}>
                        {{ $brand }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" data-auto-animate>
        @forelse($products as $p)
        @php
            $images = $p->all_images;
        @endphp
        <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:border-cyan-500/40 transition-all flex flex-col justify-between group">
            <div>
                <!-- Image Container with Click-to-Gallery and Multi-Image Counter -->
                <div class="relative bg-slate-100 dark:bg-dark-850 p-4 flex items-center justify-center h-48 cursor-pointer overflow-hidden"
                    onclick="openImageGallery({{ json_encode($images) }}, '{{ addslashes($p->name) }}')">
                    <img id="card-img-{{ $p->id }}" src="{{ $images[0] }}" alt="{{ $p->name }}"
                        class="max-h-40 max-w-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">

                    <!-- Multi-Image Badge -->
                    @if(count($images) > 1)
                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-bold bg-black/70 text-white backdrop-blur-md flex items-center gap-1.5 shadow-md">
                        <i class="fas fa-images text-cyan-400"></i> {{ count($images) }}
                    </span>
                    @endif

                    <!-- Stock Status Badge -->
                    <span class="absolute bottom-3 left-3 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $p->stock_status === 'in_stock' ? 'bg-emerald-500/90 text-white' : ($p->stock_status === 'low_stock' ? 'bg-amber-500/90 text-white' : 'bg-rose-500/90 text-white') }}">
                        {{ str_replace('_', ' ', $p->stock_status) }}
                    </span>
                </div>

                <!-- Multiple Thumbnail Selector Strip (Switch image on click) -->
                @if(count($images) > 1)
                <div class="px-4 py-2 bg-slate-50 dark:bg-dark-900 border-y border-slate-200 dark:border-slate-800 flex items-center gap-2 overflow-x-auto">
                    @foreach($images as $idx => $imgUrl)
                    <button type="button" onclick="switchCardImage({{ $p->id }}, '{{ $imgUrl }}', event)"
                        class="w-8 h-8 rounded-lg border border-slate-300 dark:border-slate-700 overflow-hidden hover:border-cyan-500 transition-colors flex-shrink-0">
                        <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif

                <!-- Product Details -->
                <div class="p-5">
                    <div class="flex items-center justify-between text-xs font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1">
                        <span>{{ $p->product_code }}</span>
                        <span class="text-cyan-600 dark:text-cyan-400 font-bold">{{ $p->vehicle_brand }}</span>
                    </div>

                    <h3 class="font-bold text-slate-900 dark:text-white text-base leading-snug line-clamp-2 hover:text-cyan-500 transition-colors">
                        {{ $p->name }}
                    </h3>

                    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span class="truncate">{{ $p->category->name }}</span>
                        <span>&bull;</span>
                        <span class="font-medium">{{ $p->material_type ?: 'Custom Matting' }}</span>
                    </div>

                    <!-- Stock Counter -->
                    <div class="mt-3 flex items-center justify-between text-xs p-2.5 rounded-xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-800">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Stock on Hand:</span>
                        <span class="font-extrabold text-sm {{ $p->stock_status === 'out_of_stock' ? 'text-rose-500' : 'text-slate-900 dark:text-white' }}">
                            {{ $p->stock_quantity }} {{ $p->unit_of_measure }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Price and Admin Actions -->
            <div class="p-5 pt-0">
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="text-[11px] text-slate-400 uppercase font-semibold">Retail Price</div>
                        <div class="text-xl font-black font-display text-cyan-600 dark:text-cyan-400">
                            ₱ {{ number_format($p->unit_price, 2) }}
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('products.edit', $p->id) }}" class="p-2 rounded-xl bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-600 dark:text-slate-300 hover:text-cyan-500 transition-colors" title="Edit Product">
                            <i class="fas fa-pen-to-square"></i>
                        </a>
                        <form method="POST" action="{{ route('products.destroy', $p->id) }}" class="inline" onsubmit="return confirm('Permanently delete {{ addslashes($p->name) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-slate-100 dark:bg-dark-800 hover:bg-rose-100 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete Product">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 glass-card rounded-2xl text-center text-slate-500">
            <i class="fas fa-box-open text-4xl mb-3 text-slate-400 dark:text-slate-600 block"></i>
            <p class="font-bold text-base text-slate-700 dark:text-slate-300">No physical products found</p>
            <p class="text-xs text-slate-500 mt-1">Try resetting your filter parameters or search terms.</p>
        </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $products->links() }}
    </div>
</div>

<!-- MODAL: Multi-Image Switcher / Gallery (Up to 5 images per product) -->
<div id="galleryModal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 id="galleryTitle" class="text-base font-bold font-display text-slate-900 dark:text-white truncate pr-4"></h3>
            <button type="button" onclick="closeImageGallery()" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl flex items-center justify-center">&times;</button>
        </div>

        <!-- Main Display Photo -->
        <div class="relative bg-slate-100 dark:bg-dark-900 rounded-2xl h-80 flex items-center justify-center p-4 overflow-hidden">
            <img id="galleryMainImg" src="" class="max-h-full max-w-full object-contain drop-shadow-xl transition-all duration-300">

            <!-- Prev / Next Nav Buttons -->
            <button type="button" onclick="prevGalleryImage()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/60 hover:bg-black/80 text-white flex items-center justify-center shadow-lg transition-transform hover:scale-110">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" onclick="nextGalleryImage()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/60 hover:bg-black/80 text-white flex items-center justify-center shadow-lg transition-transform hover:scale-110">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Thumbnails Strip (Up to 5 images) -->
        <div class="flex items-center justify-center gap-3 pt-2" id="galleryThumbnailsStrip">
        </div>
    </div>
</div>

<script>
let currentGalleryImages = [];
let currentGalleryIndex = 0;

function switchCardImage(prodId, imgUrl, event) {
    event.stopPropagation();
    const imgEl = document.getElementById('card-img-' + prodId);
    if (imgEl) imgEl.src = imgUrl;
}

function openImageGallery(images, title) {
    currentGalleryImages = images;
    currentGalleryIndex = 0;
    document.getElementById('galleryTitle').innerText = title;
    updateGalleryDisplay();

    const modal = document.getElementById('galleryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageGallery() {
    const modal = document.getElementById('galleryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updateGalleryDisplay() {
    if (!currentGalleryImages || currentGalleryImages.length === 0) return;
    document.getElementById('galleryMainImg').src = currentGalleryImages[currentGalleryIndex];

    const strip = document.getElementById('galleryThumbnailsStrip');
    strip.innerHTML = '';

    currentGalleryImages.forEach((url, i) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `w-14 h-14 rounded-xl border-2 overflow-hidden transition-all ${i === currentGalleryIndex ? 'border-cyan-500 scale-105 shadow-md shadow-cyan-500/20' : 'border-slate-300 dark:border-slate-700 opacity-60 hover:opacity-100'}`;
        btn.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
        btn.onclick = () => {
            currentGalleryIndex = i;
            updateGalleryDisplay();
        };
        strip.appendChild(btn);
    });
}

function prevGalleryImage() {
    if (currentGalleryIndex > 0) {
        currentGalleryIndex--;
    } else {
        currentGalleryIndex = currentGalleryImages.length - 1;
    }
    updateGalleryDisplay();
}

function nextGalleryImage() {
    if (currentGalleryIndex < currentGalleryImages.length - 1) {
        currentGalleryIndex++;
    } else {
        currentGalleryIndex = 0;
    }
    updateGalleryDisplay();
}
</script>
@endsection
