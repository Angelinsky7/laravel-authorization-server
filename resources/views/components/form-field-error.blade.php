@if ($errors->has($field))
    @foreach ($errors->get($field) as $message)
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @endforeach
@endif
