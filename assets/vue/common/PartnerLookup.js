import Api from 'Api'

export default {
    name: 'PartnerLookup',
    props: ['index', 'partnerchange', 'partnername'],
    setup(props) {
        const { ref, watch } = Vue;
        const partnerName = ref(props.partnername);
        const partnerId = ref('0');
        const searchPartners = ref([]);
        // watch(props, (newValue, oldValue) => {
        //     partnerName.value = props.partnername
        // })
        let timeout = null
        const getPartnersData = () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (partnerName.value.length > 2) {
                    searchPartners.value = [];
                    axios.get(Api.getApiBaseUrl("money") + '/accounts?model_type=partner&organization_id=' + organizationIDGlobal + '&name[ct]=' + encodeURIComponent(partnerName.value), Api.getInitialHeaders()).then(response => {
                        if (response.data.accounts)
                            if (response.data.accounts.length == 0)
                                searchPartners.value = ['no_results'];
                            else
                                searchPartners.value = response.data.accounts
                        else
                            searchPartners.value = ['no_results'];
                    }).catch((error) => {
                        pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                        searchPartners.value = ['no_results'];
                    });
                } else {
                    searchPartners.value = []
                    partnerId.value = 0
                }
            }, 200);

        }
        const setPartner = (partner1) => {
            partnerName.value = partner1.name + " - " + partner1.currency

            props.partnerchange(props.index, partner1)
            searchPartners.value = []
        }
        return {
            setPartner,
            getPartnersData,
            searchPartners,
            partnerName,
        };
    },
    template: `
    <div class="lookup-element">
        <input class="form-control input-lookup" 
        placeholder="` + _lang.startTyping + `"
        v-model="partnerName" @keyup="getPartnersData()" autocomplete="off" type="text" />
        <div style="position:absolute; z-index:5;width:92%" v-if="searchPartners.length">
            <ul class="list-group" style="max-height: 200px;margin-bottom: 10px; overflow-y:auto;">
                <a v-if="searchPartners[0]=='no_results'" class="list-group-item" href="javascript:;">` + _lang.noMatchesFound + `</a>
                <a v-else href="javascript:;" class="list-group-item" v-for="data1 in searchPartners" @click="setPartner(data1)">{{ data1.name }} - {{ data1.currency }}</a>
            </ul>
        </div>
    </div>
    `,
};