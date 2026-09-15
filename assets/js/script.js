 // Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const claimModal = document.getElementById('claimModal');
    const actionModal = document.getElementById('actionModal');
    const closeButtons = document.querySelectorAll('.close');
    
    // View claim details
    const viewButtons = document.querySelectorAll('.view-details');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const claimId = this.getAttribute('data-id');
            fetchClaimDetails(claimId);
        });
    });
    
    // Approve claim
    const approveButtons = document.querySelectorAll('.approve-claim');
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const claimId = this.getAttribute('data-id');
            showActionModal(claimId, 'approve');
        });
    });
    
    // Reject claim
    const rejectButtons = document.querySelectorAll('.reject-claim');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const claimId = this.getAttribute('data-id');
            showActionModal(claimId, 'reject');
        });
    });
    
    // Close modals
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            claimModal.style.display = 'none';
            actionModal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target == claimModal) {
            claimModal.style.display = 'none';
        }
        if (event.target == actionModal) {
            actionModal.style.display = 'none';
        }
    });
});

// Fetch claim details via AJAX
function fetchClaimDetails(claimId) {
    fetch(`get_claim_details.php?id=${claimId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const detailsDiv = document.getElementById('claimDetails');
                detailsDiv.innerHTML = `
                    <h3>Claim Details - ID: #${data.claim.id}</h3>
                    <div class="claim-details">
                        <p><strong>Claimant:</strong> ${data.claim.claimant}</p>
                        <p><strong>Email:</strong> ${data.claim.email}</p>
                        <p><strong>Item:</strong> ${data.claim.item_name}</p>
                        <p><strong>Claim Date:</strong> ${data.claim.claimed_at}</p>
                        <p><strong>Status:</strong> <span class="status ${data.claim.status}">${data.claim.status}</span></p>
                        <hr>
                        <h4>Claim Description:</h4>
                        <p>${data.claim.claim_description}</p>
                        <h4>Proof Details:</h4>
                        <p>${data.claim.proof_details}</p>
                        ${data.claim.admin_remarks ? `
                            <h4>Admin Remarks:</h4>
                            <p>${data.claim.admin_remarks}</p>
                        ` : ''}
                    </div>
                `;
                document.getElementById('claimModal').style.display = 'block';
            }
        })
        .catch(error => console.error('Error:', error));
}

// Show action modal
function showActionModal(claimId, action) {
    document.getElementById('claim_id').value = claimId;
    document.getElementById('action_type').value = action;
    document.getElementById('modalTitle').textContent = 
        action === 'approve' ? 'Approve Claim' : 'Reject Claim';
    document.getElementById('actionModal').style.display = 'block';
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let valid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            valid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });
    
    return valid;
}

// Password strength checker
function checkPasswordStrength(password) {
    const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
    const mediumRegex = /^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})/;
    
    if (strongRegex.test(password)) {
        return 'strong';
    } else if (mediumRegex.test(password)) {
        return 'medium';
    } else {
        return 'weak';
    }
}

// Image preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    const reader = new FileReader();
    
    reader.onloadend = function() {
        preview.innerHTML = `<img src="${reader.result}" alt="Preview" style="max-width: 200px;">`;
    }
    
    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

// Search functionality
function searchItems() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const items = document.querySelectorAll('.item-card');
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}