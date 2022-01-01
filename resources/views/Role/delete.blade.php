@php
$modal = request()->get('modal');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Delete role') }} {{ $item->name }}
        </h2>
    </x-slot>

    <div class="{{ $modal ? '' : 'py-12' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden {{ $modal ? '' : 'shadow-sm' }} sm:rounded-lg">
                <div class="p-6 bg-white {{ $modal ? '' : 'border-b' }}  border-gray-200">

                    <div class="mt-10 sm:mt-0">
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form method="POST" action="{{ route('policy.role.destroy', ['role' => $item->id]) }}">
                                @method('DELETE')
                                @csrf

                                <div class="flex flex-row">
                                    <div
                                         class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 self-center">
                                        <!-- Heroicon name: outline/exclamation -->
                                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>

                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900"
                                            id="modal-title">
                                            Delete role
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Are you sure you want to delete this role '{{ $item->name }}' ? This
                                                action cannot be undone.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-3 text-right sm:px-6">
                                    <x-policy-button-cancel route="{{ route('policy.role.index') }}">Cancel
                                    </x-policy-button-cancel>
                                    <x-policy-button-submit color="alert">Delete</x-policy-button-submit>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
