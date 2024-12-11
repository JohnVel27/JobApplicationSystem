
        function showContent(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            // Show the requested section
            document.getElementById(sectionId).style.display = 'block';
        }


function viewApplication(applicationID) {
        // Redirect to jobapplicationdetails.php with the application_id in the URL
        window.location.href = 'jobapplicationdetails.php?application_id=' + applicationID;
    }

// Get modal elements
const modal = document.getElementById("jobModal");
        const openModalBtn = document.getElementById("openModalBtn");
        const closeModalBtn = document.getElementById("closeModalBtn");

        // Open modal when "Add Job" button is clicked
        openModalBtn.addEventListener("click", () => {
            modal.style.display = "flex";
        });

        // Close modal when "x" button is clicked
        closeModalBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // Close modal when clicking outside the modal content
        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
