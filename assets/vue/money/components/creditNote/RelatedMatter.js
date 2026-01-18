import DateFilter from "DateFilter";

export default {
  name: "RelatedMatter",
  props: ["showModal", "matters", "showDiscount", "showTax", "invoicedMatters", "taxesList"],
  emits: ["on-submit-matters", "handle-change"],
  components: {
    'date-filter': DateFilter,
    'p-dialog': primevue.dialog,
    'p-inputtext': primevue.inputtext,
    'p-button': primevue.button
  },
  setup(props, context) {
    const { ref, watch } = Vue;
    const data = ref(frontEndData);
    const showModal = ref(props.showModal);
    const matters = ref(props.matters);
    const selectedMatters = ref([]);
    const selectedMattersDetails = ref([]);

    const expenses = ref([]);
    const selectedExpenses = ref([]);
    const selectedExpensesDetails = ref([]);

    const mainTimeLogs = ref([]);
    const timeLogs = ref([]);
    const selectedTimeLogs = ref([]);
    const selectedTimeLogsDetails = ref([]);
    const timeLogDateFilter = ref({});
    const selectedTax = ref({
      tax_id: null,
      tax_percentage: 0,
    });
    const selectedDiscount = ref({
      discount_id: null,
      discount_percentage: 0,
    });

    const showSection = ref("matters");
    const sectionName = ref( _lang.relatedCase);
    const showSelectMsg = ref("");
    const timeLogUserData = ref("code");
    const timeLogUserGroup = ref(false);

    const showDiscount = ref(props.showDiscount);
    const showTax = ref(props.showTax);

    const invoicedMatters = ref(props.invoicedMatters);
    const taxesList = ref(props.taxesList);

    watch(props, (newValue, oldValue) => {
      showModal.value = props.showModal;
      matters.value = props.matters;
      invoicedMatters.value = props.invoicedMatters;
      showDiscount.value = props.showDiscount;
      showTax.value = props.showTax;
      taxesList.value = props.taxesList;
      if (invoicedMatters.value.length > 0) checkinvoicedMatters();
      else checkAllMatters(true);
    });

    const checkinvoicedMatters = () => {
      selectedMatters.value = [];
      invoicedMatters.value.map((matter) => {
        selectedMatters.value.push(matter.matter_id);
      });
    };
    const checkAllMatters = (flag) => {
      if (flag)
        matters.value.map((matter) => selectedMatters.value.push(matter.id));
      else selectedMatters.value = [];
    };
    const checkAllTimeLogs = (flag) => {
      if (flag)
        timeLogs.value.map((timeLog) =>
          selectedTimeLogs.value.push(timeLog.id)
        );
      else selectedTimeLogs.value = [];
    };
    const checkAllExpenses = (flag) => {
      if (flag)
        expenses.value.map((expense) =>
          selectedExpenses.value.push(expense.expense_id)
        );
      else selectedExpenses.value = [];
    };
    const mergeGroupBy = (array) => {
      let result = [];
      for (const val in array) {
        let effectiveEffort = 0;
        let description = "";
        let obj = {};
        obj.timelog_data = [];
        array[val].map((time, index) => {
          effectiveEffort =
            Number(effectiveEffort) + Number(time["effective_effort"]);
          description += time["description"] + "\n";
          obj.description = description;
          obj.effective_effort = effectiveEffort;
          obj.account_id = time["account_id"];
          obj.case_category = time["case_category"];
          obj.comments = time["comments"];
          obj.id = time["id"];
          obj.log_date = time["log_date"];
          obj.matter = time["matter"];
          obj.matter_code = time["matter_code"];
          obj.rate = time["rate"];
          obj.time_status = time["time_status"];
          obj.user = time["user"];
          obj.user_code = time["user_code"];
          obj.user_id = time["user_id"];
          obj.tax_id = time["tax_id"];
          obj.tax_percentage = time["tax_percentage"];
          obj.discount_id = time["discount_id"];
          obj.discount_percentage = time["discount_percentage"];
          obj.user_data = time["user_data"];
          obj.partner_shares = time['partner_shares'];
          obj.timelog_data[index] = {
            time_log_id: time["id"],
            date: time["log_date"],
            user_id: time["user_id"],
            description: time["description"],
          };
        });
        result.push(obj);
      }
      return result;
    };

    const groupTimeLogs = (array) => {
      let result = {};
      let key = "";
      array.map((item) => {
        key = item["matter_code"] + "_" + item["user_id"] + "_" + item["rate"];
        if (!result[key]) result[key] = [];
        result[key].push(item);
      });
      return mergeGroupBy(result);
    };

    const resetModalCond = () => {
      showModal.value = false;
      showSection.value = "matters";
      sectionName.value = _lang.relatedCase;
      showSelectMsg.value = "";
      selectedMattersDetails.value = [];
      selectedTimeLogsDetails.value = [];
      selectedExpensesDetails.value = [];
      expenses.value = [];
      timeLogs.value = [];
      mainTimeLogs.value = [];
    };
    const onTaxChange = (event) => {
      selectedTax.value.tax_id = null;
      selectedTax.value.tax_percentage = 0;
      taxesList.value.map((tx) => {
        if (event.target.value == tx["id"]) {
          selectedTax.value.tax_percentage = tx["percentage"];
          selectedTax.value.tax_id = tx["id"];
        }
      });
    };
    const onDiscountChange = (event) => {
      selectedDiscount.value.discount_id = null;
      selectedDiscount.value.discount_percentage = 0;
      data.value.discounts.map((disc) => {
        if (event.target.value == disc["id"]) {
          selectedDiscount.value.discount_percentage = disc["percentage"];
          selectedDiscount.value.discount_id = disc["id"];
        }
      });
    };

    const closeModal = () => {
      resetModalCond();
      context.emit("handle-change", false);
    };

    const submitModal = () => {
      context.emit('on-submit-matters', selectedMattersDetails.value, selectedTimeLogsDetails.value, selectedExpensesDetails.value);
      resetModalCond();
    };

    const handleTimeLogDateFilter = (date) => (timeLogDateFilter.value = date);

    const filterTimeLog = () => {
      timeLogDateFilter.value.operator =
        timeLogDateFilter.value.operator ?? "eq";
      timeLogDateFilter.value.from =
        timeLogDateFilter.value.from ??
        moment().locale("en").toDate().format("Y-m-d");
      timeLogDateFilter.value.to =
        timeLogDateFilter.value.to ??
        moment().locale("en").toDate().format("Y-m-d");
      timeLogs.value = mainTimeLogs.value.filter((time) => {
        switch (timeLogDateFilter.value.operator) {
          case "eq":
            return time.log_date == timeLogDateFilter.value.from;
          case "neq":
            return time.log_date != timeLogDateFilter.value.from;
          case "lt":
            return time.log_date < timeLogDateFilter.value.from;
          case "lte":
            return time.log_date <= timeLogDateFilter.value.from;
          case "gt":
            return time.log_date > timeLogDateFilter.value.from;
          case "gte":
            return time.log_date >= timeLogDateFilter.value.from;
          case "btw":
            return (
              time.log_date >= timeLogDateFilter.value.from &&
              time.log_date <= timeLogDateFilter.value.to
            );
          default:
            return time.log_date == timeLogDateFilter.value.from;
        }
      });
    };

    const showStep = (stepType) => {
      if (selectedMatters.value.length == 0)
        showSelectMsg.value = _lang.selectCase;
      else {
        showSelectMsg.value = "";
        switch (stepType) {
          case "matters":
            selectedMattersDetails.value = [];
            matters.value.map((matter) => {
              if (selectedMatters.value.includes(matter.id)) {
                matter.matter_id = matter.id;
                selectedMattersDetails.value.push(matter);
                matter.related_time_logs.map((related_time_log) => {
                  related_time_log['partner_shares'] = matter.partner_shares
                  related_time_log["matter_code"] = matter.matter_code;
                  timeLogs.value.push(related_time_log);
                  mainTimeLogs.value.push(related_time_log);
                });
                matter.related_expenses.map((related_expense) => {
                  related_expense['partner_shares'] = matter.partner_shares
                  expenses.value.push(related_expense);
                });
              }
            });
            timeLogs.value.sort(
              (a, b) => new Date(a.log_date) - new Date(b.log_date)
            );

            if(timeLogs.value.length > 0){
              showSection.value = "timeLogs";
              sectionName.value = _lang.relatedTimeLogs_Invoice;
            }
            else{
                if(expenses.value.length > 0){
                    showSection.value = "expenses";
                    sectionName.value = _lang.relatedExpenses_Invoice;
                }else{
                    showSection.value = "empty";
                    sectionName.value = _lang.relatedCase;
                }
            }
            checkAllTimeLogs(true);
            checkAllExpenses(true);
            break;
          case "timeLogs":
            selectedTimeLogsDetails.value = [];
            let userData =
              timeLogUserData.value == "code" ? "user_code" : "user";
            let timeArr = [];
            timeLogs.value.map((timeLog) => {
              if (selectedTimeLogs.value.includes(timeLog.id)) {
                timeLog.tax_id = selectedTax.value.tax_id;
                timeLog.tax_percentage = selectedTax.value.tax_percentage;
                timeLog.discount_id = selectedDiscount.value.discount_id;
                timeLog.discount_percentage =
                  selectedDiscount.value.discount_percentage;
                timeLog.user_data = timeLog[userData];
                timeLog.description =
                  (timeLog["category"] ? timeLog["category"] + " - " : "") +
                  timeLog["log_date"] +
                  " - " +
                  timeLog["effective_effort"] +
                  (timeLog["comments"] ? " - " + timeLog["comments"] : "");
                timeLog.timelog_data = [
                  {
                    time_log_id: timeLog["id"],
                    date: timeLog["log_date"],
                    user_id: timeLog["user_id"],
                    description: timeLog["description"],
                  },
                ];
                timeArr.push(timeLog);
              }
            });
            selectedTimeLogsDetails.value = timeLogUserGroup.value
              ? groupTimeLogs(timeArr)
              : timeArr;
            if(expenses.value.length > 0){
                showSection.value = "expenses";
                sectionName.value = _lang.relatedExpenses_Invoice;
            }
            else{
                submitModal();
            }
            break;
          case "expenses":
            selectedExpensesDetails.value = [];
            expenses.value.map((expense) => {
              if (selectedExpenses.value.includes(expense.expense_id))
                selectedExpensesDetails.value.push(expense);
            });
            submitModal();
            break;
          default:
            alert("Error");
            break;
        }
      }
    };
    return {
      showModal,
      selectedMatters,
      selectedExpenses,
      selectedTimeLogs,
      matters,
      expenses,
      timeLogs,
      showSection,
      showSelectMsg,
      selectedTax,
      selectedDiscount,
      timeLogUserData,
      timeLogUserGroup,
      showDiscount,
      showTax,
      sectionName,
      checkAllMatters,
      checkAllTimeLogs,
      checkAllExpenses,
      handleTimeLogDateFilter,
      filterTimeLog,
      onDiscountChange,
      onTaxChange,
      showStep,
      closeModal,
      submitModal,
      data,
      taxesList
    };
  },
  template:
    `

<p-dialog :header="sectionName" v-model:visible="showModal" :breakpoints="{'960px': '75vw'}" :style="{width: '95vw'}" :maximizable="true" position="top" :modal="true" @hide="closeModal">
  <div class="panel">
    <div class="p-fluid p-formgrid">
      <div v-if="matters.length==0">${_lang.noRelatedMatters}</div>
        <div v-if="showSection=='matters'">
          <table class="table table-bordered"> 
                <thead>
                <tr>
                    <td width=8%><input type=checkbox checked="true" @change="checkAllMatters($event.target.checked)" /> </td>
                    <td class="text-left" colspan=3>${_lang.selectAll}</td>
                    </tr>
                    </thead>
                    <tbody>
                <tr v-for="(singleMatter, index) in matters" :key="index">
                    <td><input type=checkbox :value='singleMatter.id' v-model="selectedMatters" /></td>
                    <td> <a target="_blank" :href="'cases/edit/'+singleMatter.id" >{{singleMatter.matter_code}} </a></td>
                    <td>{{singleMatter.subject}}</td>
                    <td>
                    <span class="badge badge-success mx-2" v-if="singleMatter['related_time_logs'].length > 0">{{singleMatter['related_time_logs'].length}} ${_lang.timeLogs}</span>
                    <span class="badge badge-success" v-if="singleMatter['related_expenses'].length > 0"> {{singleMatter['related_expenses'].length}} ${_lang.timeLogs}</span>
                    </td>
                </tr>
            </tbody>
          </table>
          <div>
              <span class="red-color">{{showSelectMsg}}</span>
          </div>
        </div>
      </div>

      <div v-if="showSection=='timeLogs'">
        <div class="col-md-12 no-padding">
          <div class="form-inline">
              <date-filter :filterdate="handleTimeLogDateFilter"></date-filter>
              <div class="form-group">
                  <button type="submit" class="btn btn-primary" @click="filterTimeLog">${_lang.run}</button>
              </div>
          </div>
          <div class="scroll">
              <table class="table table-bordered table-hover table-striped"> 
                  <thead>
                      <tr>
                          <th><input type=checkbox checked="true" @change="checkAllTimeLogs($event.target.checked)" /> </th>
                          <th>${_lang.date}</th>
                          <th>${_lang.comments}</th>
                          <th>${_lang.category}</th>
                          <th>${_lang.timeStatus}</th>
                          <th>${_lang.case_columns.effectiveEffort}</th>
                          <th>${_lang.rate}</th>
                          <th>${_lang.user}</th>
                          <th>${_lang.userCode}</th>
                          <th>${_lang.caseId}</th>
                          <th>${_lang.matter}</th>
                          <th>${_lang.taskId}</th>
                          <th>${_lang.task_description}</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr v-for="(singleTimeLog, index) in timeLogs" :key="index">
                          <td><input type=checkbox :value='singleTimeLog.id' v-model="selectedTimeLogs" /></td>
                          <td>{{singleTimeLog.log_date}}</td>
                          <td>{{singleTimeLog.comments}}</td>
                          <td>{{singleTimeLog.category}}</td>
                          <td>{{singleTimeLog.time_status}}</td>
                          <td>{{singleTimeLog.effective_effort}}</td>
                          <td>{{singleTimeLog.rate}}</td>
                          <td>{{singleTimeLog.user}}</td>
                          <td>{{singleTimeLog.user_code}}</td>
                          <td>{{singleTimeLog.matter_code}}</td>
                          <td>{{singleTimeLog.matter}}</td>
                          <td>{{singleTimeLog.task_id}}</td>
                          <td v-html="singleTimeLog.task_description"></td>
                      </tr>
                  </tbody>
              </table>
          </div>
          <div>
              <div class="col-sm-7">
                  <div class="col-sm-12">
                      <h6>${_lang.options}</h6>
                      <div class="form-group no-margin">
                          <input v-model="timeLogUserGroup" value="true" type="checkbox" class="form-check-input" id="chk">
                          <label class="form-check-label" for="chk"> &nbsp;${_lang.groupTimeLogsByLegalPractitioner}</label>
                      </div>
                      <h6>${_lang.helperInvoiceTimeLogs}</h6>
                      <div class="form-inline">
                          <div class="form-group no-margin">
                              <input type="radio" class="form-check-input" id="user-data-name" value="name" v-model="timeLogUserData">
                              <label class="form-check-label" for="user-data-name">&nbsp; ${_lang.userUserFullName}</label>
                          </div>
                          
                          <div class="form-group no-margin">
                              &nbsp;
                              <input type="radio" class="form-check-input" id="user-data-code" value="code" v-model="timeLogUserData">
                              <label class="form-check-label" for="user-data-code">&nbsp; ${_lang.useUserCode}</label>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-sm-5">
                  <div class="col-sm-12 no-padding mt-25">
                      <div v-if="showDiscount=='item_level' || showDiscount=='both_item_after_level' || showDiscount=='both_item_before_level'" class="col-sm-6 no-padding">
                          <label for="discount"> ${_lang.discount} (%) </label>
                          <select class="form-control" id="discount" @change="onDiscountChange($event)" v-model="selectedDiscount.discount_id">
                              <option selected value="0">${_lang.none}</option>
                              <option v-for="(discount, i) in data.discounts" :key="i"  :value="discount.id">
                                  {{ discount.name }} ({{ discount.percentage }} %)
                              </option>
                          </select>
                      </div>
                      <div v-if="showTax == '1'" class="col-sm-6">
                          <label for="tax">  ${_lang.tax} (%)</label>
                          <select class="form-control" id="tax" @change="onTaxChange($event)" v-model="selectedTax.tax_id">
                              <option selected value="0">${_lang.none} </option>
                              <option v-for="(tax, i) in taxesList" :key="i"  :value="tax.id">
                                  {{ tax.name }} ({{ tax.percentage }} %)
                              </option>
                          </select>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
      <div v-if="showSection=='expenses'">
        <div class="scroll">
            <table class="table table-bordered table-hover table-striped"> 
                <thead>
                    <tr>
                        <th><input type=checkbox checked="true" @change="checkAllExpenses($event.target.checked)" /> </th>
                        <th>${_lang.paidOn}</th>
                        <th>${_lang.expenseID}</th>
                        <th>${_lang.expenseCategory}</th>
                        <th>${_lang.expenseAmount}</th>
                        <th>${_lang.paidThrough}</th>
                        <th>${_lang.billingStatus}</th>
                        <th>${_lang.matter}</th>
                        <th>${_lang.comments}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(singleExpense, index) in expenses" :key="index">
                        <td><input type=checkbox :value='singleExpense.expense_id' v-model="selectedExpenses" /></td>
                        <td>{{singleExpense.paid_on}}</td>
                        <td>{{singleExpense.expense_id}}</td>
                        <td>{{singleExpense.category}}</td>
                        <td>{{singleExpense.amount}} {{singleExpense.currency}}</td>
                        <td>{{singleExpense.paid_through}}</td>
                        <td>{{singleExpense.billing_status}}</td>
                        <td>{{singleExpense.matter}}</td>
                        <td>{{singleExpense.comments}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
      <div v-if="showSection=='empty'">
            <div>
              ${ _lang.noTimelogNoExpenses}
            </div>
      </div>
    </div>
  </div>        
  <template #footer>
      <p-button label="${_lang.cancel}" icon="pi pi-times" @click="closeModal" class="p-button-text"></p-button>
      <button v-if="showSection=='empty'" type="button" class="btn btn-save save-button"  @click="submitModal">${_lang.finish}</button>
      <button v-else-if="showSection=='expenses' && expenses.length>0 " type="button" class="btn btn-save save-button"  @click="showStep('expenses')">${_lang.finish}</button>
      <button v-else-if="showSection=='timeLogs' && timeLogs.length>0" type="button" class="btn btn-save save-button"  @click="showStep('timeLogs')"><span v-if="expenses.length > 0" > ${_lang.next} </span><span v-else>${_lang.finish}</span></button>
      <button v-else-if="showSection=='matters'" type="button" class="btn btn-save save-button"  @click="showStep('matters')"> ${_lang.next}</button>
  </template>
</p-dialog>
`,
};
