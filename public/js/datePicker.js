import flatpickr from "flatpickr";

document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#startDateExport", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
    flatpickr("#endDateExport", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
});