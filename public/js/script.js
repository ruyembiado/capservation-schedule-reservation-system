$(document).ready(function () {
    // Sidebar Toggle
    const hamBurger = document.querySelector(".toggle-btn");
    if (hamBurger) {
        hamBurger.addEventListener("click", function () {
            document.querySelector("#sidebar").classList.toggle("expand");
            document.querySelector("nav").classList.toggle("nav-collapse");
        });
    }

    // Initialize DataTable
    if ($("#dataTable1").length) {
        $("#dataTable1").DataTable();
    }

    // Tags Input Functionality
    const memberInput = document.getElementById("members"); // Fixed ID
    const membersContainer = document.getElementById("membersContainer");

    if (memberInput && membersContainer) {
        let members = [];

        // Prevent form submission when pressing Enter inside the input
        memberInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Stops form from submitting

                let memberValue = memberInput.value.trim();
                if (memberValue !== "" && !members.includes(memberValue)) {
                    members.push(memberValue);
                    updateMembers();
                    memberInput.value = ""; 
                }
            }
        });

        function updateMembers() {
            membersContainer.innerHTML = ""; 

            members.forEach((member, index) => {
                let memberElement = document.createElement("span");
                memberElement.classList.add("member-tag", "px-2", "rounded", "bg-light", "text-dark", "d-inline-flex", "align-items-center", "m-1", "p-2");
                memberElement.innerHTML = `${member} <span class="remove-member ms-2 text-danger fw-bold" data-index="${index}" style="cursor:pointer;">&times;</span>`;
                
                // Append the tag
                membersContainer.appendChild(memberElement);

                // Add a hidden input for form submission
                let hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "members[]"; 
                hiddenInput.value = member;
                membersContainer.appendChild(hiddenInput);
            });

            // Add remove event
            document.querySelectorAll(".remove-member").forEach((btn) => {
                btn.addEventListener("click", function () {
                    let index = this.getAttribute("data-index");
                    members.splice(index, 1);
                    updateMembers();
                });
            });
        }
    }
});