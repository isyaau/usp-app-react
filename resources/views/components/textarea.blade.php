<div class="mb-3">
    @if($label)
    <label class="form-label">{{ $label }}</label>
    @endif
    <textarea {{ $attributes->merge(['class' => 'form-control']) }}>{{ $slot ?? '' }}</textarea>
    @error($attributes->get('wire:model'))
    <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>