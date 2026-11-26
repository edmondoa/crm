const autocompleteInputs = document.querySelectorAll('[data-google-places]');

const makeSessionToken = () => crypto.randomUUID();

function setFieldValue(form, selector, value) {
    if (value === null || value === undefined || value === '') {
        return;
    }

    const field = form.querySelector(selector);
    if (!field) {
        return;
    }

    if (field instanceof HTMLSelectElement) {
        const option = Array.from(field.options).find(
            (item) => item.value.toLowerCase() === String(value).toLowerCase(),
        );
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
    const isAddressField = input.dataset.googlePlaces === 'address';
    const form = input.closest('form');
    const listbox = document.createElement('div');
    const status = document.createElement('div');
    let suggestions = [];
    let activeIndex = -1;
    let sessionToken = null;
    let debounceTimeout;
    let activeRequest;

    listbox.className = 'google-place-suggestions';
    listbox.id = `google-place-list-${inputIndex}`;
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
        messageRow.className = 'google-place-message';
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
            option.className = 'google-place-option';
            option.id = `${listbox.id}-option-${index}`;
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');

            mainText.className = 'google-place-main';
            mainText.textContent = suggestion.mainText || suggestion.text;
            option.append(mainText);

            if (suggestion.secondaryText) {
                secondaryText.className = 'google-place-secondary';
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
        const logo = document.createElement('img');
        attribution.className = 'google-place-attribution';
        logo.src = 'https://maps.gstatic.com/mapfiles/api-3/images/powered-by-google-on-white3.png';
        logo.alt = 'Powered by Google';
        logo.width = 120;
        logo.height = 14;
        logo.loading = 'eager';
        attribution.append(logo);
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

    async function selectSuggestion(suggestion) {
        input.value = suggestion.text || suggestion.mainText;
        closeSuggestions();

        if (!isAddressField) {
            sessionToken = null;
            form?.requestSubmit();
            return;
        }

        status.textContent = 'Loading the selected address.';

        try {
            const url = new URL(input.dataset.detailsUrl, window.location.origin);
            url.searchParams.set('placeId', suggestion.placeId);
            url.searchParams.set('sessionToken', sessionToken || makeSessionToken());

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
            });
            const details = await response.json();

            if (!response.ok) {
                throw new Error(details.message || 'Address details are unavailable.');
            }

            setFieldValue(form, '[name="address_line_1"]', details.addressLine1);
            setFieldValue(form, '[data-address-city], [name="city"]', details.city);
            setFieldValue(form, '[data-address-state], [name="state"]', details.state);
            setFieldValue(form, '[data-address-zip], [name="zip_code"]', details.zipCode);
            setFieldValue(form, '[data-address-country], [name="country"]', details.country);
            setFieldValue(form, '[data-address-latitude], [name="latitude"]', details.latitude);
            setFieldValue(form, '[data-address-longitude], [name="longitude"]', details.longitude);
            status.textContent = 'Address fields filled from the selected location.';
        } catch (error) {
            status.textContent = error.message || 'Address details are unavailable.';
        } finally {
            sessionToken = null;
        }
    }

    async function loadSuggestions(query) {
        activeRequest?.abort();
        activeRequest = new AbortController();
        sessionToken ??= makeSessionToken();
        status.textContent = 'Searching Google Maps.';

        const url = new URL(input.dataset.suggestionsUrl, window.location.origin);
        url.searchParams.set('input', query);
        url.searchParams.set('sessionToken', sessionToken);

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
            setActiveOption(activeIndex - 1);
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
