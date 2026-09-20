<!-- Section: Foto Dokumentasi (Read Only) -->
@if ($survey->photos->isNotEmpty())
<x-card title="Foto Dokumentasi" :subtitle="$survey->photos->count() . ' foto dokumentasi lokasi survey'">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach ($survey->photos as $photo)
            <div class="group relative rounded-lg overflow-hidden border border-slate-200 bg-white shadow-xs">
                <a href="{{ $photo->url }}" target="_blank" class="block">
                    <img src="{{ $photo->url }}" alt="{{ $photo->original_name }}" class="w-full h-32 object-cover hover:opacity-90 transition" loading="lazy" />
                </a>
                <div class="px-2.5 py-2">
                    <p class="text-[11px] text-slate-700 font-medium truncate">{{ $photo->original_name }}</p>
                    <p class="text-[10px] text-slate-400">{{ $photo->formatted_size }} &bull; {{ $photo->created_at?->format('d/m/Y H:i') }}</p>
                    @if ($photo->user)
                        <p class="text-[10px] text-slate-400">Oleh: {{ $photo->user->name }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-card>
@endif
