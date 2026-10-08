document.addEventListener('DOMContentLoaded', function () {

    const customerSelect =
        document.getElementById('customer_id');

    const contactSelect =
        document.getElementById('contact_id');

    const propertySelect =
        document.getElementById('property_id');

    const locationSelect =
        document.getElementById('property_location_id');


    /*
    |--------------------------------------------------------------------------
    | Customer → Contacts
    |--------------------------------------------------------------------------
    */

    if (customerSelect && contactSelect) {

        customerSelect.addEventListener(
            'change',
            function () {

                const customerId =
                    this.value;

                contactSelect.innerHTML =
                    '<option value="">Select contact</option>';

                if (!customerId) {
                    return;
                }

                fetch(
                    `/crm/customers/${customerId}/contacts`
                )
                    .then(response => response.json())
                    .then(contacts => {

                        contacts.forEach(contact => {

                            const option =
                                document.createElement('option');

                            option.value =
                                contact.id;

                            option.textContent =
                                contact.name +
                                (
                                    contact.job_title
                                        ? ` — ${contact.job_title}`
                                        : ''
                                );

                            contactSelect.appendChild(
                                option
                            );
                        });
                    })
                    .catch(error => {

                        console.error(
                            'Unable to load contacts:',
                            error
                        );

                    });
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Property → Locations
    |--------------------------------------------------------------------------
    */

    if (propertySelect && locationSelect) {

        propertySelect.addEventListener(
            'change',
            function () {

                const propertyId =
                    this.value;

                locationSelect.innerHTML =
                    '<option value="">Select location</option>';

                if (!propertyId) {
                    return;
                }

                fetch(
                    `/crm/property-locations-by-property/${propertyId}`
                )
                    .then(response => response.json())
                    .then(locations => {

                        locations.forEach(location => {

                            const option =
                                document.createElement('option');

                            option.value =
                                location.id;

                            option.textContent =
                                `${location.location_name} — ${location.city}, ${location.state}`;

                            locationSelect.appendChild(
                                option
                            );
                        });
                    })
                    .catch(error => {

                        console.error(
                            'Unable to load locations:',
                            error
                        );

                    });
            }
        );
    }

});