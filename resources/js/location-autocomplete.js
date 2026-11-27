const autocompleteInputs = document.querySelectorAll('[data-location-autocomplete]');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function setFieldValue(form, selector, value) {
    if (value === null || value === undefined || value === '') {
        return;
    }

    const field = form?.querySelector(selector);
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
    let sessionToken = null;

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

    function appendAttribution() {
        const attribution = document.createElement('div');
        const text = document.createElement('span');

        attribution.className = 'location-suggestion-attribution';
        text.textContent = 'Google Maps';
        text.setAttribute('translate', 'no');
        attribution.append(text);
        listbox.append(attribution);
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

        appendAttribution();
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

    async function selectSuggestion(suggestion) {
        if (!isAddressField) {
            input.value = suggestion.text || suggestion.mainText;
            closeSuggestions();
            form?.requestSubmit();
            return;
        }

        closeSuggestions();
        status.textContent = 'Loading the selected address.';

        try {
            const response = await fetch(input.dataset.detailsUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    place_id: suggestion.placeId,
                    session_token: sessionToken,
                }),
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Unable to load this address.');
            }

            input.value = result.formattedAddress || suggestion.text;
            setFieldValue(form, '[name="address_line_1"]', result.addressLine1);
            setFieldValue(form, '[data-address-city], [name="city"]', result.city);
            setFieldValue(form, '[data-address-state], [name="state"]', result.state);
            setFieldValue(form, '[data-address-zip], [name="zip_code"]', result.zipCode);
            setFieldValue(form, '[data-address-county], [name="county"]', result.county);
            setFieldValue(form, '[data-address-country], [name="country"]', result.country);
            setFieldValue(form, '[data-address-latitude], [name="latitude"]', result.latitude);
            setFieldValue(form, '[data-address-longitude], [name="longitude"]', result.longitude);
            sessionToken = null;
            status.textContent = 'Address fields filled from the selected Google Maps result.';
        } catch (error) {
            sessionToken = null;
            showMessage(error.message || 'Unable to load this address.');
            status.textContent = error.message || 'Unable to load this address.';
        }
    }

    async function loadSuggestions(query) {
        activeRequest?.abort();
        activeRequest = new AbortController();
        status.textContent = 'Searching Google Maps locations.';

        const url = new URL(input.dataset.suggestionsUrl, window.location.origin);
        url.searchParams.set('input', query);
        url.searchParams.set('mode', isAddressField ? 'address' : 'search');
        if (isAddressField) {
            url.searchParams.set('session_token', sessionToken);
        }

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
            sessionToken = null;
            return;
        }

        if (isAddressField && !sessionToken) {
            sessionToken = crypto.randomUUID();
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
