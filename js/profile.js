document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle visibility of details container
    function toggleSection(label, details) {
        if (details.style.display === 'none' || details.style.display === '') {
            details.style.animation = 'slideIn 0.3s ease-in-out';
            details.style.display = 'block';
            setTimeout(() => {
                details.style.animation = '';
            }, 300);
        } else {
            details.style.display = 'none';
        }
    }

    // Get references to the labels and details containers
    const contaLabel = document.getElementById('contalabel');
    const contaDetails = document.getElementById('contadetails');

    const enderecoLabel = document.getElementById('enderecolabel');
    const enderecoDetails = document.getElementById('enderecodetails');

    // Initially hide the details containers
    contaDetails.style.display = 'none';
    enderecoDetails.style.display = 'none';

    // Add click event listeners to the labels
    contaLabel.addEventListener('click', () => {
        toggleSection(contaLabel, contaDetails);
        // Hide other sections
        enderecoDetails.style.display = 'none';
    });

    enderecoLabel.addEventListener('click', () => {
        toggleSection(enderecoLabel, enderecoDetails);
        // Hide other sections
        contaDetails.style.display = 'none';
    });

});

// deslogar

// Get reference to the logout label
const logoutLabel = document.getElementById('sair_label');

// Add a click event listener for logging out
logoutLabel.addEventListener('click', () => {
    // Send an AJAX request to a server-side script to perform logout
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'logout.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Redirect the user to a login page or a desired page after logout
            window.location.href = 'user.php';
        }
    };
    xhr.send();
});

// deslogar





