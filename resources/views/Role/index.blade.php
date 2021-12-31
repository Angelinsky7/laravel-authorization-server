<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-row justify-end">
                        <form action="{{ route('policy.role.create') }}" method="GET">
                            @csrf
                            <x-policy-button-stroked color="blue" caption="Create new Role"></x-policy-button-stroked>
                        </form>
                    </div>

                    <div class="my-2"></div>

                    <x-policy-table>
                        <x-slot name="header">
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="relative px-6 py-3 w-1">
                                <span class="sr-only">Actions</span>
                            </th>
                        </x-slot>
                        <x-slot name="rows">
                            @foreach ($items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $item->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->label }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($item->system)
                                            <span
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                System
                                            </span>
                                        @else
                                            <span
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if (!$item->system)
                                            <x-policy-dropdown>¨
                                                <x-slot name="trigger">
                                                    <x-policy-button-dot></x-policy-button-dot>
                                                </x-slot>
                                                <x-slot name="content">
                                                    <x-dropdown-link :href="route('policy.role.edit', ['role' => $item->id])">
                                                        {{ __('Edit') }}
                                                    </x-dropdown-link>
                                                    <x-dropdown-link :href="route('policy.role.delete', ['role' => $item->id])">
                                                        {{ __('Delete') }}
                                                    </x-dropdown-link>
                                                </x-slot>
                                            </x-policy-dropdown>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-policy-table>

                </div>
            </div>
        </div>
    </div>

    {{-- <div x-show="open"
         x-ref="popover"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
         style="display: none;"
         @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Edit') }}
            </x-dropdown-link>
            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Delete') }}
            </x-dropdown-link>
        </div>
    </div> --}}

</x-app-layout>
