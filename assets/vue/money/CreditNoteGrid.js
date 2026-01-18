import Api from 'Api';
import Loader from "Loader";
import { dateWithoutTime, displayListString, trimHtmlTags, limitCharacters } from "Utils";

const { useConfirm } = primevue.useconfirm;
const { useToast } = primevue.usetoast;
export default {
    name: 'CreditNoteGrid',
    emits: ['handlechange'],
    props: ['loading'],
    components: {
        'loader': Loader,
        'p-datatable': primevue.datatable,
        'p-column': primevue.column,
        'p-button': primevue.button,
        'p-inputtext': primevue.inputtext,
        'p-multiselect': primevue.multiselect,
        'p-togglebutton': primevue.togglebutton,
        'p-dialog': primevue.dialog,
        'p-confirmdialog': primevue.confirmdialog,
        'p-toast': primevue.toast,
        'p-breadcrumb': primevue.breadcrumb,
        'p-badge': primevue.badge,
        'p-menu': primevue.menu
    },
    setup(props, context) {
        const { ref, watch, onMounted } = Vue;
        const { FilterMatchMode, FilterOperator } = primevue.api;
        const dt = ref();
        const loading = ref(false);
        const totalRecords = ref(0);
        const items = ref();
        const lazyParams = ref({});
        const confirm = useConfirm();
        const toast = useToast();
        const showLoader = ref(true);
        const optionsMenu = ref();
        const filterableColumns = ref(['credit_note_number', 'account.name', 'account.number', 'paid_status']);
        let selectedCreditNoteId = null;

        const columns = ref([
            {
                field: '', header: '', type:'actions', options:[
                    {label: _lang.edit, link: 'modules/money/vouchers/edit_credit_note/', key:'id'},
                    {label: _lang.delete, link: '##', key:'id', click:'deleteCreditNote'},
                ]
            },
            {field: 'credit_note_number', header: _lang.money.creditNote, frozen: true, type: 'link', link: { path: 'modules/money/vouchers/edit_credit_note/', key: 'id' } },
            {field: 'invoice_number', header: _lang.money.invoiceNumber, type:'link', link: {path:'modules/money/vouchers/invoice_edit/', key:'voucher_header_id'}},
            {field: 'invoice_date', header: _lang.money.invoiceDate},
            {field: 'invoice_total', header: _lang.money.invoiceTotal},
            {field: 'account.name', header: _lang.clientAccount, type:'link', link: {path:'modules/money/clients/client_details/', key:'account.model_id'}},
            {field: 'account.number', header: _lang.client_Money, type:'link', link: {path:'modules/money/accounts/edit/', key:'account.id'}},
            {field: 'total', header: _lang.total, type:'number'},
            {field: 'balance_due', header:  _lang.balanceDue, type:'number'},
            {field: 'account.currency', header:  _lang.currency },
            {field: 'paid_status', header:  _lang.status },
            {field: 'credit_note_date', header: _lang.date, type:'date'},
            {field: 'credit_note_reference', header: _lang.money.creditNoteRef},
            {field: 'tax_number', header: _lang.taxNumber},
            {field: 'case_id', header: _lang.caseId},
            {field: 'case_subject', header: _lang.caseSubject},
            {field: 'assignee', header: _lang.assignee},
            {field: 'practice_area', header: _lang.caseType},
            {field: 'created_on', header:  _lang.createdOn},
            {field: 'created_by_name', header: _lang.createdBy},
            {field: 'modified_on', header: _lang.modifiedOn},
            {field: 'modified_by_name', header:  _lang.modifiedBy},
            {field: 'exchange_rate', header: _lang.exchangeRate, type:'number'},
            {field: 'taxable', header: _lang.taxable, type:'number'},
            {field: 'non_taxable', header: _lang.nonTaxable, type:'number'},
            {field: 'sub_total', header: _lang.subTotal, type:'number'},
            {field: 'total_discount', header: _lang.totalDiscount, type:'number'},
            {field: 'total_tax', header: _lang.money.totalTax, type:'number'},
            {field: 'sub_total_after_discount', header: _lang.subTotalAfterDiscount, type:'number' },
            {field: 'notes', header:  _lang.money.notes},
            {field: 'terms', header:  _lang.terms},
            {field: 'description', header: _lang.description},
        ]);
        const shownColumnsOptions = ref([]);
        const filters = ref(null);
        
        const selectedColumns = ref(columns.value);
        const onToggle = (val) => {
            selectedColumns.value = columns.value.filter(col => val.includes(col));
        };

        const setLoader = (action) => {
			showLoader.value = action;
        }

        const optionsMenuItems = ref([
				{
					label: _lang.money.editCreditNote,
					icon: 'pi pi-pencil',
					command: () => {
                        window.location = `${getBaseURL('money')}/vouchers/edit_credit_note/${selectedCreditNoteId}`;
					}
				}, {
					label: _lang.money.deleteCreditNote,
					icon: 'pi pi-trash',
                    command: () => {
                        deleteCreditNote(selectedCreditNoteId);
					}
            }
        ]
                );

        const toggleOptionsMenu = (event, id) => {
            selectedCreditNoteId = id;
			optionsMenu.value.toggle(event);
		};

        onMounted(() => {
            setLoader(true);
            lazyParams.value = {
                sort: null,
                sortField : null,
                perPage: dt.value.rows,
                page:0,
                filterParams:''
            };
            filters.value = {
                credit_note_number: {
                    constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
                },
                "account.name": {
                    constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
                },
                "account.number": {
                    constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
                },
                "paid_status": {
                    constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
                },
            }
            let shownColumns = columns.value.slice();
            shownColumns = shownColumns.slice(1);
            shownColumnsOptions.value = shownColumns;
            initialize()
        })
        const initialize = () => {
            initApiAccessToken(getList, getBaseURL('money') + 'money_dashboards/');
        };

        const onPage = (event) => {
            setLoader(true);
            console.log(event)
            lazyParams.value.perPage =  event.rows;
            lazyParams.value.page = event.page;
            lazyParams.value.filterParams = getFilterParams(event.filters);
            console.log(lazyParams.value)
            getList();
        };
        const onSort = (event) => {
            setLoader(true);
            lazyParams.value.sort =  event.sortOrder === 1 ? 'asc' : 'desc';
            lazyParams.value.sortField = event.sortField;
            lazyParams.value.perPage =  dt.value.rows;
            lazyParams.value.page = 0;
            lazyParams.value.filterParams = getFilterParams(event.filters);
            getList();
        };
        const onFilter = () => {
            setLoader(true);
            lazyParams.value.perPage =  dt.value.rows;
            lazyParams.value.page = 0;
            lazyParams.value.filters = filters.value;
            lazyParams.value.filterParams = getFilterParams(filters.value);
            getList();
        }
        const edit = (item) => {
            console.log(item)
        }

        const getList = () => {
            setLoader(true);
            let sortingParams = lazyParams.value.sort ? `&sort[${lazyParams.value.sort}]=${lazyParams.value.sortField}` : '';
            let param = `?per_page=${lazyParams.value.perPage}&page=${(lazyParams.value.page + 1)}${sortingParams}${lazyParams.value.filterParams}`;
            axios.get(`${Api.getApiBaseUrl("money")}/credit_notes${param}&organization_id=${organizationIDGlobal}`, Api.getInitialHeaders())
                .then(response => {
                setLoader(false);
                if(response.data.credit_note){
                    totalRecords.value = response.data.page_context.total;
                    items.value = response.data.credit_note;
                } else {
                    items.value = '';
                }
                }).catch((error) => {
                    setLoader(false);
                    toast.add({severity:'error', summary:_lang.error, detail:error.response.data.message, life: 3000});
            });
        }

        const getFilterParams = (filterObject) => {
            var filterParams = '';
            for (const [key, value] of Object.entries(filterObject)) {
                if(value.constraints) {
                    if(value.constraints[0].value !== null) {
                        filterParams += '&' + key + '[' + value.constraints[0].matchMode + ']=' + value.constraints[0].value;
                    }
                }
                else{
                    if(value.value !== null) {
                        filterParams += '&' + key + '[' + value.matchMode + ']=' + value.value;
                    }
                }
            }
            return filterParams;
        };
        const index = (obj,is, value) => {
            if (typeof is == 'string')
                return index(obj,is.split('.'), value);
            else if (is.length==1 && value!==undefined)
                return obj[is[0]] = value;
            else if (is.length==0)
                return obj;
            else
                return index(obj[is[0]],is.slice(1), value);
        };
        const exportCSV = () => {
            let currentColumns = selectedColumns.value.map(column => column.field);
            currentColumns.shift();
            currentColumns = currentColumns.join("-");
            currentColumns = encodeURIComponent(currentColumns);
            window.location = `${getBaseURL('money')}/vouchers/credit_notes_export_to_excel?exported_columns=${currentColumns}`;
        };
        function onButtonClick(functionName,data, key) {
            this[functionName](data, key);
        };
        function deleteCreditNote(id) {
            confirm.require({
                message: _lang.money.confirmationDeleteCreditNote,
                header: _lang.confirmationDelete,
                icon: 'pi pi-exclamation-triangle',
                acceptClass: 'p-button-danger',
                accept: () => {
                    axios.delete(`${Api.getApiBaseUrl("money")}/credit_notes/${id}?organization_id=${organizationIDGlobal}`, Api.getInitialHeaders())
                    .then(() => {
                        toast.add({severity:'success', summary: _lang.feedback_messages.success, detail: _lang.money.creditNoteDeletedSuccessfully, life: 3000});
                        getList();
                    }).catch((error) => {
                        toast.add({severity:'error', summary:_lang.error, detail:error.response.data.message, life: 3000});
                    });
                },
            });
        };
        return { optionsMenuItems,
			toggleOptionsMenu,
			optionsMenu,dt, columns, shownColumnsOptions, totalRecords, selectedColumns, filterableColumns, items, loading,lazyParams, filters, showLoader, dateWithoutTime, displayListString, trimHtmlTags, limitCharacters, deleteCreditNote,onButtonClick, onPage,onSort,onFilter, onToggle, exportCSV,index, edit, getList }
    },
    template: `
    <p-toast></p-toast>
    <p-confirmdialog></p-confirmdialog>
    <loader :show="showLoader"></loader>
    <div class="col-md-12">
        <p-datatable class="p-datatable" :value="items" :reorderable-columns="true" :paginator="true" :lazy="true" :rows="10" ref="dt"
        data-key="id" :row-hover="true" v-model:selection="selectedColumns" v-model:filters="filters" filter-display="menu" 
        :total-records="totalRecords" @page="onPage($event)" @sort="onSort($event)" @filter="onFilter($event)" filter-display="row"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rows-per-page-options="[10,25,50]" current-page-report-template=" {first} - {last} of {totalRecords} items"
        :global-filter-fields="['credit_note_number', 'account.name']" 
        :scrollable="true" scroll-height="flex" :loading="loading" scroll-direction="both" responsive-layout="scroll">
            <template #header>
                <div class="bg-gray-50 p-2 flex justify-content-between align-items-center shadow-1">
                    <div><span class="text-2xl">${_lang.money.creditNotes}</span></div>
                    <div>
                        <p-button label="${_lang.money.exportList}" icon="pi pi-external-link" class="p-button mr-2 ml-2" @click="exportCSV()"></p-button>
                        <p-multiselect class="mr-2 ml-2" :model-value="selectedColumns" :options="shownColumnsOptions" option-label="header" selected-items-label="${_lang.kendo_grid_sortable_messages.columns}" :max-selected-labels="1" @update:model-value="onToggle" placeholder="${_lang.kendo_grid_sortable_messages.columns}" :filter="true" style="width: 12em"></p-multiselect>
                        <p-button @click="getList" icon="pi pi-refresh" class="p-button-rounded p-button-primary mr-2 ml-2"></p-button>
                    </div>
                </div>
            </template>
            <template #empty>
                ${_lang.noRecordFound}
            </template>

            <p-column :showFilterMenu="filterableColumns.includes(col.field) ? true : false" :style="col.type !== 'actions' ? 'flex-grow:1; width:200px;' :' width:57px;'" v-for="(col, ind) of selectedColumns" :field="col.field" :header="col.header"
             :key="col.field + '_' + ind" :sortable="col.type !== 'actions'" :frozen="col.frozen">
                <template #filter="{filterModel}" v-if="filters">
                    <p-inputtext type="text" v-model="filterModel.value" class="p-column-filter" placeholder="Search by name"></p-inputtext>
                </template>
                <template #body="{data}" v-if="col.type == 'link'">
                    <a :href="col.link.path + index(data,col.link.key)">{{ index(data,col.field ) }}</a>
                </template>
                <template #body="{data}" v-if="col.type == 'actions'">
                    <p-button icon="pi pi-cog" class="p-button-rounded p-button-text" @click="toggleOptionsMenu($event, data.id)" aria-haspopup="true" aria-controls="overlay_menu"></p-button>
                    <p-menu ref="optionsMenu" :model="optionsMenuItems" :popup="true"></p-menu>
                </template>
                <template #body="{data}" v-if="col.type == 'number'">
                    <span>{{parseFloat(data[col.field]).toLocaleString()}}</span>
                </template>
                <template #body="{data}" v-if="col.type == 'date'">
                    <span>{{dateWithoutTime(data[col.field])}}</span>
                </template>
                <template #body="{data}" v-if="col.field == 'paid_status'">
                <p-badge v-if="data.paid_status === 'draft'" value="${_lang.money.draft}" severity="warning" size="large" class="text-base"></p-badge>
                <p-badge v-else-if="data.paid_status === 'open'" value="${_lang.money.open}" severity="info" size="large" class="text-base"></p-badge>
                <p-badge v-else-if="data.paid_status === 'paid'" value="${_lang.paid}" severity="success" size="large" class="text-base"></p-badge>
                <p-badge v-else-if="data.paid_status === 'cancelled'" value="${_lang.cancelled}" severity="danger" size="large" class="text-base"></p-badge>
                </template>
                <template #body="{data}" v-if="col.field == 'case_id' || col.field == 'practice_area'">
                <span style="white-space: pre;">{{displayListString(data[col.field], ',')}}</span>
            </template>
            <template #body="{data}" v-if="col.field == 'case_subject'">
                <span style="white-space: pre;">{{displayListString(data.case_subject, ':/;')}}</span>
            </template>
            <template #body="{data}" v-if="col.field == 'notes'">
                <span style="white-space: pre-wrap;">{{limitCharacters(trimHtmlTags(data.notes))}}</span>
            </template>
            <template #body="{data}" v-if="col.field == 'description'">
                <span style="white-space: pre-wrap;">{{limitCharacters(trimHtmlTags(data.description))}}</span>
            </template>
            <template #body="{data}" v-if="col.field == 'invoice_number'">
                <a :href="col.link.path + data.related_invoices[0].voucher_header_id">{{data.related_invoices[0].invoice_number}}</a>
            </template>
            <template #body="{data}" v-if="col.field == 'invoice_date'">
                <span>{{dateWithoutTime(data.related_invoices[0].invoice_date)}}</span>
            </template>
            <template #body="{data}" v-if="col.field == 'invoice_total'">
                <span>{{parseFloat(data.related_invoices[0].invoice_total).toLocaleString()}}</span>
            </template>
            </p-column>
        </p-datatable>
    </div>
    `,
};