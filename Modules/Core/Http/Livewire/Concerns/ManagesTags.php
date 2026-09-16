<?php

namespace Modules\Core\Http\Livewire\Concerns;

use Illuminate\Validation\ValidationException;
use Modules\Core\Services\TagService;
use Modules\Core\Traits\LivewireNotify;

trait ManagesTags
{
    use LivewireNotify;

    public $showTagCreateModal = false;

    public $selectedTag = null;

    public $form = [
        'edit_tag' => false,
        'tenant' => '',
        'name' => '',
        'color' => '',
        'is_active' => true,
    ];

    public function createTag()
    {
        $this->reset('form');

        $this->form['edit_tag'] = false;
        $this->form['is_active'] = true;

        $this->showTagCreateModal = true;
    }

    public function editTag($slug)
    {
        $tagService = app(TagService::class);

        $this->selectedTag = $tagService->findByColumn('slug', $slug);

        if (! $this->selectedTag) {
            $this->notify(
                'error',
                __('core::messages.not_found')
            );

            return;
        }

        $this->form['edit_tag'] = true;
        $this->form['tenant'] = $this->selectedTag->tenant->slug;
        $this->form['name'] = $this->selectedTag->name;
        $this->form['color'] = $this->selectedTag->color;
        $this->form['is_active'] = $this->selectedTag->is_active;

        $this->showTagCreateModal = true;
    }

    public function storeTag(TagService $tagService)
    {
        try {
            $this->validate([
                'form.tenant' => [
                    'required',
                    'exists:tenants,slug',
                ],

                'form.name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'form.color' => [
                    'nullable',
                    'string',
                    'max:7',
                ],

                'form.is_active' => [
                    'required',
                    'boolean',
                ],
            ]);

            if ($this->form['edit_tag']) {
                $tagService->update(
                    $this->selectedTag,
                    $this->form
                );

                $this->notify(
                    'success',
                    __('core::messages.edit.success')
                );
            } else {
                $tagService->create($this->form);

                $this->notify(
                    'success',
                    __('core::messages.create.success')
                );
            }

            $this->showTagCreateModal = false;

            $this->resetTagForm();

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            $this->notify(
                'error',
                __('core::messages.error')
            );
        }
    }

    protected function resetTagForm()
    {
        $this->reset('form');

        $this->selectedTag = null;

        $this->form['edit_tag'] = false;
        $this->form['is_active'] = true;
    }
}
