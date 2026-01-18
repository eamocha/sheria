import Api from "Api";

export default {
  name: "EditExchangeRate",
  props: ["showmodal", "entity", "exchangerate", "currencyid", "currencycode"],
  emits: ["submitnewrate", "closemodal"],

  components: {
    "p-dialog": primevue.dialog,

    "p-button": primevue.button,
    "p-inputnumber":primevue.inputnumber
  },
  setup(props, context) {
    const { ref, watch } = Vue;

    const showModal = ref(props.showmodal);
    const loading = ref(false);
    const entity = ref(props.entity);
    const currency = ref({
        id : props.currencyid,
        code : props.currencycode,
        rate : props.exchangerate,
    });
    watch(props, (newValue, oldValue) => {
        showModal.value = props.showmodal;
        entity.value = props.entity;
        currency.value.id = props.currencyid;
        currency.value.code = props.currencycode;
        currency.value.rate = props.exchangerate;
    });

    const closeModal = () => {
      context.emit("closemodal", false);
    };
    const EditExchangeRate = () => {
        loading.value = true

        axios.put(Api.getApiBaseUrl("money") + '/exchangerates/'+ entity.value.id+'/'+currency.value.id, currency.value, Api.getInitialHeaders()).then((response) => {
            if (response.data.exchange_rate) {
                loading.value = false
                pinesMessageV2({ ty: 'success', m: response.data.message });                
                context.emit("submitnewrate",  response.data.exchange_rate );
                context.emit("closemodal", false);
            } else {
                loading.value = false
                pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
            }
        }).catch((error) => {
            loading.value = false
            pinesMessageV2({ ty: 'error', m: error.response.data.message });
        });

    };
    return { showModal, entity, currency, loading, closeModal, EditExchangeRate };
  },
  template: `
    <p-dialog header="${_lang.money.editExchangeRate}" v-model:visible="showModal" :style="{width: '450px'}" position="left" :modal="true" @hide="closeModal">
        <div class="panel">
            <div class="p-formgroup-inline">
                <div class="p-field" style="direction: ltr;">
                    <span for="rate" >1 {{currency.code}} = </span>
                    <p-inputnumber id="rate" v-model="currency.rate" mode="decimal" :min-fraction-digits="0" :max-fraction-digits="10" :min="0"></p-inputnumber> 
                     <span> &nbsp; {{entity.currency_code}}</span>
                </div>
                <p-button type="button" label="${_lang.save}" icon="pi pi-check" :loading="loading" @click="EditExchangeRate"></p-button>
            </div>
        </div>
    </p-dialog>
    `,
};
