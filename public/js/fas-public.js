document.addEventListener('DOMContentLoaded', () => { 
    let debounceTimer; 
    let abortController = null;

    const searchOverlay = document.querySelector('.fas-search-overlay');
    const searchInput = document.querySelector('.fas-search-input'); 
    
    // Read dynamic settings passed from PHP via wp_localize_script
    const currentLang = (typeof fas_params !== 'undefined' && fas_params.lang) ? fas_params.lang : (document.documentElement.lang || 'fa');
    const ajaxUrl = (typeof fas_params !== 'undefined' && fas_params.ajax_url) ? fas_params.ajax_url : '/wp-json/fas/v1/search';
    const tabsOrder = (typeof fas_params !== 'undefined' && fas_params.tabs_order) ? fas_params.tabs_order : ['all', 'products', 'posts', 'docs'];
    const i18n = (typeof fas_params !== 'undefined' && fas_params.i18n) ? fas_params.i18n : {
        placeholder: 'Search products, articles, docs...',
        no_results: 'No results found',
        searching: 'Searching...'
    };

    // Modal Interaction
    const triggerButtons = document.querySelectorAll('.fas-search-trigger');
    const closeButton = document.querySelector('.fas-modal-close');
    const tabButtons = document.querySelectorAll('.fas-tab-btn');
    const tabContents = document.querySelectorAll('.fas-tab-content');

    const openModal = () => {
        if (searchOverlay) {
            searchOverlay.classList.add('is-open');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
            setTimeout(() => {
                if (searchInput) searchInput.focus();
            }, 100);
        }
    };

    const closeModal = () => {
        if (searchOverlay) {
            searchOverlay.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    };

    triggerButtons.forEach(btn => btn.addEventListener('click', (e) => {
        e.preventDefault();
        openModal();
    }));

    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    if (searchOverlay) {
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) {
                closeModal();
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Tab Switching Logic (supporting custom accent colors dynamically)
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            const accentColor = btn.getAttribute('data-accent-color') || '#0066cc';
            
            tabButtons.forEach(b => {
                b.classList.remove('is-active');
                // Reset text/border colors for inactive tabs
                b.style.color = '';
                b.style.borderBottomColor = '';
                const subIcon = b.querySelector('.dashicons');
                if (subIcon) subIcon.style.color = '';
            });

            tabContents.forEach(c => c.classList.remove('is-active'));

            btn.classList.add('is-active');
            btn.style.color = accentColor;
            btn.style.borderBottomColor = accentColor;
            const activeIcon = btn.querySelector('.dashicons');
            if (activeIcon) activeIcon.style.color = accentColor;

            const targetContent = document.getElementById(`fas-tab-${targetTab}`);
            if (targetContent) {
                targetContent.classList.add('is-active');
            }
        });
    });

    if (!searchInput) return; 
 
    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer); 
        const query = e.target.value.trim(); 
 
        if (query.length < 3) { 
            // Reset results container to default state if query is empty or too short
            clearFasResults();
            if (abortController) {
                abortController.abort();
                abortController = null;
            }
            return; 
        } 
 
        // Show searching message in current active tab content
        showFasSearching();

        debounceTimer = setTimeout(() => { 
            // Abort previous search request if one exists
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            // Handle URL format gracefully, support both pretty permalinks and default parameters (?rest_route=)
            const querySeparator = ajaxUrl.includes('?') ? '&' : '?';
            const requestUrl = `${ajaxUrl}${querySeparator}s=${encodeURIComponent(query)}&lang=${encodeURIComponent(currentLang)}`;

            fetch(requestUrl, { signal: abortController.signal }) 
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                }) 
                .then(data => { 
                    renderFasResults(data); 
                }) 
                .catch(err => {
                    if (err.name === 'AbortError') {
                        return;
                    }
                    console.error('Search failed:', err);
                    showFasError();
                }); 
        }, 300); // 300ms Debounce 
    }); 

    function clearFasResults() {
        tabContents.forEach(content => {
            content.innerHTML = '';
        });
    }

    function showFasSearching() {
        tabContents.forEach(content => {
            content.innerHTML = `<div class="fas-status-message">${i18n.searching}</div>`;
        });
    }

    function showFasError() {
        tabContents.forEach(content => {
            content.innerHTML = `<div class="fas-status-message">Error retrieving search results. Please try again.</div>`;
        });
    }

    function renderFasResults(data) { 
        // Populate results under each custom ordered tab
        tabsOrder.forEach(cat => {
            const container = document.getElementById(`fas-tab-${cat}`);
            if (!container) return;

            const items = data[cat] || [];
            if (items.length === 0) {
                container.innerHTML = `<div class="fas-status-message">${i18n.no_results}</div>`;
                return;
            }

            // Securely render pre-built safe HTML from the server (preventing Client-side XSS and enabling overridable templates)
            let html = '';
            items.forEach(item => {
                if (item.html) {
                    html += item.html;
                }
            });

            container.innerHTML = html;
        });
    }
});
