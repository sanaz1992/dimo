<section>
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">

        <div class="flex flex-col gap-4 md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-[24px]">
                {{__('user::attributes.admins_list')}}
            </h2>
            <div class="flex items-center gap-4">

                <a href="{{route('admin.admins.create')}}"
                    class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                    <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                    <span class="">@lang("user::attributes.create_admin")</span>
                </a>

            </div>
        </div>
        <div class="relative">
            <div class="rounded-xl border border-black/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[800px] w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                @foreach ($columns as $col)
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $col['label'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $key => $user)
                                <tr class="hover:bg-gray-50 {{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}"
                                    wire:key="user-{{ $user->id }}">
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        <img alt="{{$user->name}}" class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $user->avatar?->getThumbnailUrl('small') }}">
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$user->name}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$user->mobile}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        <span
                                            class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{$user->status['color'] }}">
                                            {{$user->status['title'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                        اطلاعاتی
                                        یافت نشد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$users->links('Core::pagination')}}
        </div>
    </div>
</section>
