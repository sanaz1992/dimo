<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="$title">
        <x-slot:icon>
            <img src="{{ asset('icons/sidebar/instagram-white.svg') }}"
                alt="@lang('instagram::attributes.instagram_accounts_list')" />
        </x-slot:icon>

        {{-- فیلترها --}}
        {{-- <livewire:instagram::instagram-advanced-filters /> --}}

        <x-dashboard::buttons.primary-action id="btn-add-user" tag="button" class="btn-fill btn-new-tx shrink-0"
            wire:click="syncInstagramPosts" target="syncInstagramPosts">
            <x-slot:icon>
                <img src="{{ asset('icons/dashboard/sync-white.svg') }}" alt="sync_posts" style="max-width: 24px;" />
            </x-slot:icon>

            @lang('instagram::attributes.sync_posts')
        </x-dashboard::buttons.primary-action>
    </x-dashboard::card.card-header>

   @if (
    ($postsSyncRun &&in_array($postsSyncRun->status,[\Modules\Core\Enums\SyncRunStatus::PENDING,\Modules\Core\Enums\SyncRunStatus::RUNNING],true))
    ||($postsSyncRuns && $postsSyncRuns->isNotEmpty())
)
        <x-Core::sync-status
            :title="__('instagram::messages.posts_are_being_updated')"
            :description="__('instagram::messages.instagram_posts_are_being_fetched_in_the_background')"
        />
    @endif

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('instagram::attributes.instagram_username')</th>
                    <th>@lang('instagram::attributes.caption')</th>
                    <th>@lang('instagram::attributes.media_product_type')</th>
                    <th>@lang('instagram::attributes.published_at')</th>
                    <th>@lang('instagram::attributes.comments_count')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse($instagramPosts as $instagramPost)
                    <tr class="data-row" data-status="success">
                        {{-- ردیف --}}
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{ toPersianNumber(($instagramPosts->currentPage() - 1) * $instagramPosts->perPage() + $loop->iteration) }}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.instagram_username')">
                            {{$instagramPost->instagramAccount->username}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.caption')">
                          {{$instagramPost->caption_summery }}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.media_product_type')">
                            <x-dashboard::badge :color="$instagramPost->media_product_type?->color()">
                                {{$instagramPost->media_product_type?->label()}}
                            </x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.published_at')">
                            {{toPersianNumber($instagramPost->published_at_jalali)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.comments_count')">
                            {{toPersianNumber($instagramPost->comments_count)}}
                        </x-dashboard::table.cell>

                        <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                            <div class="flex gap-1">
                                <x-dashboard::buttons.primary-action id="btn-show-post-{{ $instagramPost->id }}" tag="a"
                                        href="{{ $instagramPost->permalink }}" target="_blank" size="sm"
                                        :lable="__('instagram::attributes.show_post_in_instagram')">
                                        <img src="{{ asset('icons/dashboard/vuesax/outline/eye.svg') }}" alt="show"
                                            class="w-5" />
                                    </x-dashboard::buttons.primary-action>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-[13px] text-ink-faint">
                            @lang('core::messages.no_data')
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-dashboard::table.table>

        {{-- Pagination --}}
        {{ $instagramPosts->links('Core::pagination') }}

    </div>

    {{$slot}}

</section>
