document.addEventListener("DOMContentLoaded", function () {
	const tabs = document.querySelectorAll(".coeditor-airship__nav-tab");
	const tabPanes = document.querySelectorAll(".coeditor-airship__tab-pane");

	function activateTab(tabId) {
		// Remove active class from all tabs and panes
		tabs.forEach(tab => tab.classList.remove("nav-tab-active"));
		tabPanes.forEach(pane => pane.classList.remove("active"));

		// Add active class to the selected tab and pane
		const selectedTab = document.querySelector(`.coeditor-airship__nav-tab[href="${tabId}"]`);
		const selectedPane = document.querySelector(tabId);

		if (selectedTab && selectedPane) {
			selectedTab.classList.add("nav-tab-active");
			selectedPane.classList.add("active");

			// Store the selected tab in localStorage
			localStorage.setItem("activeTab", tabId);
		}
	}

	// Tab click event listeners
	tabs.forEach(tab => {
		tab.addEventListener("click", function (e) {
			e.preventDefault();
			activateTab(this.getAttribute("href"));
		});
	});

	// Restore the last active tab or default to #tab1
	const savedTab = localStorage.getItem("activeTab");
	if (savedTab && document.querySelector(savedTab)) {
		activateTab(savedTab);
	} else {
		activateTab("#tab1");
	}

	// Confirm delete logic
	const deleteButtons = document.querySelectorAll(".coeditor-airship__delete");
	deleteButtons.forEach(button => {
		button.addEventListener("click", function (e) {
			const confirmation = confirm(
				"Are you sure you want to delete this item? This action cannot be undone."
			);

			if (!confirmation) {
				e.preventDefault();
			} else {
				setTimeout(() => {
					location.reload();
				}, 500);
			}
		});
	});
});
