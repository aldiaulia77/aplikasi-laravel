@props(['paginator'])
@if($paginator->hasPages())
<div class="flex items-center justify-between px-4 py-3 border-t text-xs text-gray-500" style="border-color:var(--line)">
    <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }} · {{ $paginator->total() }} data</span>
    <div class="flex gap-1">
        @if($paginator->onFirstPage())
            <span class="sd-btn-ghost rounded p-1.5 opacity-30">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="sd-btn-ghost rounded p-1.5 px-2">‹</a>
        @endif
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="sd-btn-ghost rounded p-1.5 px-2">›</a>
        @else
            <span class="sd-btn-ghost rounded p-1.5 opacity-30">›</span>
        @endif
    </div>
</div>
@endif
