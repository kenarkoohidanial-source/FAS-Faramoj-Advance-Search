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

    const trackClickUrl = ajaxUrl.replace('/search', '/track-click');

    // Modal Interaction
    const triggerButtons = document.querySelectorAll('.fas-search-trigger');
    const closeButton = document.querySelector('.fas-modal-close');
    const voiceButton = document.querySelector('.fas-voice-search-btn');
    const tabButtons = document.querySelectorAll('.fas-tab-btn');
    const tabContents = document.querySelectorAll('.fas-tab-content');
    const historyContainer = document.querySelector('.fas-search-history');
    
    // History Logic
    const getHistory = () => {
        try {
            const history = localStorage.getItem('fas_search_history');
            return history ? JSON.parse(history) : [];
        } catch (e) {
            return [];
        }
    };
    
    const saveHistory = (term) => {
        if (!term || term.length < 3) return;
        let history = getHistory();
        history = history.filter(t => t.toLowerCase() !== term.toLowerCase());
        history.unshift(term);
        const maxHistory = (typeof fas_params !== 'undefined' && fas_params.history_count) ? parseInt(fas_params.history_count, 10) : 5;
        if (history.length > maxHistory) history.pop(); // keep dynamic limit
        try {
            localStorage.setItem('fas_search_history', JSON.stringify(history));
        } catch (e) {}
    };

    const clearHistory = () => {
        try {
            localStorage.removeItem('fas_search_history');
            renderHistory();
        } catch (e) {}
    };

    const removeHistoryItem = (term) => {
        let history = getHistory();
        history = history.filter(t => t !== term);
        try {
            localStorage.setItem('fas_search_history', JSON.stringify(history));
            renderHistory();
        } catch (e) {}
    };

    const renderHistory = () => {
        if (!historyContainer) return;
        const history = getHistory();
        if (history.length === 0) {
            historyContainer.style.display = 'none';
            return;
        }
        
        let titleText = currentLang === 'fa' ? 'تاریخچه جستجو' : 'Search History';
        let clearText = currentLang === 'fa' ? 'پاک کردن' : 'Clear';
        
        let html = `
            <div class="fas-search-history-header">
                <span class="fas-search-history-title">${titleText}</span>
                <button type="button" class="fas-search-history-clear">${clearText}</button>
            </div>
            <div class="fas-search-history-items">
        `;
        
        html += `</div>`;
        historyContainer.innerHTML = html;
        
        // Use textContent to prevent self-XSS when inserting history terms
        const itemsContainer = historyContainer.querySelector('.fas-search-history-items');
        itemsContainer.innerHTML = ''; // clear initial safe string
        
        history.forEach(term => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'fas-search-history-item';
            
            const textSpan = document.createElement('span');
            textSpan.className = 'fas-search-history-item-text';
            textSpan.textContent = term;
            
            const removeBtn = document.createElement('span');
            removeBtn.className = 'fas-search-history-item-remove';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = currentLang === 'fa' ? 'حذف' : 'Remove';
            
            // Handle individual remove click
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // prevent clicking the parent button
                removeHistoryItem(term);
            });

            btn.appendChild(textSpan);
            btn.appendChild(removeBtn);
            
            // Handle clicking the item to search
            btn.addEventListener('click', (e) => {
                if (searchInput) {
                    searchInput.value = term;
                    searchInput.dispatchEvent(new Event('input'));
                }
            });

            itemsContainer.appendChild(btn);
        });

        historyContainer.style.display = 'block';

        // Bind clear button
        historyContainer.querySelector('.fas-search-history-clear').addEventListener('click', clearHistory);
    };

    // Force placeholder translation dynamically via JavaScript
    if (searchInput && i18n.placeholder) {
        searchInput.setAttribute('placeholder', i18n.placeholder);
    }

    const openModal = () => {
        if (searchOverlay) {
            searchOverlay.classList.add('is-open');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
            if (searchInput && searchInput.value.trim().length === 0) {
                renderHistory();
            }
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

    let activeRecognition = null;
    let audioContext = null;
    let microphoneStream = null;
    let reqAnimFrameId = null;

    const stopAudioContext = () => {
        if (reqAnimFrameId) cancelAnimationFrame(reqAnimFrameId);
        if (microphoneStream) {
            microphoneStream.getTracks().forEach(track => track.stop());
            microphoneStream = null;
        }
        if (audioContext && audioContext.state !== 'closed') {
            audioContext.close();
            audioContext = null;
        }
        if (voiceButton) {
            voiceButton.classList.remove('fas-listening', 'fas-fallback-pulse');
            voiceButton.style.setProperty('--volume-scale', 1);
            voiceButton.style.setProperty('--volume-opacity', 0);
        }
    };

    const startAudioContext = async () => {
        try {
            microphoneStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const analyser = audioContext.createAnalyser();
            const microphone = audioContext.createMediaStreamSource(microphoneStream);
            microphone.connect(analyser);
            analyser.fftSize = 256;
            const bufferLength = analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);

            const checkVolume = () => {
                if (!audioContext) return;
                analyser.getByteFrequencyData(dataArray);
                let sum = 0;
                for (let i = 0; i < bufferLength; i++) {
                    sum += dataArray[i];
                }
                let average = sum / bufferLength;

                // Map average (0-100 typical speaking range) to scale (1 to 1.6)
                let scale = 1 + (average / 100) * 0.6;
                // Map opacity: higher volume = more opaque
                let opacity = 0.4 + (average / 100) * 0.5;

                // Cap values
                scale = Math.min(Math.max(scale, 1), 1.6);
                opacity = Math.min(Math.max(opacity, 0.2), 0.9);

                voiceButton.style.setProperty('--volume-scale', scale);
                voiceButton.style.setProperty('--volume-opacity', opacity);

                reqAnimFrameId = requestAnimationFrame(checkVolume);
            };
            checkVolume();
        } catch (err) {
            console.warn('Audio visualization not available (microphone permission or context issue). Falling back to CSS pulse.', err);
            if (voiceButton) {
                voiceButton.classList.add('fas-fallback-pulse');
            }
        }
    };

    if (voiceButton) {
        voiceButton.addEventListener('click', () => {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                alert(currentLang === 'fa' ? 'مرورگر شما از جستجوی صوتی پشتیبانی نمی‌کند.' : 'Your browser does not support Voice Search. Please use a modern browser like Chrome or Safari.');
                return;
            }

            if (voiceButton.classList.contains('fas-listening')) {
                if (activeRecognition) {
                    activeRecognition.stop();
                }
                stopAudioContext();
                return;
            }

            try {
                const recognition = new SpeechRecognition();
                activeRecognition = recognition;

                // Fallback for Safari/iOS that strongly prefers explicit basic language codes sometimes
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                if (currentLang === 'fa') {
                    recognition.lang = isIOS ? 'fa' : 'fa-IR';
                } else {
                    recognition.lang = 'en-US';
                }

                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                recognition.onstart = function() {
                    voiceButton.classList.add('fas-listening');
                    startAudioContext();
                };

                recognition.onresult = function(event) {
                    const speechResult = event.results[0][0].transcript;
                    if (searchInput) {
                        searchInput.value = speechResult;
                        searchInput.dispatchEvent(new Event('input'));
                    }
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error:', event.error);
                    stopAudioContext();
                    if (event.error === 'not-allowed') {
                        alert(currentLang === 'fa' ? 'دسترسی به میکروفون رد شد. لطفاً اجازه دسترسی را در مرورگر خود بدهید.' : 'Microphone access denied. Please allow microphone access in your browser settings.');
                    } else if (event.error === 'language-not-supported') {
                        alert(currentLang === 'fa' ? 'زبان فارسی در جستجوی صوتی این مرورگر پشتیبانی نمی‌شود (در آیفون کیبورد فارسی و قابلیت Dictation را فعال کنید).' : 'Language not supported for voice search on this device.');
                    } else if (event.error !== 'no-speech') {
                        alert(currentLang === 'fa' ? 'خطای جستجوی صوتی: ' + event.error : 'Voice search error: ' + event.error);
                    }
                };

                recognition.onend = function() {
                    stopAudioContext();
                    activeRecognition = null;
                };

                recognition.start();
            } catch (error) {
                console.error('Voice search failed to start:', error);
                stopAudioContext();
                activeRecognition = null;
                alert(currentLang === 'fa' ? 'خطا در اجرای جستجوی صوتی مرورگر: ' + error.message : 'Error starting voice search: ' + error.message);
            }
        });
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
            if (query.length === 0) {
                renderHistory();
            }
            return; 
        } 
 
        if (historyContainer) {
            historyContainer.style.display = 'none';
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
                    
                    // Save to history if any results were found
                    if (data.all && data.all.length > 0) {
                        saveHistory(query);
                    }
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
                let noResHtml = `<div class="fas-status-message">${i18n.no_results}</div>`;
                if (data.did_you_mean && cat === 'all') {
                    let dymText = currentLang === 'fa' ? 'آیا منظور شما این است:' : 'Did you mean:';
                    noResHtml += `<div class="fas-did-you-mean" style="text-align: center; margin-top: -20px; font-size: 14px; color: var(--fas-text-muted);">
                        ${dymText} <a href="#" class="fas-dym-link" style="color: var(--fas-primary); font-weight: 600; text-decoration: none;">${data.did_you_mean}</a>
                    </div>`;
                }
                container.innerHTML = noResHtml;
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

        // Attach Did You Mean click handler
        const dymLinks = document.querySelectorAll('.fas-dym-link');
        dymLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                if (searchInput) {
                    searchInput.value = e.target.innerText;
                    searchInput.dispatchEvent(new Event('input'));
                }
            });
        });

        // Attach click tracking to newly rendered result items
        const resultItems = document.querySelectorAll('.fas-result-item');
        resultItems.forEach(item => {
            item.addEventListener('click', (e) => {
                const postId = item.getAttribute('data-post-id');
                const postTitle = item.getAttribute('data-post-title');
                const term = searchInput ? searchInput.value.trim() : '';
                
                if (postId && postId !== '0') {
                    // Fire and forget fetch request with keepalive so it's not cancelled by navigation
                    fetch(trackClickUrl, {
                        method: 'POST',
                        keepalive: true,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `post_id=${encodeURIComponent(postId)}&title=${encodeURIComponent(postTitle)}&term=${encodeURIComponent(term)}`
                    }).catch(() => {});
                }
            });
        });
    }
});
