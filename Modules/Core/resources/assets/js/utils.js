window.formatNumberInput = function (el, model) {
    if (!el) return;

    // مقدار خام
    let raw = el.value.replace(/,/g, '').replace(/[^\d]/g, '');

    // نمایش به کاربر (با کاما)
    el.value = raw ? new Intl.NumberFormat('en-US').format(raw) : '';

    // hidden input که Livewire داره روی اون bind میشه
    const hidden = el.parentNode.querySelector(`input[type=hidden][wire\\:model\\.defer="${model}"]`);
    if (hidden) {
        hidden.value = raw;
        hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }
};
