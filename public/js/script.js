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

    // Initialize DataTable
    if ($("#dataTable2").length) {
        $("#dataTable2").DataTable();
    }

    // Initialize DataTable
    if ($("#dataTable3").length) {
        $("#dataTable3").DataTable();
    }

    // Initialize DataTable
    if ($("#dataTable4").length) {
        $("#dataTable4").DataTable();
    }

    const selectPanelists = document.getElementById("panelists");
    const panelistCards = document.querySelectorAll(".panelist-card");

    panelistCards.forEach(card => {
        card.addEventListener("click", function () {
            const panelistId = this.dataset.id;
            const option = selectPanelists.querySelector(`option[value="${panelistId}"]`);

            if (option) {
                option.selected = !option.selected;
                this.classList.toggle("bg-selected-panelist");
            }
        });
    });

    const vacantTimeRepeater = $("#vacantTimeRepeater");
    const addVacantTimeBtn = $("#addVacantTimeBtn");

    addVacantTimeBtn.on("click", function () {
        let newItem = `
            <div class="input-group mb-2 vacant-time-item">
                <select name="vacant_time[day][]" class="form-select">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
                <input type="time" name="vacant_time[start_time][]" class="form-control">
                <input type="time" name="vacant_time[end_time][]" class="form-control">
                <button type="button" class="btn btn-danger remove-vacant-time">x</button>
            </div>
        `;
        vacantTimeRepeater.append(newItem);
    });

    vacantTimeRepeater.on("click", ".remove-vacant-time", function () {
        $(this).closest(".vacant-time-item").remove();
    });

    const $selectBox = $("#custom-select");
    const $dropdown = $("#custom-dropdown");
    const $hiddenSelect = $("#instructor");

    // Add search input inside dropdown
    const $searchInput = $("<input>", {
        type: "text",
        class: "custom-search form-control",
        placeholder: "Search..."
    });

    $dropdown.prepend($searchInput);

    // Load instructors from JSON API
    function loadInstructors() {
        $.ajax({
            url: "/get-instructors",
            method: "GET",
            dataType: "json",
            success: function (response) {
                $dropdown.find("div[data-value]").remove(); // Clear old options

                if (response.length === 0) {
                    $dropdown.append('<div class="no-results">No instructors found</div>');
                } else {
                    $.each(response, function (index, instructor) {
                        let isSelected = $hiddenSelect.val() == instructor.id ? "selected" : "";
                        $dropdown.append(
                            `<div data-value="${instructor.id}" class="dropdown-item ${isSelected}">${instructor.name}</div>`
                        );
                    });

                    // Set the selected instructor on page load
                    let oldValue = $hiddenSelect.val();
                    if (oldValue) {
                        let selectedText = $dropdown.find(`div[data-value="${oldValue}"]`).text();
                        if (selectedText) {
                            $selectBox.text(selectedText);
                        }
                    }
                }
            }
        });
    }

    // Load instructors on page load
    loadInstructors();

    // Toggle dropdown visibility
    $selectBox.on("click", function () {
        $dropdown.toggle();
        $searchInput.focus(); // Auto-focus on search input when opening dropdown
    });

    // Handle selection
    $dropdown.on("click", "div[data-value]", function () {
        let selectedValue = $(this).data("value");
        let selectedText = $(this).text();

        $selectBox.text(selectedText);
        $hiddenSelect.val(selectedValue); // Update hidden input
        $dropdown.hide();
    });

    // Close dropdown when clicking outside
    $(document).on("click", function (e) {
        if (!$selectBox.is(e.target) && !$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
            $dropdown.hide();
        }
    });

    // Search functionality
    $searchInput.on("keyup", function (e) {
        let searchText = $(this).val().toLowerCase();
        let $options = $dropdown.find("div[data-value]");

        $options.each(function () {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchText));
        });

        // Select first visible item on Enter key
        if (e.key === "Enter") {
            let firstVisibleItem = $options.filter(":visible").first();
            if (firstVisibleItem.length) {
                firstVisibleItem.click();
            }
        }
    });

    // Keyboard accessibility: Close dropdown on Esc key
    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            $dropdown.hide();
        }
    });

    $('#reserve_group').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Select a group",
        allowClear: true
    });

    $('#select_instructor').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Select instructor",
        allowClear: true
    });

    $('#group_schedule').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Select a group",
        allowClear: true
    });

    const credentialsRepeater = document.getElementById("credentialsRepeater");
    const addCredentialBtn = document.getElementById("addCredentialBtn");

    if (!credentialsRepeater || !addCredentialBtn) {
        return;
    }

    addCredentialBtn.addEventListener("click", function () {
        let newItem = document.createElement("div");
        newItem.classList.add("input-group", "mb-2", "credential-item");
        newItem.innerHTML = `
            <input type="text" name="credentials[]" class="form-control" placeholder="Enter credential">
            <button type="button" class="btn btn-danger remove-credential">x</button>
        `;
        credentialsRepeater.appendChild(newItem);
    });

    credentialsRepeater.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-credential")) {
            e.target.closest(".credential-item").remove();
        }
    });
});