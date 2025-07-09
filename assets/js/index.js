document.addEventListener("DOMContentLoaded", function () {
	const tabs = document.querySelectorAll(".coeditor-airship__nav-tab");
	const tabPanes = document.querySelectorAll(".coeditor-airship__tab-pane");

	/**
	 * Activates the specified tab and stores the selection in localStorage
	 * @param {string} tabId - The ID selector of the tab content to activate (e.g., "#main-tab1")
	 */
	function activateTab(tabId) {
		tabs.forEach(tab => tab.classList.remove("nav-tab-active"));
		tabPanes.forEach(pane => pane.classList.remove("active"));

		const selectedTab = document.querySelector(`.coeditor-airship__nav-tab[href="${tabId}"]`);
		const selectedPane = document.querySelector(tabId);

		if (selectedTab && selectedPane) {
			selectedTab.classList.add("nav-tab-active");
			selectedPane.classList.add("active");
			localStorage.setItem("activeTab", tabId);
		}
	}

	// Restore previously active tab on page load (or default to first tab)
	const savedTab = localStorage.getItem("activeTab");
	if (savedTab && document.querySelector(savedTab)) {
		activateTab(savedTab);
	} else {
		const firstTab = tabs[0]?.getAttribute("href");
		if (firstTab) activateTab(firstTab);
	}

	tabs.forEach(tab => {
		tab.addEventListener("click", function (e) {
			e.preventDefault();
			const tabId = this.getAttribute("href");
			activateTab(tabId);

			if (tabId === "#main-tab2" && !window.mainTab2Loaded) {
				loadNotifications();
				window.mainTab2Loaded = true;
			}
		});
	});

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

	/**
	 * Loads the most recent push notifications
	 */
	function loadNotifications() {
		const container = document.querySelector('#main-tab2');
		if (!container) return;

		container.innerHTML = '<p>Loading notifications...</p>';

		fetch(CoeditorAirship.ajaxUrl + '?action=coeditor_airship_get_notifications&nonce=' + CoeditorAirship.nonce)
			.then(res => res.json())
			.then(data => {
				if (!data.success) throw new Error(data.data);

				const list = data.data.pushes || [];
				if (list.length === 0) {
					container.innerHTML = '<p>No notifications found.</p>';
					return;
				}

				const html = list.map(item => `
				<div class="coeditor-airship__notification-card">
					<p><strong>Push ID:</strong> ${item.push_uuid}</p>
					<p><strong>Push Type:</strong> ${item.push_type}</p>
					<p><strong>Sent:</strong> ${new Date(item.push_time).toLocaleString()}</p>					
					<p><strong>Direct Responses:</strong> ${item.direct_responses ?? 0}</p>
					<p><strong>Sends:</strong> ${item.sends ?? 0}</p>
				</div>
			`).join("");

				container.innerHTML = `<div class="coeditor-airship__notification-list">${html}</div>`;
			})
			.catch(err => {
				container.innerHTML = `<p>Error loading notifications: ${err.message}</p>`;
			});
	}

});
