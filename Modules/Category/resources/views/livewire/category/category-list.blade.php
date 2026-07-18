{{-- <div class="p-6 bg-white">
    <h2 class="text-2xl font-semibold mb-4">{{__('product::attributes.category_list')}}</h2>
    <div class=" m-3">
        <a href="{{ route('admin.categories.create') }}"
            class="bg-blue-700 text-white px-3 py-1 rounded ">{{__('product::attributes.category_create')}}</a>
    </div>
    <table class="w-full border border-gray-300 text-sm text-right">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">{{__('product::attributes.image')}} </th>
                <th class="px-4 py-2">{{__('product::attributes.name')}} </th>
                <th class="px-4 py-2">{{__('product::attributes.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ $category->id }}</td>
                <td class="px-4 py-2">
                    <img src="{{ $category->main_image?->getThumbnailUrl('small') }}" class="h-32" />
                </td>
                <td class="px-4 py-2">{{ $category->name }}</td>
                <td class="px-4 py-2">
                    @can('categories_edit')
                    <a href="{{ route('admin.categories.edit', $category) }}"
                        class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700">{{__('product::attributes.edit')}}</a>
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                    {{__('product::messages.without_category')}}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $categories->links('Core::pagination') }}
</div> --}}

<section>
    <livewire:UI::tabs :tabs="$allTabs" :active="$activeTab" />

    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">

        <div class="flex flex-col gap-4 md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-[24px]">
                @lang('product::attributes.category_list')
            </h2>
            <div class="flex items-center gap-4">

                <a href="{{route('admin.categories.create')}}"
                    class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                    <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                    <span class="">@lang("product::attributes.category_create")</span>
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
                                    @lang("warehouse::attributes.row")
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('category::attributes.image')}}
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('category::attributes.name')}}
                                </th>
                                <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('category::attributes.form.type')}}
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('category::attributes.actions')}}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" x-data="{
                            expanded: {},
                            isShown(ancestors) {
                                return ancestors.every(id => this.expanded[id] !== false);
                            }
                        }">
                            @php $pathStack = []; @endphp
                            @forelse($categories as $category)
                                @php
                                    $depth = (int) ($category->treeDepth ?? 0);
                                    $hasChildren = $category->children->count() > 0;
                                    if ($depth === 0) {
                                        $pathStack = [];
                                    } else {
                                        $pathStack = array_slice($pathStack, 0, $depth);
                                        $pathStack[$depth - 1] = (int) $category->parent_id;
                                    }
                                    $ancestorIds = array_values($pathStack);
                                @endphp
                                <tr class="border-t hover:bg-gray-50/80 transition-colors duration-200 {{ $depth > 0 ? 'bg-[#F6F6F5]/40' : '' }}"
                                    @if($depth > 0)
                                        x-show="isShown(@js($ancestorIds))"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-250"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-2"
                                    @endif
                                >
                                    <td class="px-4 py-2">{{ $loop->index+1}}</td>

                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        <img alt="{{$category->slug}}" class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $category->main_image?->getThumbnailUrl('small')}}">
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center" style="margin-right: {{ $depth * 24 }}px;">
                                            @if($depth > 0)
                                                <!-- Premium SVG tree connector for RTL -->
                                                <svg class="w-4 h-5 text-[#3E3E3B]/40 ml-2 shrink-0" fill="none" viewBox="0 0 16 20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 0v10a4 4 0 0 1-4 4H2" />
                                                </svg>
                                            @endif

                                            @if($hasChildren)
                                                <!-- Collapse/Expand indicator button for parent category -->
                                                <button type="button" @click="expanded[{{ $category->id }}] = expanded[{{ $category->id }}] === false ? true : false"
                                                    class="ml-2 focus:outline-none transition-all duration-300 flex items-center justify-center w-7 h-7 rounded-lg bg-[#3E3E3B]/10 border border-[#3E3E3B]/20 hover:bg-[#3E3E3B] hover:border-[#3E3E3B] text-[#3E3E3B] hover:text-white shadow-sm font-bold"
                                                    :class="expanded[{{ $category->id }}] === false ? 'rotate-0' : 'rotate-90'">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </button>
                                                <span class="text-sm {{ $depth === 0 ? 'font-bold text-gray-800' : 'font-medium text-gray-500' }}">{{ $category->name }}</span>
                                            @else
                                                <!-- Empty space matching toggle button width for alignment -->
                                                <div class="w-9"></div>
                                                <span class="text-sm {{ $depth === 0 ? 'font-bold text-gray-800' : 'font-medium text-gray-500' }}">{{ $category->name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">{{ !empty($category->getRawOriginal('type')) ? $category->type->label():'' }}</td>
                                    <td class="px-4 py-2">
                                        @can('categories_edit')

                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                                class="block text-sm px-4 text-gray-700 hover:bg-gray-100"
                                                style="float: right;">
                                                <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/edit-2.svg') }}"
                                                    alt="add" class="w-5" />
                                            </a>
                                        @endcan
                                        @can('categories_delete')
                                        <div class="flex justify-end"
                                             x-data="{
                                                 selectedReplacementCategory: null,
                                                 categories: @js(resolve(\Modules\Category\Services\CategoryService::class)->list(
                                                                conditions: ['where' => ['type' => ['=', $category->type]]]
                                                            )->reject(fn ($cat) => $cat->id == $category->id)->pluck('name', 'id')),
                                                 errorMessage: @entangle('errorMessage'),
                                                 showActions: false,
                                                 showDeleteModal: @entangle("showDeleteModal.$category->id"),
                                                 menuStyle: '',
                                                 updatePos(el) { const r = el.getBoundingClientRect(); this.menuStyle = `position:fixed; top:${r.bottom + 8}px; left:${r.left}px;`; },
                                                 openModal() {
                                                    // Get data from Livewire component
                                                    this.showDeleteModal = true;

                                                    // Prevent body scroll
                                                    document.body.style.overflow = 'hidden';
                                                },
                                                closeModal() {
                                                    this.showDeleteModal = false;
                                                    document.body.style.overflow = '';

                                                    // Call Livewire method to reset state
                                                    @this.cancelDelete();
                                                },
                                                confirmDelete(selectedCategory) {
                                                    @this.confirmDelete(@js($category->id), selectedCategory);
                                                }
                                            }
                                             "
                                             x-on:open-modal.window="openModal()"
                                             x-on:close-modal.window="closeModal()"
                                        >
                                            <button @click="showActions = !showActions; if (showActions) updatePos($el);"
                                                    @click.away="showActions = false"
                                                    @resize.window="showActions = false"
                                                    @scroll.window="showActions = false"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20"
                                                     fill="currentColor">
                                                    <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                </svg>
                                            </button>
                                            <!-- منوی عملیات -->
                                            <template x-teleport="body">
                                                <div x-show="showActions" @click.away="showActions = false"
                                                     :style="menuStyle"
                                                     class="w-48 bg-white rounded-lg shadow-hard-sm z-50 border border-gray-200">
                                                    <div class="">
                                                        <button
                                                                wire:click="deleteCategory({{$category->id}})"
                                                                class="w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-eye ml-2"></i>
                                                            {{__('category::messages.delete') }}
                                                        </button>
                                                    </div>
                                            </template>
                                            <template x-teleport="body">
                                                <div x-show="showDeleteModal"
                                                     x-cloak
                                                     x-transition:enter="transition ease-out duration-300"
                                                     x-transition:enter-start="opacity-0"
                                                     x-transition:enter-end="opacity-100"
                                                     x-transition:leave="transition ease-in duration-200"
                                                     x-transition:leave-start="opacity-100"
                                                     x-transition:leave-end="opacity-0"
                                                     class="fixed inset-0 z-50 flex items-center justify-center">

                                                    <!-- Backdrop -->
                                                    <div class="absolute inset-0 " style="background-color: gray; opacity: 0.5"
                                                         @click="closeModal()">
                                                    </div>

                                                    <!-- Modal Content -->
                                                    <div x-show="showDeleteModal"
                                                         x-transition:enter="transition ease-out duration-300"
                                                         x-transition:enter-start="opacity-0 scale-95"
                                                         x-transition:enter-end="opacity-100 scale-100"
                                                         x-transition:leave="transition ease-in duration-200"
                                                         x-transition:leave-start="opacity-100 scale-100"
                                                         x-transition:leave-end="opacity-0 scale-95"
                                                         @click.away="closeModal()"
                                                         id="modal-boy-{{$category->id}}"
                                                         class="relative bg-white rounded-lg shadow-xl w-full md:w-1/5 p-6 gap-4 flex flex-col"
                                                    >

                                                        <!-- Modal Header -->
                                                        <div class="flex justify-between items-center mb-4">
                                                            <h3 class="text-xl font-bold text-gray-900">
                                                                {{__('category::messages.delete')}}
                                                            </h3>
                                                            <button @click="closeModal()"
                                                                    class="text-gray-400 hover:text-gray-600">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <!-- Modal Body -->
                                                        <div class="mb-6">
                                                            <p class="text-gray-600" x-text="errorMessage"></p>
                                                        </div>
                                                        <label>
                                                            {{__('category::messages.select_replacement_category')}}
                                                        </label>
                                                        <select x-model="selectedReplacementCategory">
                                                            <option value=""></option>
                                                            <template x-for="(cat, id) in categories" :key="id" >
                                                                <option :value="id" x-text="cat"></option>
                                                            </template>
                                                        </select>

                                                        <!-- Modal Footer -->
                                                        <div class="flex justify-end gap-3">
                                                            <button @click="closeModal()"
                                                                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition">
                                                                {{__('category::attributes.cancel')}}
                                                            </button>
                                                            <button @click="confirmDelete(selectedReplacementCategory)"
                                                                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600 transition">
                                                                {{__('category::attributes.delete_and_move')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                        {{__('category::messages.without_category')}}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
