@php
$colorClasses = '';
switch ($color) {
    case 'primary':
        $colorClasses = 'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500';
        break;
    case 'warn':
        $colorClasses = 'text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500';
        break;
    case 'alert':
        $colorClasses = 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500';
        break;
}
@endphp

<button type="submit"
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $colorClasses }}">
    {{ $slot }}
</button>
