@props(['label', 'value', 'sub' => null, 'gold' => false])
<div class="sd-card p-5 flex flex-col gap-3">
    <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-gray-500">{{ $label }}</span>
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $gold ? 'var(--gold-soft)' : 'var(--sage-soft)' }}"></div>
    </div>
    <p class="sd-display text-2xl" style="color:var(--forest-deep)">{{ $value }}</p>
    @if($sub)<span class="sd-mono text-[11px] text-gray-400">{{ $sub }}</span>@endif
</div>
