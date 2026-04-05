document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
    // Highlight low stock items
    const lowStockItems = document.querySelectorAll('.status-low');
    lowStockItems.forEach(item => {
        item.closest('tr').style.backgroundColor = '#fff5e8';
    });
    
    // Confirm delete
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to delete this inventory item?')) {
                e.preventDefault();
            }
        });
    });
});