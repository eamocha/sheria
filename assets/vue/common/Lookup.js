import Api from 'Api'
import { trimHtmlTags } from "Utils";

export default {
    name: 'Lookup',
    props: ['api-name', 'place-holder', 'selected', 'error', 'error-message', 'disable'],
    emits: ['handlechange'],
    components: {
        'p-autocomplete': primevue.autocomplete,
    },
    setup(props, context) {
        const {ref, watch } = Vue;
        const selected = ref(props.selected);
        const filtered = ref();
        const showSearchIcon = ref(1);
        const search = (event) => {
            showSearchIcon.value = 0;
            setTimeout(() => {
                if (event.query.trim().length > 1){
                    axios.get(Api.getApiBaseUrl() + '/' + props.apiName + '?name[ct]=' + encodeURIComponent(event.query.toLowerCase()), Api.getInitialHeaders()).then(response => {
                            if (response.data[props.apiName]){
                                if (response.data[props.apiName].length == 0) {
                                    filtered.value = [{name: _lang.noMatchesFound, id: -1}];
                                } else {
                                    filtered.value = response.data[props.apiName];
                                }
                            } else {
                                filtered.value = [{name: _lang.noMatchesFound, id: -1}];
                            }
                        }).catch((error) => {
                            pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? error.response.data.message + ' ' + _lang.feedback_messages.tryAgain : _lang.feedback_messages.error });
                            if (error?.response?.status == 401){
                                localStorage.removeItem('api-access-token');
                                setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                            }
                            loader(false)
                        });
                } else {
                    filtered.value = [];
                }
            }, 250);
        };
        const rtl = ref(_lang.languageSettings['langDirection'] === 'rtl' ? 1 : 0);
        const selectItem = (event) => {
            if (event.value.id < 0) selected.value = '';
            else context.emit('handlechange', event.value);
        };
        watch(() => props.selected, (newValue, oldValue) => {
            selected.value = replaceHtmlCharacter(newValue);
        }); 
        const validateSelected = () => {
            if (typeof selected.value === 'string' || selected.value instanceof String || selected.value == null) return selected.value;
            else return selected.value.name;
        }

        return {
            showSearchIcon,
            selected,
            filtered,
            rtl,
            search,
            selectItem,
            validateSelected
        }
    },
    template: `
    <span :class="(rtl) ? 'p-input-icon-left timelog-width-100' : 'p-input-icon-right timelog-width-100'">
        <i class="pi pi-search" style="z-index:2;"></i>
        <p-autocomplete style="width:100%;" :class="{'not-allowed' : disable, 'p-invalid': error}" v-model="selected" :suggestions="filtered" @complete="search($event)" @item-select="selectItem($event)" field="name"  :placeholder="placeHolder" :disabled="disable"></p-autocomplete>
        <small v-if="error" class="p-error" style="line-height:12px">`+ _lang.validationFieldRequired.sprintf([`{{errorMessage}}`]) +`</small>
    </span>
    `,
};