// Bill Split JavaScript - Fixed Version
document.addEventListener('DOMContentLoaded', function() {
    
    // Get DOM elements
    const numPeopleInput = document.getElementById('numPeople');
    const perPersonAmountSpan = document.getElementById('perPersonAmount');
    
    // Get total amount from data attribute (set in PHP)
    let totalAmount = 0;
    
    // Try to get total amount from the page
    const totalAmountElement = document.querySelector('.total-price');
    if(totalAmountElement) {
        // Extract number from "Ksh 1,500" format
        const amountText = totalAmountElement.textContent;
        const match = amountText.match(/\d[\d,]*/);
        if(match) {
            totalAmount = parseInt(match[0].replace(/,/g, ''));
        }
    }
    
    // Alternative: get from hidden input if exists
    const totalHidden = document.getElementById('totalAmount');
    if(totalHidden) {
        totalAmount = parseInt(totalHidden.value);
    }
    
    // Function to update per person amount
    function updatePerPersonAmount() {
        if(!numPeopleInput || !perPersonAmountSpan) return;
        
        let numPeople = parseInt(numPeopleInput.value);
        
        // Validation
        if(isNaN(numPeople) || numPeople < 1) {
            numPeople = 1;
            numPeopleInput.value = 1;
        }
        if(numPeople > 20) {
            numPeople = 20;
            numPeopleInput.value = 20;
        }
        
        // Calculate per person amount
        const perPerson = totalAmount / numPeople;
        const formattedAmount = Math.round(perPerson).toLocaleString();
        
        // Update display
        perPersonAmountSpan.innerHTML = `Ksh ${formattedAmount}`;
        
        // Add animation to the card
        const perPersonCard = document.getElementById('perPersonCard');
        if(perPersonCard) {
            perPersonCard.style.transform = 'scale(1.02)';
            setTimeout(() => {
                perPersonCard.style.transform = 'scale(1)';
            }, 150);
        }
    }
    
    // Add event listeners
    if(numPeopleInput) {
        numPeopleInput.addEventListener('input', updatePerPersonAmount);
        numPeopleInput.addEventListener('change', updatePerPersonAmount);
    }
    
    // Initial calculation
    updatePerPersonAmount();
    
    // Form validation
    const splitForm = document.getElementById('splitForm');
    if(splitForm) {
        splitForm.addEventListener('submit', function(e) {
            const numPeople = parseInt(numPeopleInput.value);
            
            if(isNaN(numPeople) || numPeople < 1) {
                e.preventDefault();
                alert('Please enter a valid number of people (minimum 1)');
                numPeopleInput.focus();
                return false;
            }
            
            if(numPeople > 20) {
                e.preventDefault();
                alert('Maximum 20 people allowed for bill splitting');
                numPeopleInput.focus();
                return false;
            }
            
            // Show confirmation
            const perPerson = totalAmount / numPeople;
            if(!confirm(`Split bill ${numPeople} ways? Each person will pay Ksh ${Math.round(perPerson).toLocaleString()}`)) {
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    }
    
    // Add keyboard shortcut (Enter key submits)
    if(numPeopleInput) {
        numPeopleInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                const submitBtn = document.querySelector('.btn-split');
                if(submitBtn) submitBtn.click();
            }
        });
    }
});