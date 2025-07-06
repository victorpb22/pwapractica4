// Funcionalidad general del sistema

document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e8491d';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
            }
        });
    });
    
    // Mostrar/ocultar contraseña
    const togglePassword = document.querySelectorAll('.toggle-password');
    togglePassword.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
        });
    });
    
    // Confirmación antes de acciones importantes
    const deleteButtons = document.querySelectorAll('.btn-delete, a[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de realizar esta acción?')) {
                e.preventDefault();
            }
        });
    });
    
    // Validación de contraseña en tiempo real
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const hint = this.nextElementSibling;
            
            if (value.length > 0 && value.length < 8) {
                this.style.borderColor = '#e8491d';
                if (hint) hint.style.color = '#e8491d';
            } else if (!/[A-Z]/.test(value) || !/[0-9]/.test(value)) {
                this.style.borderColor = '#e8491d';
                if (hint) hint.style.color = '#e8491d';
            } else {
                this.style.borderColor = '#ddd';
                if (hint) hint.style.color = '#6c757d';
            }
        });
    });
    
    // Tooltips para observaciones
    const observaciones = document.querySelectorAll('.observaciones');
    observaciones.forEach(obs => {
        obs.addEventListener('click', function() {
            alert(this.title);
        });
    });
});

// Funciones AJAX
function makeAjaxRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            } else {
                console.error('Error en la solicitud AJAX');
            }
        }
    };
    xhr.send(data);
}