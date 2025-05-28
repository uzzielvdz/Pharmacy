// Toggle Sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        });
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Table filtering
    window.filterTable = function(input, tableId) {
        const filter = input.value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    };

    // Print functionality
    window.printElement = function(elementId) {
        const element = document.getElementById(elementId);
        const originalContents = document.body.innerHTML;
        const printContents = element.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    };

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete
    window.confirmDelete = function(id, type) {
        if (confirm('¿Estás seguro de que deseas eliminar este ' + type + '?')) {
            window.location.href = `/delete/${type}/${id}`;
        }
    };

    // Dynamic form fields
    window.addFormField = function(containerId, template) {
        const container = document.getElementById(containerId);
        const newField = template.cloneNode(true);
        container.appendChild(newField);
    };

    window.removeFormField = function(button) {
        const field = button.closest('.form-field');
        field.remove();
    };

    // Image preview
    window.previewImage = function(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Funciones para productos
    window.editProduct = function(id) {
        window.location.href = `index.php?controller=product&action=edit&id=${id}`;
    }

    window.viewProduct = function(id) {
        window.location.href = `index.php?controller=product&action=view&id=${id}`;
    }

    window.deleteProduct = function(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
            fetch(`index.php?controller=product&action=delete&id=${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error al eliminar el producto: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el producto');
            });
        }
    }
}); 