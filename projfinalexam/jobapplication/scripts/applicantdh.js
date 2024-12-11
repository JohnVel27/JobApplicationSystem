
        function showContent(sectionId) {
            document.querySelectorAll('.main-content').forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }



        function showSection(sectionId) {
            document.querySelectorAll('.main-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');
        }
   

        

        function openModal(jobID, jobTitle, jobCategory, jobLocation, companyName, jobDescription, salaryRange, postingDate, expiryDate, employmentType, status, userID) {
            // Set modal content
            document.getElementById("modalJobID").value = jobID;
            document.getElementById("modalUserID").value = userID;

            document.getElementById("modalJobTitle").textContent = jobTitle;
            document.getElementById("modalJobCategory").textContent = jobCategory;
            document.getElementById("modalJobDescription").textContent = jobDescription;
            document.getElementById("modalCompanyName").textContent = companyName;
            document.getElementById("modalJobLocation").textContent = jobLocation;
            document.getElementById("modalSalaryRange").textContent = salaryRange;
            document.getElementById("modalPostingDate").textContent = postingDate;
            document.getElementById("modalExpiryDate").textContent = expiryDate;
            document.getElementById("modalEmploymentType").textContent = employmentType;
            document.getElementById("modalStatus").textContent = status;

            // Update the Apply Now link with JobID and UserID
            document.getElementById("applyNowLink").href = "jobapplicationform.php?jobID=" + encodeURIComponent(jobID) + "&userID=" + encodeURIComponent(userID);

            // Show modal
            document.getElementById("modalOverlay").style.display = "flex";
        }






        // Function to close the modal
        function closeModal() {
            document.getElementById("modalOverlay").style.display = "none";
        }

