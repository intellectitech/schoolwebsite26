@props(['action'])

<form
    method="POST"
    action="{{ $action }}"
    x-data
    @submit="if (! confirm('Are you sure? This cannot be undone.')) $event.preventDefault()"
>
    @csrf
    @method('DELETE')
    <button type="submit" class="text-error hover:text-error/70 transition-colors" title="Delete">
        <x-icon name="delete" class="text-xl" />
    </button>
</form>