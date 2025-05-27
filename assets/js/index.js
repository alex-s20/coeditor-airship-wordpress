document.addEventListener("DOMContentLoaded", function () {
	const tabs = document.querySelectorAll(".coeditor-airship__nav-tab");
	const tabPanes = document.querySelectorAll(".coeditor-airship__tab-pane");

	function activateTab(tabId) {
		// Remove active class from all tabs and panes
		tabs.forEach(tab => tab.classList.remove("nav-tab-active"));
		tabPanes.forEach(pane => pane.classList.remove("active"));

		// Add active class to the selected tab and pane
		let selectedTab = document.querySelector(`.coeditor-airship__nav-tab[href="${tabId}"]`);
		let selectedPane = document.querySelector(tabId);

		if (selectedTab && selectedPane) {
			selectedTab.classList.add("nav-tab-active");
			selectedPane.classList.add("active");

			// Store the selected tab in localStorage for persistence
			localStorage.setItem("activeTab", tabId);
		}
	}

	tabs.forEach(tab => {
		tab.addEventListener("click", function (e) {
			e.preventDefault();
			activateTab(this.getAttribute("href"));
		});
	});

	// Restore the last active tab (if it exists)
	const savedTab = localStorage.getItem("activeTab");
	if (savedTab && document.querySelector(savedTab)) {
		activateTab(savedTab);
	} else {
		activateTab("#tab1"); // Default to first tab if none is saved
	}
});

document.addEventListener("DOMContentLoaded", function () {
	// If confirm delete class exists, on click show a confirm dialog
	const deleteButtons = document.querySelectorAll(".coeditor-airship__delete");
	deleteButtons.forEach(button => {
		button.addEventListener("click", function (e) {
			const confirmation = confirm(
				"Are you sure you want to delete this item? This action cannot be undone."
			);

			if (!confirmation) {
				e.preventDefault();
			} else {
				// Allow default action, then refresh the page after a short delay to allow deletion to complete
				setTimeout(() => {
					location.reload();
				}, 500);
			}
		});
	});
});