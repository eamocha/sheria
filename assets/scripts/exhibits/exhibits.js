 /**
     * General function to load a modal via AJAX and inject its HTML into the DOM.
     * @param {Object} options - Configuration options:
     *   url: (string) Required. The AJAX URL to fetch modal HTML.
     *   modalContainerId: (string) Optional. The container ID for the modal (default: 'dynamic-modal-container').
     *   data: (object) Optional. Data to send with the AJAX request.
     *   method: (string) Optional. HTTP method ('GET' or 'POST', default: 'GET').
     *   onShow: (function) Optional. Callback after modal is shown.
     *   onError: (function) Optional. Callback on AJAX error.
     *   loaderSelector: (string) Optional. Selector for a loader to show/hide.
     */
    function loadDynamicModal(options) {
        var settings = jQuery.extend({
            url: null,
            modalContainerId: 'dynamic-modal-container',
            data: {},
            method: 'GET',
            onShow: null,
            onError: null,
            loaderSelector: '#loader-global'
        }, options);

        if (!settings.url) {
            if (typeof settings.onError === 'function') {
                settings.onError('No URL specified');
            }
            return;
        }

        var formContainer = '#' + settings.modalContainerId;

        // Remove any existing modal container with the same ID
        jQuery(formContainer).remove();

        // Create container and append to body
        jQuery('<div id="' + settings.modalContainerId + '"></div>').appendTo('body');
        var container = jQuery(formContainer);

        // Show loader if specified
        if (settings.loaderSelector) jQuery(settings.loaderSelector).show();

        jQuery.ajax({
            url: settings.url,
            type: settings.method,
            data: settings.data,
            dataType: 'json',
            success: function(response) {
                if (!response || !response.result) {
                    if (typeof settings.onError === 'function') {
                        settings.onError(response && response.message ? response.message : 'Error loading modal');
                    } else {
                        pinesMessageV2({ ty: 'error', m: "Error occurred loading the modal" });
                    }
                    return;
                }
                container.html(response.html);
                // Optionally initialize modal size or other logic here
                if (typeof initializeModalSize === 'function') {
                    initializeModalSize(container);
                }
                var $modal = jQuery('.modal', container);
                $modal.modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'
                });
                $modal.on('hidden.bs.modal', function () {
                    container.remove();
                    jQuery('.modal-backdrop').remove();
                    jQuery('body').removeClass('modal-open');
                });
                if (typeof settings.onShow === 'function') {
                    settings.onShow($modal, response);
                }
            },
            error: function(xhr) {
                if (typeof settings.onError === 'function') {
                    settings.onError('Failed to load modal');
                } else {
                    container.html('<div class="alert alert-danger">Failed to load modal.</div>');
                    jQuery('.modal', container).modal('show');
                }
            },
            complete: function() {
                if (settings.loaderSelector) jQuery(settings.loaderSelector).hide();
            }
        });
    }
    /**
     * Handles modal form submission, supporting file uploads (multipart/form-data).
     * @param {jQuery} $modal - The modal jQuery object.
     * @param {string} defaultActionUrl - The default URL to submit the form to.
     * @param {function} [onSuccess] - Optional callback on successful response.
     */
    function handleModalFormSubmit($modal, defaultActionUrl, onSuccess) {
        $modal.find('form').on('submit', function(e) {
            e.preventDefault();
            var $form = jQuery(this);
            var actionUrl = $form.attr('action') || defaultActionUrl;
            var method = $form.attr('method') || 'POST';

            // Use FormData to support file uploads
            var formData = new FormData(this);

            jQuery.ajax({
                url: actionUrl,
                type: method,
                data: formData,
                processData: false, // Don't process the data
                contentType: false, // Let browser set content type (multipart/form-data)
                dataType: 'json',
                beforeSend: function () {
                    jQuery("#loader-global").show();
                },
                complete: function () {
                    jQuery("#loader-global").hide();
                },
                success: function(resp) {
                    if (resp && resp.result) {
                        $modal.modal('hide');
                        pinesMessageV2({ ty: 'success', m: resp.message || 'Operation successful.' });
                        if (typeof onSuccess === 'function') onSuccess(resp);
                        // Optionally refresh list or update UI here
                    } else {
                        pinesMessageV2({ ty: 'error', m: (resp && resp.message ? resp.message : 'Failed to process request.') });
                    }
                },
                error: function() {
                    pinesMessageV2({ ty: 'error', m: 'Failed to process request.' });
                }
            });
        });
    }

    function openEditExhibitModal(exhibitId) {
        loadDynamicModal({
            url: getBaseURL() + 'exhibits/edit',
            method: 'GET',
            data: { id: exhibitId },
            modalContainerId: 'edit-exhibit-modal-container',
            onShow: function($modal, response) {
                $modal.find('input,textarea,select').first().focus();
                handleModalFormSubmit($modal, getBaseURL() + 'exhibits/edit');
            },
            onError: function(message) {
                pinesMessageV2({ ty: 'error', m: message });
            }
        });
    }
    function openDeleteExhibitModal(exhibitId) {
        loadDynamicModal({
            url: getBaseURL() + 'exhibits/delete', 
            data: { id: exhibitId },
            modalContainerId: 'delete-exhibit-modal-container',
            onShow: function($modal, response) {
                handleModalFormSubmit($modal, getBaseURL() + 'exhibits/delete');
            },
            onError: function(message) {
                pinesMessageV2({ ty: 'error', m: message });
            }        
        });
    }


function openStatusChangeModal(exhibitId) {
    loadDynamicModal({
        url: getBaseURL() + 'exhibits/change_status',
        method: 'GET',
        data: { id: exhibitId },
        modalContainerId: 'status-change-modal-container',
        onShow: function($modal, response) {
            handleModalFormSubmit($modal, getBaseURL() + 'exhibits/change_status', function() {
                //refreshExhibitList();
            });
        },
        onError: function(message) {
            pinesMessageV2({ ty: 'error', m: message });
        }
    });
    }
    function openAddNoteModal(exhibitId) {
        loadDynamicModal({
            url: getBaseURL() + 'exhibits/add_note', 
            method: 'GET',
            data: { id: exhibitId },
            modalContainerId: 'add-note-modal-container',
            onShow: function($modal, response) {
                handleModalFormSubmit($modal, getBaseURL() + 'exhibits/add_note', function() {
                    //refreshExhibitList();
                });
            },
            onError: function(message) {
                pinesMessageV2({ ty: 'error', m: message });
            }
        });
    }
    function openAddCustodyModal(exhibitId) {
        loadDynamicModal({
            url: getBaseURL() + 'exhibits/transfer_custody', 
            method: 'GET',
            data: { id: exhibitId },
            modalContainerId: 'transfer-modal-container',
            onShow: function($modal, response) {
                handleModalFormSubmit($modal, getBaseURL() + 'exhibits/transfer_custody', function() {
                    //refreshExhibitList();
                });
            },
            onError: function(message) {
                pinesMessageV2({ ty: 'error', m: message });
            }
        });
    }
    function openAddDocumentModal(exhibitId) {
        loadDynamicModal({
            url: getBaseURL() + 'exhibits/upload_file', 
            method: 'GET',
            data: { id: exhibitId },
            modalContainerId: 'add-document-modal-container',
            onShow: function($modal, response) {
                handleModalFormSubmit($modal, getBaseURL() + 'exhibits/upload_file', function() {
                    //refreshExhibitList();
                });
            },
            onError: function(message) {
                pinesMessageV2({ ty: 'error', m: message });
            }
        });
    }
