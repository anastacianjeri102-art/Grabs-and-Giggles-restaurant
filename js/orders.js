// Orders Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Filter orders by status
    const filterBtns = document.querySelectorAll('.filter-btn');
    const tableRows = document.querySelectorAll('.orders-table tbody tr');
    
    if(filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                tableRows.forEach(row => {
                    const status = row.dataset.status;
                    if(filter === 'all' || status === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
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
    
    // Confirm delete order
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to delete this order?')) {
                e.preventDefault();
            }
        });
    });
    
    // Confirm complete order
    const completeBtns = document.querySelectorAll('.complete-btn');
    completeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Mark this order as completed?')) {
                e.preventDefault();
            }
        });
    });
    
    // Confirm payment
    const paymentBtns = document.querySelectorAll('.payment-btn');
    paymentBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Process payment for this order?')) {
                e.preventDefault();
            }
        });
    });
});