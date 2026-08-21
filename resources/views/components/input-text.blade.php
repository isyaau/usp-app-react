@props(['label' => '', 'model' => '', 'type' => 'text', 'placeholder' => ''])

<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <input
        type="{{ $type }}"
        wire:model.debounce.500ms="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="form-control @error($model) is-invalid @enderror">
    @error($model)
    <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>