<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('user::attributes.users_list')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\sellers.svg') }}" alt="users" />
        </x-slot:icon>
        <x-dashboard::buttons.primary-action id="btn-add-user" tag="a" class="btn-fill btn-new-tx shrink-0"
            href="{{ route('admin.users.create') }}">
            <x-slot:icon>
                <img src="{{ asset('icons/header/add.svg') }}" alt="users" />
            </x-slot:icon>
            @lang('user::attributes.create_user')
        </x-dashboard::buttons.primary-action>
    </x-dashboard::card.card-header>

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('user::attributes.unique_code')</th>
                    <th>@lang('user::attributes.name')</th>
                    <th>@lang('user::attributes.mobile')</th>
                    <th>@lang('user::attributes.level')</th>
                    <th>@lang('user::attributes.created_at')</th>
                    <th>@lang('user::attributes.status')</th>
                    <th>@lang('user::attributes.last_login_at')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($users as $user)
                    <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{ toPersianNumber($loop->index + 1)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.unique_code')">
                            {{toPersianNumber($user->unique_code)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.name')" class="flex items-center gap-2">
                            <img alt="{{$user->name}}" class="h-10 w-10 rounded-full object-cover"
                                src="{{ $user->main_image?->getThumbnailUrl('small') }}">
                            {{$user->name}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.mobile')">
                            {{toPersianNumber($user->mobile)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.level')">
                            <x-dashboard::badge :color="$user->level->color()">
                                {{$user->level->label()}}</x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.created_at')">
                            {{toPersianNumber($user->created_at_jalali_date)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.status')">
                            @if($user->active)
                                <x-dashboard::badge color="green">
                                    @lang('user::attributes.active')</x-dashboard::badge>
                            @else
                                <x-dashboard::badge color="red">
                                    @lang('user::attributes.inactive')</x-dashboard::badge>
                            @endif
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('user::attributes.last_login_at')">
                            {{toPersianNumber($user->last_login_at_jalali_date)}}
                        </x-dashboard::table.cell>

                        <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                            <div class="flex gap-1">
                                <x-dashboard::buttons.primary-action id="btn-edit-user-{{$user->id}}" tag="a"
                                    href="{{ route('admin.users.edit', $user) }}" size="sm">
                                    <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add"
                                        class="w-5" />
                                </x-dashboard::buttons.primary-action>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-dashboard::table.table>

        {{$users->links('Core::pagination')}}
    </div>
</section>
