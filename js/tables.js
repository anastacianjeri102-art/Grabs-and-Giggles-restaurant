// Table Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if(alert) alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Add click animation to table cards
    const tableCards = document.querySelectorAll('.table-card');
    tableCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button
            if(e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Confirm status change
    const statusForms = document.querySelectorAll('.table-actions form');
    statusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button');
            const action = button.textContent.trim();
            const tableCard = this.closest('.table-card');
            const tableNumber = tableCard.querySelector('.table-number').textContent;
            
            let message = '';
            if(action.includes('Occupy')) {
                message = `Occupy ${tableNumber}? This table will be marked as occupied.`;
            } else if(action.includes('Reserve')) {
                message = `Reserve ${tableNumber}? This table will be marked as reserved.`;
            } else if(action.includes('Available')) {
                message = `Mark ${tableNumber} as available? This will free up the table.`;
            }
            
            if(!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});