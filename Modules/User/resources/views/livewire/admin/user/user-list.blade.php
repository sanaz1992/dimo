<section class="panel p-5 anim-fade-up">
    <div class="relative z-[1] mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-bold text-ink sm:text-lg">@lang('user::attributes.users_list')</h2>
            <p class="text-[11px] text-ink-faint sm:text-[12px]">{{count($users)}} @lang('user::attributes.user') </p>
        </div>
        <x-dashboard::buttons.primary-action id="btn-add-user" tag="a" href="{{ route('admin.users.create') }}">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
            </x-slot:icon>

            @lang('user::attributes.new_user')
        </x-dashboard::buttons.primary-action>
    </div>

    <div class="relative z-[1] grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 anim-stagger">
        @foreach ($users as $user)
            <div class="user-card anim-pop" data-searchable style="animation-delay:0s">
                <div class="flex items-center gap-3">
                    <span class="user-dot">{{ substr($user->name, 0, 1) }}</span>
                    <div>
                        <p class="font-semibold text-ink">{{$user->name}}</p>
                        <p class="text-[11px] text-ink-faint">{{$user->mobile}}</p>
                    </div>
                </div>
                {{-- <a class="row-btn mt-3  justify-center">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            class="icon-svg shrink-0" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor"
                                stroke-width="1.5"></path>
                            <circle cx="12" cy="12" r="2.5" fill="currentColor" fill-opacity="0.2" stroke="currentColor"
                                stroke-width="1.5"></circle>
                        </svg>
                    </span>
                </a> --}}
                <a class="row-btn mt-3 justify-center" href="{{ route('admin.users.edit', $user) }}">
                    <span>
                        <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add" class="w-5" />
                    </span>
                </a>
            </div>
        @endforeach
    </div>
</section>
