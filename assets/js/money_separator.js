// Format amount input for pawned items
// This script formats the amount input for pawned items with thousand separators
// while typing, and ensures the hidden input for submission is correctly formatted.
/* Add Pawn: format amount input with thousand separators while typing.
   Visible input: #addAmountPawnedVisible
   Hidden input (submitted): #addAmountPawned
*/
// Reusable Currency Input Formatter
// Usage: attachFormatter(visibleInputElement, hiddenInputElement);
function attachCurrencyFormatter(visible, hidden) {
    if (!visible || !hidden) return;

    function formatCurrencyInput(raw) {
        if (!raw) return '';
        raw = raw.replace(/[^\d.]/g, '');
        const parts = raw.split('.');
        let intPart = parts[0].replace(/^0+(?=\d)/, '');
        if (intPart === '') intPart = '0';
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        if (parts.length > 1) {
            parts[1] = parts[1].slice(0, 2); // max 2 decimals
            return intPart + '.' + parts[1];
        }
        return intPart;
    }

    function rawNumberString(formatted) {
        return formatted.replace(/,/g, '');
    }

    function syncHidden() {
        const rawStr = rawNumberString(visible.value);
        if (rawStr === '' || rawStr === '.') {
            hidden.value = '';
            return;
        }
        const normalized = parseFloat(rawStr);
        hidden.value = isNaN(normalized) ? '' : normalized.toFixed(2);
    }

    visible.addEventListener('input', function () {
        this.value = formatCurrencyInput(this.value);
        this.selectionStart = this.selectionEnd = this.value.length;
        syncHidden();
    });

    visible.addEventListener('blur', function () {
        const raw = rawNumberString(this.value);
        if (raw === '' || raw === '.') {
            this.value = '';
            hidden.value = '';
            return;
        }
        const num = parseFloat(raw);
        const parts = num.toFixed(2).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        this.value = parts.join('.');
        hidden.value = num.toFixed(2);
    });

    visible.addEventListener('keypress', function (e) {
        const allowed = /[0-9.]/;
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        const char = String.fromCharCode(e.which);
        if (!allowed.test(char)) e.preventDefault();
        if (char === '.' && this.value.includes('.')) e.preventDefault();
    });

    // Optional: sync hidden before form submit
    const form = visible.closest('form');
    if (form) {
        form.addEventListener('submit', () => visible.dispatchEvent(new Event('blur')));
    }
}


// Refactored script to format pawn amount inputs inside editPawnModal
// document.addEventListener('DOMContentLoaded', function () {
//     $('#editPawnModal').on('show.bs.modal', function () {
//         const visible = document.getElementById('editAmountPawnedVisible');
//         const hidden = document.getElementById('editAmountPawned');

//         if (!visible || !hidden) return;

//         function formatNumber(value) {
//             if (value === '' || value === null || isNaN(value)) return '';
//             return parseFloat(value).toLocaleString(undefined, {
//                 minimumFractionDigits: 2,
//                 maximumFractionDigits: 2
//             });
//         }

//         function rawFromFormatted(str) {
//             if (!str) return '';
//             const cleaned = str.replace(/[^0-9.-]/g, '');
//             return cleaned === '' ? '' : parseFloat(cleaned).toFixed(2);
//         }

//         // Format existing raw value when modal opens
//         const raw = hidden.value;
//         visible.value = raw ? formatNumber(raw) : '';

//         // Format while typing and update hidden input
//         visible.addEventListener('input', function (e) {
//             const pos = this.selectionStart;
//             const oldLen = this.value.length;

//             let raw = this.value.replace(/,/g, '').replace(/[^0-9.]/g, '');
//             const parts = raw.split('.');
//             if (parts.length > 2) {
//                 raw = parts.shift() + '.' + parts.join('');
//             }

//             let [integer, decimal] = raw.split('.');
//             integer = integer || '0';
//             decimal = decimal || '';

//             if (decimal.length > 2) decimal = decimal.slice(0, 2);
//             integer = integer.replace(/^0+(?=\d)/, '');

//             let formatted = integer ? Number(integer).toLocaleString() : '';
//             if (decimal !== '') {
//                 formatted = (formatted === '' ? '0' : formatted) + '.' + decimal;
//             }

//             if (integer === '' && decimal === '') formatted = '';

//             this.value = formatted;
//             hidden.value = (raw === '' || isNaN(Number(raw))) ? '' : Number(raw).toFixed(2);

//             const newLen = this.value.length;
//             const newPos = Math.max(0, pos + (newLen - oldLen));
//             this.setSelectionRange(newPos, newPos);
//         });

//         // Observe changes to hidden input and update visible input
//         const observer = new MutationObserver(function (mutations) {
//             mutations.forEach(function (m) {
//                 if (m.attributeName === 'value') {
//                     const raw = hidden.value;
//                     visible.value = raw ? formatNumber(raw) : '';
//                 }
//             });
//         });
//         observer.observe(hidden, { attributes: true, attributeFilter: ['value'] });
//     });
// });

