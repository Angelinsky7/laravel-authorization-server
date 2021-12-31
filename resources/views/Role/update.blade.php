<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit role') }} {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="mt-10 sm:mt-0">
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form method="POST" action="update">
                                @method('PUT')
                                @csrf
                                <div class="overflow-hidden">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="name"
                                                       class="block text-sm font-medium text-gray-700">
                                                    Name
                                                </label>
                                                <input type="text" name="name" id="name"
                                                       value="{{ old('name') ?? $item->name }}"
                                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                                                <x-policy-form-field-error field="name"></x-policy-form-field-error>
                                            </div>

                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="label"
                                                       class="block text-sm font-medium text-gray-700">
                                                    Label
                                                </label>
                                                <input type="text" name="label" id="label"
                                                       value="{{ old('label') ?? $item->label }}"
                                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm rounded-md  {{ $errors->has('label') ? 'border-red-500' : 'border-gray-300' }}">
                                                <x-policy-form-field-error field="label"></x-policy-form-field-error>
                                            </div>

                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="description"
                                                       class="block text-sm font-medium text-gray-700">
                                                    Description
                                                </label>
                                                <div class="mt-1">
                                                    <textarea id="description" name="description" rows="3"
                                                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                              placeholder="a simple description">{{ old('description') ?? $item->description }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 py-3 text-right sm:px-6">
                                        <a class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-black hover:text-white bg-transparent border-gray-500 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                           href="{{ route('policy.role.index') }}">Cancel</a>
                                        <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
