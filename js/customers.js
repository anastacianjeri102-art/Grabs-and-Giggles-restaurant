// Customer Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Search functionality
    const searchInput = document.getElementById('searchCustomer');
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customerTable tbody tr');
            rows.forEach(row => {
                if(row.cells.length > 1) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                }
            });
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if(alert) alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Confirm delete
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to delete this customer? This will NOT delete their order history.')) {
                e.preventDefault();
            }
        });
    });
    
    // Phone number validation for add customer form
    const addForm = document.querySelector('.form-card form');
    if(addForm) {
        addForm.addEventListener('submit', function(e) {
            const phoneInput = this.querySelector('input[name="phone"]');
            const phone = phoneInput.value.trim();
            
            if(phone && !/^[0-9]{10,12}$/.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid phone number (10-12 digits)');
                phoneInput.focus();
                return false;
            }
            return true;
        });
    }
});