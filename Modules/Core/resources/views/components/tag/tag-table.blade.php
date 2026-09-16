<div>
    <section class="table-panel anim-fade-up">

        <x-dashboard::card.card-header :title="$title">
            <x-slot:icon>
                <img src="{{ asset('icons/sidebar/tag.svg') }}" alt="@lang('core::attributes.tag_list')" />
            </x-slot:icon>

            {{-- فیلترها --}}
            {{-- <livewire:core::instagram-advanced-filters /> --}}

            <x-dashboard::buttons.primary-action id="btn-add-tag" tag="button" class="btn-fill btn-new-tx shrink-0"
                wire:click="createTag" target="createTag">
                <x-slot:icon>
                    <img src="{{ asset('icons/header/add.svg') }}" alt="create_tag" />
                </x-slot:icon>
                @lang('core::attributes.create_tag')
            </x-dashboard::buttons.primary-action>
        </x-dashboard::card.card-header>

        <div>
            <x-dashboard::table.table>
                <x-slot:head>
                    <tr>
                        <th>@lang('core::attributes.row')</th>
                        <th>@lang('core::attributes.name')</th>
                        <th>@lang('core::attributes.tenant')</th>
                        <th>@lang('core::attributes.color')</th>
                        <th>@lang('core::attributes.status')</th>
                        <th>@lang('core::attributes.created_at')</th>
                        <th class="col-actions"></th>
                    </tr>
                </x-slot:head>

                <x-slot:body>
                    @forelse($tags as $tag)
                        <tr class="data-row" data-status="success">
                            {{-- ردیف --}}
                            <x-dashboard::table.cell :label="__('core::attributes.row')">
                                {{ toPersianNumber(($tags->currentPage() - 1) * $tags->perPage() + $loop->iteration) }}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('core::attributes.name')">
                                {{toPersianNumber($tag->name)}}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('core::attributes.tenant')">
                                {{$tag->tenant->name}}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('core::attributes.color')">
                                <div class="flex items-center gap-2">
                                    <span>{{ $tag->color }}</span>
                                    <span class="inline-block w-5 h-5 rounded-full border"
                                        style="background-color: {{ $tag->color }};"></span>
                                </div>
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('core::attributes.status')">
                                <x-dashboard::badge :color="$tag->is_active ? 'green' : 'red'">
                                    {{$tag->is_active ? 'فعال' : 'غیرفعال'}}
                                </x-dashboard::badge>
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('core::attributes.created_at')">
                                {{toPersianNumber($tag->created_at_jalali)}}
                            </x-dashboard::table.cell>

                            <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                                <div class="flex gap-1">
                                    <x-dashboard::buttons.primary-action id="btn-tag-{{ $tag->slug }}-edit" tag="button"
                                        wire:click="editTag('{{ $tag->slug }}')" target="editTag('{{ $tag->slug }}')"
                                        size="sm" color="blue" :title="__('core::attributes.edit')">
                                        <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}"
                                            alt="retry-automation-run" class="w-5" />
                                    </x-dashboard::buttons.primary-action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[13px] text-ink-faint">
                                @lang('core::messages.no_data')
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-dashboard::table.table>

            {{-- Pagination --}}
            {{ $tags->links('Core::pagination') }}

        </div>

        {{$slot}}

    </section>

    @if($showTagCreateModal)
        @teleport('body')
        <div class="modal-backdrop modal-backdrop--show" wire:click="$set('showTagCreateModal', false)">
            <div class="modal modal--show max-w-lg " role="dialog" aria-modal="true" @click.stop>
                <div class="modal-head">
                    <h2 class="text-lg font-bold text-ink">
                        @lang('core::attributes.create_tag')
                    </h2>
                    <button type="button" class="btn-ghost" aria-label="بستن"
                        wire:click="$set('showTagCreateModal', false)">
                        ×
                    </button>
                </div>

                <form class="modal-body space-y-3">
                    <x-dashboard::forms.select label="core::attributes.tenant" wire:model.live="form.tenant"
                        :options="$tenants" option-value="slug" placeholder="core::messages.select_tenant" />

                    <x-dashboard::forms.input label="core::attributes.name" wire:model.defer="form.name" />

                    <x-dashboard::forms.input type="color" label="core::attributes.color" wire:model.defer="form.color" />

                    <x-dashboard::forms.radio label="core::attributes.status" wire:model.defer="form.is_active" :options="[
                '1' => 'core::attributes.active',
                '0' => 'core::attributes.inactive',
            ]" />

                    <x-dashboard::buttons.primary-action id="submit-create-tag" tag="button" wire:click="storeTag" size="sm"
                        class="btn-fill" target="storeTag">
                        @lang('core::attributes.store')
                    </x-dashboard::buttons.primary-action>

                </form>
            </div>
        </div>
        @endteleport
    @endif
</div>
