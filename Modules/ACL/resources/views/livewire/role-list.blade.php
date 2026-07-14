<section>
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">
        <div class="flex flex-col gap-4 md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-[24px]">@lang('acl::attributes.list')</h2>
            <div class="flex items-center gap-4">

                <a href="{{route('admin.roles.create')}}"
                    class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                    <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                    <span class="">@lang("acl::attributes.create")</span>
                </a>

            </div>
        </div>
        <div class="relative">
            <div class="rounded-xl border border-black/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[800px] w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #</th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('acl::attributes.title') }}
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('acl::attributes.permissions') }}
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('acl::attributes.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($roles as $key => $role)
                                <tr class="hover:bg-gray-50 {{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">{{ $role->id }}</td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">{{ $role->title }}</td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{ $role->permissions->pluck('title')->join(', ') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        <a href="{{ route('admin.roles.edit', $role) }}"
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/edit-2.svg') }}"
                                                alt="" class="w-5 h-5" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                                        {{ __('acl::messages.without_permission') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $roles->links('Core::pagination') }}
        </div>
    </div>
</section>