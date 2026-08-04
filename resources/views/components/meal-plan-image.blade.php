@props(['plan'])
@php
    $itemPhotos = ($plan->use_item_photos ?? false)
        ? $plan->mealItems->whereNotNull('image_path')->take(4)->values()
        : collect();
@endphp
@if ($itemPhotos->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-0.5 rounded-md overflow-hidden']) }}>
        @foreach ($itemPhotos as $item)
            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover" />
        @endforeach
    </div>
@elseif ($plan->image_path)
    <img src="{{ asset('storage/'.$plan->image_path) }}" alt="{{ $plan->name }}" {{ $attributes->merge(['class' => 'rounded-md object-cover']) }} />
@endif
