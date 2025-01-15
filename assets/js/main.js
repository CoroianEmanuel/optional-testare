// Funcție pentru confirmarea ștergerii
function confirmDelete(message) {
    return confirm(message || 'Ești sigur că vrei să ștergi acest element?');
}

// Inițializare tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Validare formular
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Actualizare automată a statusului
function updateTaskStatus(taskId, newStatus) {
    fetch(`update_status.php?id=${taskId}&status=${newStatus}`, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('A apărut o eroare la actualizarea statusului.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A apărut o eroare la actualizarea statusului.');
    });
} 