// Staff Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete
    const deleteBtns = document.querySelectorAll('.delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to delete this staff member?')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if(alert) alert.style.display = 'none';
            }, 300);
        }, 3000);
    });
});