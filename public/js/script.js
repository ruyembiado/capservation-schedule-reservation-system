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

    // Members Input Functionality
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

    var calendarEl = $('#FullCalendar')[0];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        validRange: {
            start: null
        },
        selectAllow: function (selectInfo) {
            return selectInfo.start >= new Date().setHours(0, 0, 0, 0);
        },
        dateClick: function (info) {
            if (new Date(info.dateStr) >= new Date().setHours(0, 0, 0, 0)) {
                $('input#event-date').val(info.dateStr);
            }
        },
        dayCellDidMount: function (info) {
            let today = new Date().setHours(0, 0, 0, 0);
            let cellDate = new Date(info.date).setHours(0, 0, 0, 0);

            if (cellDate < today) {
                info.el.style.backgroundColor = "#f8d7da"; 
                info.el.style.color = "#6c757d"; 
                info.el.style.pointerEvents = "none"; 
                info.el.style.opacity = "0.6";
            }
        }
    });
    calendar.render();

    // $('#instructorSelect2').select2({
    //     ajax: {
    //         url: '/get-instructors',
    //         dataType: 'json',
    //         delay: 250,
    //         data: function (params) {
    //             return {
    //                 search: params.term 
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data.map(item => ({
    //                     id: item.id,
    //                     text: item.name
    //                 }))
    //             };
    //         }
    //     },
    //     placeholder: "Search for an instructor",
    //     minimumInputLength: 2,
    //     allowClear: true
    // });

});