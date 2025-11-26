import './bootstrap';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import { autocompleteField } from './components/autocompleteField';
import './dashboard/karyawanChart.js';
import './dashboard/kendaraanChart.js';
import './dashboard/suhuChart.js';
import './dashboard/counterChart.js';
import './dashboard/speedChart.js';

window.Alpine = Alpine;
window.autocompleteField = autocompleteField;

document.addEventListener('DOMContentLoaded', function () {
    Alpine.start();

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(el => new bootstrap.Popover(el));

    const tooltip = new bootstrap.Tooltip(document.getElementById('importButton'));
});

