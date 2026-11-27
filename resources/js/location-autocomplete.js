const autocompleteInputs = document.querySelectorAll('[data-location-autocomplete]');

function setFieldValue(form, selector, value) {
    if (value === null || value === undefined || value === '') {
        return;
    }

    const field = form.querySelector(selector);
    if (!field) {
        return;
    }

    if (field instanceof HTMLSelectElement) {
        const normalizedValue = String(value).toLowerCase();
        const option = Array.from(field.options).find((item) => (
            item.value.toLowerCase() === normalizedValue
            || item.textContent.toLowerCase().includes(normalizedValue)
        ));
        if (!option) {
            return;
        }
        field.value = option.value;
    } else {
        field.value = value;
    }

    field.dispatchEvent(new Event('change', { bubbles: true }));
}

function initializeAutocomplete(input, inputIndex) {
    const isAddressField = input.dataset.locationAutocomplete === 'address';
    const form = input.closest('form');
    const listbox = document.createElement('div');
    const status = document.createElement('div');
    let suggestions = [];
    let activeIndex = -1;
    let debounceTimeout;
    let activeRequest;

    listbox.className = 'location-suggestions';
    listbox.id = `location-list-${inputIndex}`;
    listbox.setAttribute('role', 'listbox');
    listbox.hidden = true;
    input.insertAdjacentElement('afterend', listbox);

    status.className = 'visually-hidden';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    listbox.insertAdjacentElement('afterend', status);

    input.setAttribute('autocomplete', 'off');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-controls', listbox.id);
    input.setAttribute('aria-expanded', 'false');

    function closeSuggestions() {
        listbox.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        suggestions = [];
        activeIndex = -1;
    }

    function showMessage(message) {
        listbox.replaceChildren();
        const messageRow = document.createElement('div');
        messageRow.className = 'location-suggestion-message';
        messageRow.textContent = message;
        listbox.append(messageRow);
        listbox.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    }

    function renderSuggestions(items) {
        suggestions = items;
        activeIndex = -1;
        listbox.replaceChildren();

        if (suggestions.length === 0) {
            closeSuggestions();
            return;
        }

        suggestions.forEach((suggestion, index) => {
            const option = document.createElement('button');
            const mainText = document.createElement('span');
            const secondaryText = document.createElement('span');

            option.type = 'button';
            option.className = 'location-suggestion-option';
            option.id = `${listbox.id}-option-${index}`;
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');

            mainText.className = 'location-suggestion-main';
            mainText.textContent = suggestion.mainText || suggestion.text;
            option.append(mainText);

            if (suggestion.secondaryText) {
                secondaryText.className = 'location-suggestion-secondary';
                secondaryText.textContent = suggestion.secondaryText;
                option.append(secondaryText);
            }

            option.addEventListener('mousedown', (event) => {
                event.preventDefault();
            });
            option.addEventListener('click', () => selectSuggestion(suggestion));

            listbox.append(option);
        });

        const attribution = document.createElement('div');
        attribution.className = 'location-suggestion-attribution';
        attribution.innerHTML = [
            '<a href="https://photon.komoot.io/" target="_blank" rel="noreferrer">Search by Photon</a>',
            '<span aria-hidden="true">·</span>',
            '<a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">© OpenStreetMap contributors</a>',
        ].join(' ');
        listbox.append(attribution);
        listbox.hidden = false;
        input.setAttribute('aria-expanded', 'true');
        status.textContent = `${suggestions.length} location suggestions available.`;
    }

    function setActiveOption(nextIndex) {
        const options = listbox.querySelectorAll('[role="option"]');
        if (options.length === 0) {
            return;
        }

        activeIndex = (nextIndex + options.length) % options.length;
        options.forEach((option, index) => {
            const isActive = index === activeIndex;
            option.setAttribute('aria-selected', String(isActive));
            option.classList.toggle('is-active', isActive);
        });

        input.setAttribute(
            'aria-activedescendant',
            `${listbox.id}-option-${activeIndex}`,
        );
    }

    function selectSuggestion(suggestion) {
        input.value = suggestion.text || suggestion.mainText;
        closeSuggestions();

        if (!isAddressField) {
            form?.requestSubmit();
            return;
        }

        setFieldValue(form, '[name="address_line_1"]', suggestion.addressLine1);
        setFieldValue(form, '[data-address-city], [name="city"]', suggestion.city);
        setFieldValue(form, '[data-address-state], [name="state"]', suggestion.state);
        setFieldValue(form, '[data-address-zip], [name="zip_code"]', suggestion.zipCode);
        setFieldValue(form, '[data-address-county], [name="county"]', suggestion.county);
        setFieldValue(form, '[data-address-country], [name="country"]', suggestion.country);
        setFieldValue(form, '[data-address-latitude], [name="latitude"]', suggestion.latitude);
        setFieldValue(form, '[data-address-longitude], [name="longitude"]', suggestion.longitude);
        status.textContent = 'Address fields filled from the selected location.';
    }

    async function loadSuggestions(query) {
        activeRequest?.abort();
        activeRequest = new AbortController();
        status.textContent = 'Searching OpenStreetMap locations.';

        const url = new URL(input.dataset.suggestionsUrl, window.location.origin);
        url.searchParams.set('input', query);

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
                signal: activeRequest.signal,
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Location suggestions are unavailable.');
            }

            renderSuggestions(result.suggestions || []);
        } catch (error) {
            if (error.name !== 'AbortError') {
                showMessage(error.message || 'Location suggestions are unavailable.');
                status.textContent = error.message || 'Location suggestions are unavailable.';
            }
        }
    }

    input.addEventListener('input', () => {
        window.clearTimeout(debounceTimeout);
        activeRequest?.abort();

        const query = input.value.trim();
        if (query.length < 3) {
            closeSuggestions();
            status.textContent = '';
            return;
        }

        debounceTimeout = window.setTimeout(() => loadSuggestions(query), 300);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' && !listbox.hidden) {
            event.preventDefault();
            setActiveOption(activeIndex + 1);
        } else if (event.key === 'ArrowUp' && !listbox.hidden) {
            event.preventDefault();
            const optionCount = listbox.querySelectorAll('[role="option"]').length;
            setActiveOption(activeIndex < 0 ? optionCount - 1 : activeIndex - 1);
        } else if (event.key === 'Enter' && activeIndex >= 0) {
            event.preventDefault();
            selectSuggestion(suggestions[activeIndex]);
        } else if (event.key === 'Escape') {
            closeSuggestions();
        }
    });

    document.addEventListener('mousedown', (event) => {
        if (event.target !== input && !listbox.contains(event.target)) {
            closeSuggestions();
        }
    });
}

autocompleteInputs.forEach(initializeAutocomplete);
