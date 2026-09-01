import './bootstrap';
import * as bootstrap from 'bootstrap';
import { autocompleteField } from './components/autocompleteField';
import './dashboard/karyawanChart.js';
import './dashboard/kendaraanChart.js';
import './dashboard/seamer-monitor.js';

// Expose fungsi ke window agar dapat diakses dari atribut HTML/Alpine jika diperlukan
window.autocompleteField = autocompleteField;

// 1. Pendaftaran komponen/directive Alpine via event 'alpine:init'
document.addEventListener('alpine:init', () => {
    // Daftarkan autocompleteField sebagai Alpine Data jika ingin digunakan via x-data="autocompleteField"
    window.Alpine.data('autocompleteField', autocompleteField);
});

// 2. Inisialisasi komponen Bootstrap UI setelah DOM siap
document.addEventListener('DOMContentLoaded', function () {
    // Tooltip
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

    // Popover
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(el => new bootstrap.Popover(el));

    // Specific Tooltip untuk importButton (dengan pengecekan elemen agar tidak error jika tidak ada di page)
    const importBtn = document.getElementById('importButton');
    if (importBtn) {
        new bootstrap.Tooltip(importBtn);
    }
});