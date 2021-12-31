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

                    <hr class="my-2" />

                    <table>
                        <thead>
                            <tr>
                                <td>Id </td>
                                <td>Name </td>
                                <td>Label </td>
                                <td class="w-full">Description </td>
                                <td class="text-center">System </td>
                                <td class="text-center">Actions </td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr border="none">
                                    <td class="text-center">{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->label }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>
                                        <div class="flex flex-row justify-center">
                                            <x-policy-bool-tick value="{{ $item->system }}"></x-policy-bool-tick>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex flex-row">
                                            <form action="{{ route('policy.role.destroy', $item->id) }}"
                                                  method="post">
                                                @csrf
                                                @method('DELETE')
                                                <x-policy-button-raised color="red" caption="Delete">
                                                </x-policy-button-raised>
                                            </form>
                                            <form action="{{ route('policy.role.edit', $item->id) }}" method="GET">
                                                @csrf
                                                <x-policy-button-stroked color="blue" caption="Edit">
                                                </x-policy-button-stroked>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
