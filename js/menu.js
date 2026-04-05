// Menu Page JavaScript
function toggleNewCustomer(select) {
    var form = select.closest('form');
    var newCustomerDiv = form.querySelector('.new-customer-form');
    
    if(select.value === 'new') {
        newCustomerDiv.style.display = 'block';
        var nameInput = newCustomerDiv.querySelector('input[name="new_name"]');
        var phoneInput = newCustomerDiv.querySelector('input[name="new_phone"]');
        if(nameInput) nameInput.required = true;
        if(phoneInput) phoneInput.required = true;
    } else {
        newCustomerDiv.style.display = 'none';
        var nameInput = newCustomerDiv.querySelector('input[name="new_name"]');
        var phoneInput = newCustomerDiv.querySelector('input[name="new_phone"]');
        if(nameInput) nameInput.required = false;
        if(phoneInput) phoneInput.required = false;
    }
}

// Auto-hide alerts if any
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(function(alert) {
        alert.style.opacity = '0';
        setTimeout(function() {
            if(alert) alert.remove();
        }, 300);
    });
}, 5000);