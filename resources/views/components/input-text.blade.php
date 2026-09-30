@php
    $inputValue = $type === 'password' ? '' : old($name, $value);
@endphp

<div class="mb-3">
    <label for="{{ $id }}">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        class="form-control @error($name) is-invalid @enderror"
        value="{{ $inputValue }}"
        @if($required) required @endif
        {{ $attributes }}
    >

    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror

    @if(trim((string) $slot) !== '')
        {{ $slot }}
    @endif
</div>
