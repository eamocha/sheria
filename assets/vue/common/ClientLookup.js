import Api from 'Api'

export default {
    name: 'ClientLookup',
    emits: ['handlechange'],
    props: ['clientname', 'required', 'disabled'],
    setup(props, context) {
        const { ref, watch } = Vue;
        const clientName = ref(props.clientname);
        const clientId = ref('0');
        const searchClients = ref([]);
        const required = ref(false)
        const disabled = ref( props.disabled)
        watch(props, () => {
            required.value = props.required
            disabled.value = props.disabled
            clientName.value = props.clientname
        })
        let timeout = null
        const getClientsData = () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                 if (clientName.value.length > 2) {
                     searchClients.value = [];
                     axios.get(Api.getApiBaseUrl("money") + '/accounts?model_type=client&organization_id=' + organizationIDGlobal + '&name[ct]=' + encodeURIComponent(clientName.value), Api.getInitialHeaders()).then(response => {
                         if (response.data.accounts)
                             if (response.data.accounts.length == 0)
                                 searchClients.value = ['no_results'];
                             else
                                 searchClients.value = response.data.accounts
                         else
                             searchClients.value = ['no_results'];
                     }).catch((error) => {
                         pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                         searchClients.value = ['no_results'];
                     });
                 } else {
                     searchClients.value = []
                     clientId.value = 0
                 }
            }, 200);
        }

        const setClient = (client) => {
            clientName.value = client.name
            context.emit('handlechange', client)
            searchClients.value = []
        }
        /*
        * This is a jquery function to be deleted in the next releases
        */
        const quickAddNewClient = () => {
            let clientFormDialog = jQuery('#clientFormDialog');
            let newClient = {}
            jQuery.ajax({
                dataType: 'JSON',
                type: 'POST',
                url: getBaseURL('money') + 'clients/add',
                success: function(response) {
                    clientFormDialog.dialog({
                        autoOpen: true,
                        buttons: [{
                                text: _lang.save,
                                'class': 'btn btn-info',
                                id: 'btnSubmitSave',
                                click: function() {
                                    var dataIsValid = jQuery("form#clientForm", this).validationEngine('validate');
                                    var formData = jQuery("form#clientForm", this).serialize();
                                    if (dataIsValid) {
                                        var that = this;
                                        jQuery.ajax({
                                            beforeSend: function() {},
                                            data: formData,
                                            dataType: 'JSON',
                                            type: 'POST',
                                            url: getBaseURL('money') + 'clients/add',
                                            success: function(response) {
                                                if (!response.status) {
                                                    pinesMessage({
                                                        ty: 'error',
                                                        m: _lang.saveRecordFailed.sprintf([_lang.clients_Money])
                                                    });
                                                    jQuery(that).dialog("close");
                                                } else {
                                                    pinesMessage({
                                                        ty: 'success',
                                                        m: _lang.record_added_successfull.sprintf([_lang.clients_Money])
                                                    });
                                                    //udpdate fields to new key to be same with keys returned from the api
                                                    newClient = response.account;
                                                    newClient.account_data = response.account.accountData;
                                                    newClient.system_account = response.account.systemAccount;
                                                    newClient.address = response.account.address1;
                                                    newClient.currency = response.account.currencyCode;
                                                    if(newClient.additional_id_type)    //to be selected convert to number
                                                        newClient.additional_id_type = parseInt(response.account.additional_id_type);
                                                    setClient(newClient);
                                                    jQuery(that).dialog("close");
                                                }
                                            },
                                            error: defaultAjaxJSONErrorsHandler
                                        });
                                    }
                                }
                            },
                            {
                                text: _lang.cancel,
                                'class': 'btn btn-link',
                                click: function() {
                                    jQuery(this).dialog("close");
                                }
                            }
                        ],
                        close: function() {
                            jQuery(window).unbind('resize');
                        },
                        draggable: true,
                        modal: false,
                        open: function() {
                            var that = jQuery(this);
                            jQuery(window).bind('resize', (function() {
                                resizeNewDialogWindow(that, '70%', '500');
                            }));
                            resizeNewDialogWindow(that, '70%', '500');
                        },
                        resizable: true,
                        responsive: true,
                        title: _lang.client_Money
                    });
                    clientFormDialog.html(response.html);
                    jQuery('.form-action', '#clientForm').remove();
                    jQuery('#client_form').removeClass('col-md-6').addClass('col-md-12');
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
        return {
            setClient,
            getClientsData,
            quickAddNewClient,
            searchClients,
            clientName,
            required,
            disabled
        };
    },
    template: `
        <div class="lookup-element">
            <input id='client-lookup-element' :disabled=disabled :class="{ 'required-field' : (clientName=='' && required)  }" class="form-control input-lookup" 
            placeholder="` + _lang.startTyping + `"
            v-model="clientName" @keyup="getClientsData()" autocomplete="off" type="text" />
            <div style="position:absolute; z-index:5;width:96%" v-if="searchClients.length">
                <ul class="list-group" style="max-height: 200px;margin-bottom: 10px; overflow-y:auto;">
                    <a v-if="searchClients[0]=='no_results'" class="list-group-item" href="javascript:;" @click="quickAddNewClient()">` + _lang.noMatchesFound + '. <span class="text-primary"> <i class="fa fa-fw fa-plus-circle"></i> ' + _lang.addNewClient + `</span></a>
                    <a v-else href="javascript:;" class="list-group-item" v-for="data1 in searchClients" @click="setClient(data1)">{{ data1.name }} - {{ data1.currency }}</a>
                </ul>
            </div>
        </div>
    `,
};