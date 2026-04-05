// Payment Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // M-Pesa phone number validation
    const mpesaForm = document.querySelector('.mpesa-form form');
    if(mpesaForm) {
        mpesaForm.addEventListener('submit', function(e) {
            const phoneInput = this.querySelector('input[name="mpesa_number"]');
            const phone = phoneInput.value.trim();
            
            // Validate Kenyan phone number (10 digits starting with 07 or 01)
            const phoneRegex = /^(07|01)[0-9]{8}$/;
            if(!phoneRegex.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid Kenyan phone number (e.g., 0712345678)');
                phoneInput.focus();
                return false;
            }
            
            // Confirm payment
            const amount = document.querySelector('.amount-display strong')?.textContent || '';
            if(!confirm(`Confirm M-Pesa payment of ${amount} from ${phone}?`)) {
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    }
    
    // Cash payment confirmation
    const cashForm = document.querySelector('.payment-form button[name="cash_payment"]');
    if(cashForm) {
        cashForm.addEventListener('click', function(e) {
            if(!confirm('Confirm cash payment?')) {
                e.preventDefault();
            }
        });
    }
    
    // Print receipt confirmation
    const printBtn = document.querySelector('.print-btn');
    if(printBtn) {
        printBtn.addEventListener('click', function() {
            setTimeout(() => {
                // Optional: Add analytics or logging here
            }, 100);
        });
    }
});