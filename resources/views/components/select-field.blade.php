<div class="mb-3">
    <label for="{{ $id }}">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        class="form-control @error($name) is-invalid @enderror"
        @if($required) required @endif
        {{ $attributes }}
    >
        {{ $slot }}
    </select>

    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
