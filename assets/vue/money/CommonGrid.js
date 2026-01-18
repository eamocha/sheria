
import CommonInit from 'CommonInit';
import Loader from "Loader";
import { dateWithoutTime, displayListString, trimHtmlTags } from "Utils";

const { useToast } = primevue.usetoast;
const { useConfirm } = primevue.useconfirm;

export default {
    name: "CommonGrid",
    props: ["globalFilterFields", "columns", "searchListUrl", "addRecordUrl", "responseResourceName", "filterableColumns", "filters", "title", "initErrorUrl"],
    emits: ["updateSelectedRecordId"],

    components: {
        'loader': Loader,
        'p-datatable': primevue.datatable,
        'p-column': primevue.column,
        'p-button': primevue.button,
        'p-inputtext': primevue.inputtext,
        'p-multiselect': primevue.multiselect,
        'p-textarea': primevue.textarea,
        'p-inputnumber': primevue.inputnumber,
        'p-dropdown': primevue.dropdown,
        'p-togglebutton': primevue.togglebutton,
        'p-dialog': primevue.dialog,
        'p-confirmdialog': primevue.confirmdialog,
        'p-toast': primevue.toast,
        'p-breadcrumb': primevue.breadcrumb,
        'p-badge': primevue.badge,
        'p-menu': primevue.menu,
    },
    setup(props, context) {
        const { ref, watch, onMounted } = Vue;
        const dt = ref();
        const loading = ref(false);
        const toast = useToast();
        const confirm = useConfirm();
        const totalRecords = ref(0);
        const items = ref();
        const lazyParams = ref({});
        const showLoader = ref(true);
        const globalFilterFields = ref(props.globalFilterFields);
        let columns = ref(props.columns);
        let searchListUrl = ref(props.searchListUrl);
        let addRecordUrl = ref(props.addRecordUrl);
        let responseResourceName = ref(props.responseResourceName);
        let filterableColumns = ref(props.filterableColumns);
        const filters = ref(props.filters);
        let title = ref(props.title);
        let initErrorUrl = ref(props.initErrorUrl);

        let selectedRecordId = null;
        const optionsMenu = ref();
        const toggleOptionsMenu = (event, id) => {
            selectedRecordId = id;
            context.emit("updateSelectedRecordId", selectedRecordId);
            optionsMenu.value.toggle(event);
        };

        const selectedColumns = ref(columns.value);
        const onToggle = (val) => {
            selectedColumns.value = columns.value.filter(col => val.includes(col));
        };
        const setLoader = (action) => {
            showLoader.value = action;
        }
        const onSort = (event) => {
            lazyParams.value.sort = event.sortOrder === 1 ? 'asc' : 'desc';
            lazyParams.value.sortField = event.sortField;
            lazyParams.value.perPage = dt.value.rows;
            lazyParams.value.page = 0;
            lazyParams.value.filterParams = getFilterParams(event.filters);
            getList();
        };
        const onPage = (event) => {
            lazyParams.value.perPage = event.rows;
            lazyParams.value.page = event.page;
            lazyParams.value.filterParams = getFilterParams(event.filters);
            getList();
        };
        const onFilter = () => {
            lazyParams.value.perPage = dt.value.rows;
            lazyParams.value.page = 0;
            lazyParams.value.filters = filters.value;
            lazyParams.value.filterParams = getFilterParams(filters.value);
            getList();
        }
        const edit = (item) => {
        }
        const exportCSV = () => {
            dt.value.exportCSV();
        };

        let getList = function () {
            setLoader(true);
            let sortingParams = lazyParams.value.sort ? `&sort[${lazyParams.value.sort}]=${lazyParams.value.sortField}` : '';
            let param = `?per_page=${lazyParams.value.perPage}&page=${(lazyParams.value.page + 1)}${sortingParams}${lazyParams.value.filterParams}`;
            axios.get(searchListUrl.value + param)
                .then(response => {
                    setLoader(false);
                    if (response.data[responseResourceName.value]) {
                        totalRecords.value = response.data.page_context.total;
                        items.value = response.data[responseResourceName.value];
                    } else {
                        items.value = '';
                    }
                }).catch((error) => {
                    setLoader(false);
                    toast.add({ severity: 'error', summary: _lang.error, detail: error.response.data.message, life: 3000 });
                });
        };

        let deleteRecord = function(url){
            confirm.require({
                message: _lang.money.confirmationDeleteRecord,
                header: _lang.confirmationDelete,
                icon: 'pi pi-exclamation-triangle',
                acceptClass: 'p-button-danger',
                accept: () => {
                    axios.delete(url)
                        .then((response) => {
                            toast.add({ severity: 'success', summary: _lang.feedback_messages.success, detail: response.data.message, life: 3000 });
                            CommonGrid.getList.value();
                        }).catch((error) => {
                            toast.add({ severity: 'error', summary: _lang.error, detail: error.response.data.message, life: 3000 });
                        });
                },
            });
        }

        let getFilterParams = (filterObject) => {
            var filterParams = '';
            for (const [key, value] of Object.entries(filterObject)) {
                if (value.constraints) {
                    if (value.constraints[0].value !== null) {
                        filterParams += '&' + key + '[' + value.constraints[0].matchMode + ']=' + value.constraints[0].value;
                    }
                }
                else {
                    if (value.value !== null) {
                        filterParams += '&' + key + '[' + value.matchMode + ']=' + value.value;
                    }
                }
            }
            return filterParams;
        };

        const index = (obj, is, value) => {
            if (typeof is == 'string')
                return index(obj, is.split('.'), value);
            else if (is.length == 1 && value !== undefined)
                return obj[is[0]] = value;
            else if (is.length == 0)
                return obj;
            else
                return index(obj[is[0]], is.slice(1), value);
        };

        watch(props, (newValue, oldValue) => {
            optionsMenu.value = props.optionsMenu;
        });

        onMounted(() => {
            lazyParams.value = {
                sort: null,
                sortField: null,
                perPage: dt.value.rows,
                page: 0,
                filterParams: ''
            };
            CommonInit.initialize(getList, initErrorUrl);
        });

        return {
            getList,
            dt,
            loading,
            totalRecords,
            globalFilterFields,
            items,
            lazyParams,
            showLoader,
            columns,
            filters,
            selectedColumns,
            onToggle,
            onSort,
            onPage,
            onFilter,
            edit,
            getFilterParams,
            exportCSV,
            setLoader,
            title,
            filterableColumns,
            dateWithoutTime, displayListString, trimHtmlTags,
            optionsMenu,
            toggleOptionsMenu,
            index,
            deleteRecord,
            addRecordUrl,
            locationRedirect: function(new_url){
                window.location = new_url;
            }
        };
    },
    template: `
    <div>
        <p-toast></p-toast>
        <p-confirmdialog></p-confirmdialog>
        <loader :show="showLoader"></loader>
        <div class="col-md-12">
            <p-datatable class="p-datatable" :value="items" :reorderable-columns="true" :paginator="true" :lazy="true" :rows="10" ref="dt"
            data-key="id" :row-hover="true" v-model:selection="selectedColumns" v-model:filters="filters" filter-display="menu" 
            :total-records="totalRecords" @page="onPage($event)" @sort="onSort($event)" @filter="onFilter($event)" filter-display="row"
            paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rows-per-page-options="[10,25,50]" current-page-report-template=" {first} - {last} of {totalRecords} items"
            :global-filter-fields="globalFilterFields" 
            :scrollable="true" scroll-height="flex" :loading="loading" scroll-direction="both" responsive-layout="scroll">
                <template #header>
                    <div class="bg-gray-50 p-2 flex justify-content-between align-items-center shadow-1">
                        <div><span class="text-2xl">{{title}}</span></div>
                        <div>
                            <p-button label="${_lang.money.exportList}" icon="pi pi-external-link" class="p-button mr-2 ml-2" @click="exportCSV()"></p-button>
                            <p-multiselect class="mr-2 ml-2" :model-value="selectedColumns" :options="columns" option-label="header" selected-items-label="${_lang.kendo_grid_sortable_messages.columns}" :max-selected-labels="1" @update:model-value="onToggle" placeholder="${_lang.kendo_grid_sortable_messages.columns}" :filter="true" style="width: 12em"></p-multiselect>
                            <p-button @click="getList" icon="pi pi-refresh" class="p-button-rounded p-button-primary mr-2 ml-2"></p-button>
                            <p-button v-if="addRecordUrl" @click="locationRedirect(addRecordUrl)" icon="pi pi-plus" class="p-button-rounded p-button-primary mr-2 ml-2"></p-button>
                        </div>
                    </div>
                </template>
                <template #empty>
                    ${_lang.noRecordFound}
                </template>

                <p-column :showFilterMenu="filterableColumns.includes(col.field) ? true : false" :style="col.type !== 'actions' ? 'flex-grow:1; width:200px;' :' width:57px;'" v-for="(col, ind) of selectedColumns" :field="col.field" :header="col.header"
                :key="col.field + '_' + ind" :sortable="col.type !== 'actions'" :frozen="col.frozen">
                    <template #filter="{filterModel}" v-if="filters[col.field]">
                        <p-inputtext type="text" v-model="filterModel.value" class="p-column-filter" placeholder=""></p-inputtext>
                    </template>
                    <template #body="{data}" v-if="col.type == 'link'">
                        <a :href="col.link.path + index(data,col.link.key)">{{ index(data,col.field ) }}</a>
                    </template>
                    <template #body="{data}" v-if="col.type == 'actions'">
                        <p-button icon="pi pi-cog" class="p-button-rounded p-button-text" @click="toggleOptionsMenu($event, data.id)" aria-haspopup="true" aria-controls="overlay_menu"></p-button>
                        <p-menu ref="optionsMenu" :model="this.$parent.optionsMenuItems" :popup="true"></p-menu>
                    </template>
                    <template #body="{data}" v-if="col.type == 'number'">
                        <span>{{parseFloat(data[col.field]).toLocaleString()}}</span>
                    </template>
                    <template #body="{data}" v-if="col.type == 'date'">
                        <span>{{dateWithoutTime(data[col.field])}}</span>
                    </template>
                </p-column>
            </p-datatable>
        </div>
    </div>
    `,
};