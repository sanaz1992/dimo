@push('styles')
    @vite('Modules/Instagram/resources/assets/css/chat.css')
@endpush

<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="$title">
        <x-slot:icon>
            <img src="{{ asset('icons/dashboard/messages.svg') }}" alt="{{ $title }}" />
        </x-slot:icon>
    </x-dashboard::card.card-header>


    <div class="chat-layout mt-4" wire:key="instagram-chat-layout">

        {{-- =====================================================
        Conversations Sidebar
        ====================================================== --}}
        <section class="ui-card chat-conversations p-0 reveal d2">

            {{-- Header --}}
            <div class="chat-panel-header">
                <div>
                    <h3 class="font-bold text-sm text-[var(--text)]">
                        گفتگوها
                    </h3>

                    <p class="text-xs text-[var(--text-muted)] mt-1">
                        {{$instagramAccount->username}}
                    </p>
                </div>

                @if($conversations->count())
                    <span class="chat-count">
                        {{ $conversations->count() }}
                    </span>
                @endif
            </div>


            {{-- Conversations --}}
            <div class="chat-conversations-list">


                @forelse ($conversations as $conversation)

                    <button type="button"
                        class="conversation-item {{ $selectedConversation?->id === $conversation->id ? 'is-active' : '' }}"
                        wire:click="selectConversation({{ $conversation->id }})"
                        wire:key="conversation-{{ $conversation->id }}">

                        {{-- Avatar --}}
                        <span class="avatar shrink-0">
                            {{-- @if($conversation->customer_profile_picture_url)
                            <img src="{{ $conversation->customer_profile_picture_url }}"
                                alt="{{ $conversation->customer_username }}"
                                class="h-11 w-11 rounded-full object-cover">
                            @else --}}
                            <span class="chat-avatar-placeholder"
                                style="background: {{ $conversation->avatar_color }}; color: #fff;">
                                {{ mb_substr($conversation->customer_username, 0, 1) }}
                            </span>
                            {{-- @endif --}}
                        </span>


                        {{-- Content --}}
                        <span class="min-w-0 flex-1 text-right">

                            <span class="flex items-center justify-between gap-2">

                                <span class="block truncate font-bold text-sm">
                                    {{ $conversation->customer_username }}
                                </span>

                                <span class="shrink-0 text-[10px] text-[var(--text-muted)]">
                                    {{ toPersianNumber($conversation->last_message_at_jalali) }}
                                </span>

                            </span>


                            <span class="mt-1 flex items-center justify-between gap-2">

                                {{-- <span class="block truncate text-xs text-[var(--text-muted)]">
                                    {{ $conversation->last_message_preview ?? 'هنوز پیامی ارسال نشده است' }}
                                </span> --}}

                                {{-- @if($conversation->unread_count ?? 0)
                                <span class="chat-unread">
                                    {{ toPersianNumber($conversation->unread_count) }}
                                </span>
                                @endif --}}

                            </span>

                        </span>

                    </button>

                @empty

                    {{-- Empty conversations --}}
                    <div class="chat-empty-state">

                        <div class="chat-empty-icon">
                            <img src="{{ asset('icons/dashboard/messages.svg') }}" alt="">
                        </div>

                        <h4 class="font-bold text-sm">
                            هنوز گفتگویی وجود ندارد
                        </h4>

                        <p class="mt-1 text-xs leading-6 text-[var(--text-muted)]">
                            وقتی پیامی از طریق اینستاگرام دریافت شود،
                            گفتگوهای شما اینجا نمایش داده می‌شوند.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>


        {{-- =====================================================
        Chat Panel
        ====================================================== --}}
        <section class="ui-card chat-panel flex flex-col p-0 reveal d3"
            wire:key="chat-panel-{{ $selectedConversation?->id ?? 'empty' }}">

            @if($selectedConversation)

                {{-- =================================================
                Chat Header
                ================================================== --}}
                <div class="chat-header">

                    <div class="flex min-w-0 items-center gap-3">

                        <span class="avatar shrink-0">

                            {{-- @if($selectedConversation->customer_profile_picture_url)
                            <img src="{{ $selectedConversation->customer_profile_picture_url }}"
                                alt="{{ $selectedConversation->customer_username }}"
                                class="h-10 w-10 rounded-full object-cover">
                            @else --}}
                            <span class="chat-avatar-placeholder"
                                style="background: {{ $selectedConversation->avatar_color }}; color: #fff;">
                                {{ mb_substr($selectedConversation->customer_username, 0, 1) }}
                            </span>
                            {{-- @endif --}}

                        </span>


                        <div class="min-w-0">

                            <p class="truncate font-bold text-sm">
                                {{ $selectedConversation->customer_username }}
                            </p>

                            <p class="mt-0.5 text-xs text-[var(--text-muted)]">
                                گفتگوهای اینستاگرام
                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                Messages
                ================================================== --}}
                <div id="chat-thread" class="chat-thread" wire:key="messages-{{ $selectedConversation->id }}">

                    @forelse ($messages as $message)

                        <div class="message-row {{ $message->direction == Modules\Instagram\Enums\MessageDirection::INCOMING ? 'message-row-in' : 'message-row-out' }}"
                            wire:key="message-{{ $message->id }}">

                            <div
                                class="bubble {{ $message->direction == Modules\Instagram\Enums\MessageDirection::INCOMING ? 'bubble-in' : 'bubble-out' }}">
                                <div class="message-text">
                                    {{ $message->message_body }}
                                </div>

                                <div class="message-time">
                                    {{ toPersianNumber($message->sent_at_jalali) }}
                                </div>
                            </div>

                        </div>

                    @empty

                        <div class="chat-messages-empty">

                            <div class="chat-empty-icon">
                                <img src="{{ asset('icons/dashboard/messages.svg') }}" alt="">
                            </div>

                            <h4 class="font-bold text-sm">
                                هنوز پیامی وجود ندارد
                            </h4>

                            <p class="mt-1 text-xs text-[var(--text-muted)]">
                                این گفتگو هنوز پیامی ندارد.
                            </p>

                        </div>

                    @endforelse

                </div>


                {{-- بدون فرم ارسال پیام --}}

            @else

                {{-- =================================================
                No Conversation Selected
                ================================================== --}}
                <div class="chat-no-selection">

                    <div class="chat-no-selection-icon">
                        <img src="{{ asset('icons/dashboard/messages.svg') }}" alt="">
                    </div>

                    <h3 class="mt-4 font-bold text-base">
                        یک گفتگو را انتخاب کنید
                    </h3>

                    <p class="mt-2 max-w-sm text-center text-xs leading-6 text-[var(--text-muted)]">
                        برای مشاهده پیام‌ها، یکی از گفتگوهای سمت راست را انتخاب کنید.
                    </p>

                </div>

            @endif

        </section>

    </div>

    {{-- اگر slot واقعاً برای این کامپوننت لازم است نگه دار --}}
    {{ $slot }}

</section>


{{-- =============================================================
Scroll To Latest Message
============================================================= --}}
@push('scripts')
    <script>
        const scrollChatToBottom = () => {
            const thread = document.getElementById('chat-thread');

            if (!thread) {
                return;
            }

            requestAnimationFrame(() => {
                thread.scrollTop = thread.scrollHeight;
            });
        };

        // بعد از انتخاب گفتگو
        $wire.on('conversation-selected', () => {
            scrollChatToBottom();
        });

        // بعد از آپدیت Livewire
        Livewire.hook('morph.updated', ({ el }) => {

            if (el.querySelector?.('#chat-thread')) {
                scrollChatToBottom();
            }
        });
    </script>
@endpush
