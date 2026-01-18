import PartnerLookup from "PartnerLookup";

export default {
    name: 'ItemRecord',
    components: {
        'p-inputtext': primevue.inputtext,
        'p-inputnumber': primevue.inputnumber,
        'p-dropdown': primevue.dropdown,
        'p-dialog': primevue.dialog,
        'p-button': primevue.button,
        'p-autocomplete': primevue.autocomplete,
        'p-divider': primevue.divider,
        'p-textarea': primevue.textarea,
        'partner-lookup': PartnerLookup,
    },
    props: ['withTax', 'withDiscount', 'withDate', 'withCommission', 'currency', 'exchangeRate', 'type', 'category', 'nbRecords', 'expenses', 'timeLogs', 'items', 'validate', 'disabled', 'itemsList', 'taxesList', 'creditNoteDiscountPercent'],
    emits: ['handle-change', 'handle-partners', 'handle-new-tax', 'handle-new-item'],

    setup(props, context) {
        const { ref, watch } = Vue;
        const frontData = ref(frontEndData);
        const itemRecords = ref([]);
        const currency = ref(props.currency);
        const exchangeRate = ref(props.exchangeRate);
        const showValidation = ref(props.validate);
        const disabled = ref(props.disabled);
        const itemsList = ref(props.itemsList);
        const timeLogSummary = ref([]);
        const itemsShowDiscount = ref(props.withDiscount);
        const itemsShowTax = ref(props.withTax);
        const itemsShowDate = ref(props.withDate);
        const taxesList = ref(props.taxesList);
        const creditNoteDiscountPercent = ref(props.creditNoteDiscountPercent);
        let isFirstLoad = true;

        watch(props, (newValue, oldValue) => {
            showValidation.value = props.validate;
            disabled.value = props.disabled;
            currency.value = props.currency;
            exchangeRate.value = props.exchangeRate;
            itemsList.value = props.itemsList;
            taxesList.value = props.taxesList;
            let isCreditNoteDiscChange = (creditNoteDiscountPercent.value !== props.creditNoteDiscountPercent);
            creditNoteDiscountPercent.value = props.creditNoteDiscountPercent;

            itemsShowDiscount.value = props.withDiscount;
            itemsShowTax.value = props.withTax;
            itemsShowDate.value = props.withDate;

            if (props.category == "invoice_details") {
                if (props.items.length == 0) {
                    if (props.expenses.length == 0 && props.timeLogs.length == 0) {
                        itemRecords.value.length = 0
                        itemRecords.value.push({
                            "account_id": null,
                            "item_id": null,
                            "tax_id": null,
                            "discount_id": null,
                            "item_title": 0,
                            "unit_price": 0,
                            "quantity": 1,
                            "discount_amount": 0,
                            "discount_percentage": 0,
                            "tax_percentage": 0,
                            "item_description": "",
                            "item_date": "",
                            "partner_shares": [],
                            "discount_type": "percentage",
                            "line_sub_total": 0,
                            "tax_amount": 0,
                            "sub_total_after_line_disc": 0,
                            "total": 0
                        });
                    }
                } else {
                    itemRecords.value = props.items;
                }
            }
            if (props.category == "invoice_expenses") {
                itemRecords.value = props.expenses;
            }
            if (props.category == "invoice_time_logs") {
                itemRecords.value = props.timeLogs;
                fillTimeLogSummary();
            }
            //here since the change maybe in the credit note discount percent so we must recalculate to get new tax amount this 
            //case when discount before tax, so we need the credit note discount percent
            if (isCreditNoteDiscChange && (itemsShowDiscount.value == 'invoice_level_before_tax' || itemsShowDiscount.value == 'both_item_before_level'))
                updateAllItemsDataCalc();
            //
            itemRecords.value.map((item, i) => {
                if (item._updateItemDataCalc == undefined) {
                    item._updateItemDataCalc = function () {
                        updateItemDataCalc(item);
                    }
                }
                //when click to link matters and we add timelog/expenses row this new rows need to calculate the row data
                //we do that for example by checking if the total property is not defined.
                if (item.total == undefined || item.sub_total_after_line_disc == undefined)
                    updateItemDataCalc(item);
            });
            if (isInvoiceDataLoaded && isFirstLoad)
                isFirstLoad = false;
        });

        // just for credit note items details
        watch(itemRecords.value, (newValue, oldValue) => {
            context.emit('handle-change', { name: props.category, value: newValue });
        });

        const updateAllItemsDataCalc = (item) => {
            itemRecords.value.map((item, i) => {
                updateItemDataCalc(item);
            });
        }

        const updateItemDataCalc = (item) => {
            item.line_sub_total = accounting.roundNumber(accounting.multiplyNumbers(item.quantity, item.unit_price));
            let subTotalBeforeTax = item.line_sub_total;
            if (showLineDiscount()) {
                if (item.discount_type == "amount")
                    item.discount_percentage = (accounting.multiplyNumbers(item.discount_amount, 100) / item.line_sub_total);
                else
                    item.discount_amount = accounting.roundNumber(accounting.multiplyNumbers(item.line_sub_total, item.discount_percentage) / 100);
                //this case may occur if discount type is amount and price 124 and put discount 60 then change price to 12 only
                if (item.discount_percentage < 0 || item.discount_percentage > 100) {
                    item.discount_percentage = 0;
                    item.discount_amount = 0;
                }
                subTotalBeforeTax = accounting.subtractNumbers(subTotalBeforeTax, item.discount_amount);
            }
            item.sub_total_after_line_disc = subTotalBeforeTax;  //sub-total price after line discount only
            let diff;
            if (itemsShowDiscount.value == 'item_level' || itemsShowDiscount.value == 'both_item_after_level') {
                diff = accounting.subtractNumbers(item.line_sub_total, item.discount_amount);
            } else if (itemsShowDiscount.value == 'invoice_level_before_tax' || itemsShowDiscount.value == 'both_item_before_level') {
                let tempSubTotal = accounting.subtractNumbers(item.line_sub_total, item.discount_amount);
                diff = accounting.subtractNumbers(tempSubTotal, (accounting.multiplyNumbers(tempSubTotal, (creditNoteDiscountPercent.value / 100))))
            } else {
                diff = item.line_sub_total;
            }
            item.tax_amount = (itemsShowTax.value ? accounting.roundNumber(accounting.multiplyNumbers(diff, (accounting.toNumber(item.tax_percentage) / 100))) : 0);
            item.total = item.sub_total_after_line_disc;
            if (itemsShowDiscount.value != 'invoice_level_before_tax' && itemsShowDiscount.value != 'both_item_before_level')
                item.total = accounting.addNumbers(item.total, item.tax_amount);
        }

        const addItemRecord = () => {
            itemRecords.value.push({
                "account_id": null,
                "item_id": null,
                "tax_id": null,
                "discount_id": null,
                "item_title": 0,
                "unit_price": 0,
                "quantity": 1,
                "discount_amount": 0,
                "discount_percentage": 0,
                "tax_percentage": 0,
                "item_description": "",
                "item_date": "",
                "partner_shares": [],
                "discount_type": "percentage",
                "line_sub_total": 0,
                "tax_amount": 0,
                "sub_total_after_line_disc": 0,
                "total": 0
            });
        }

        const removeItemRecord = (index) => {
            itemRecords.value.splice(index, 1);
            updateAllItemsDataCalc();
        }

        const onItemChange = (event, data) => {
            let selectedItem = event.value ?? 0;
            itemsList.value.map((item) => {
                if (selectedItem == item.id) {
                    data.item_id = item.id;
                    data.unit_price = accounting.roundNumber(accounting.toNumber(item.price) / accounting.toNumber(exchangeRate.value));
                    data.account_id = parseFloat(item.account_id);
                    data.item_description = item.description;
                    data.item = item.name;
                }
            });
            updateItemDataCalc(data);
        }

        const onDiscountType = (event, data) => {
            let selected = event.target.value;
            data.discount_id = null;
            data.discount_percentage = 0;
            data.discount_amount = 0;
            data.discount_type = (selected === "amount" ? 'amount' : "percentage");
            if (selected != "amount" && selected != "percentage") {
                data.discount_id = selected;
                frontData.value.discounts.map((item) => {
                    if (selected == item.id) {
                        data.discount_percentage = item.percentage;
                        data.discount_id = item.id;
                    }
                })
            }
            updateItemDataCalc(data);
        }

        const onTaxChange = (event, data) => {
            data.tax_percentage = 0;
            data.tax_amount = 0;
            data.tax_id = null;
            let selectedTax = event.value ?? 0;
            taxesList.value.map((tax) => {
                if (selectedTax == tax.id) {
                    data.tax_percentage = accounting.toNumber(tax.percentage);
                    data.tax_id = tax.id;
                }
            });
            updateItemDataCalc(data);
        }

        const validateDiscountNumber = (event, data) => {
            if (event.value > data.line_sub_total) {
                data.discount_amount = 0;
            }
            //we update it here, since if we log here data.discount_amount will still get the old value not updated yet 
            //i think this occur when we use "<p-inputnumber .." not the native "<input .."
            else
                data.discount_amount = accounting.toNumber(event.value);
            updateItemDataCalc(data);
        }

        const validateDiscountPercentage = (event, data) => {
            let numberPattern = /^(\d+\.?\d{0,2}|\.\d{1,2})$/;
            if (!numberPattern.test(event.value) || Math.abs(event.value) > 100) {
                data.discount_percentage = 0;
            } else
                data.discount_percentage = accounting.toNumber(event.value);
            updateItemDataCalc(data);
        }

        const onItemQtyChange = (event, data) => {
            data.quantity = accounting.toNumber(event.value);
            updateItemDataCalc(data);
        }

        const onItemPriceChange = (event, data) => {
            data.unit_price = accounting.toNumber(event.value);
            updateItemDataCalc(data);
        }

        const showPartnerSharesModal = (data, index, action = "") => {
            context.emit('handle-partners', { data: data.partner_shares ? data.partner_shares : [], index: index, action: action, category: props.category });
        }

        const mergeGroupBy = (array) => {
            let result = []
            for (const val in array) {
                let obj = {};
                let quantity = 0;
                array[val].map((time) => {
                    quantity = Number(quantity) + Number(time['quantity']);
                    obj.userCode = time['timelog_data'][0]['user_code'];
                    obj.userName = time['timelog_data'][0]['user_full_name'];
                    obj.unitPrice = time['unit_price'];
                    obj.quantity = quantity;
                })
                result.push(obj);
            }
            return result;
        }

        const fillTimeLogSummary = () => {
            timeLogSummary.value = [];
            let result = {};
            let key = "";
            itemRecords.value.map((itemSingle) => {
                key = itemSingle['timelog_data'][0]['user_id'] + "_" + itemSingle['unit_price'];
                if (!result[key]) result[key] = []
                result[key].push(itemSingle)
            })
            timeLogSummary.value = mergeGroupBy(result);
        }

        const formatTotalAmount = (val) => {
            return accounting.formatMoney(accounting.roundNumber(val), "");
        }

        const calcPartnerCommissionAmount = function (vals, commission_percent) {
            let commission_amount = accounting.toNumber(vals.sub_total_after_line_disc);
            return (commission_amount * commission_percent / 100);
        }

        const showNewTaxModal = (index) => {
            context.emit('handle-new-tax', { index: index, category: props.category });
        }
        const showNewItemModal = (index) => {
            context.emit('handle-new-item', { index: index, category: props.category });
        }

        const showLineDiscount = () => {
            return (itemsShowDiscount.value == 'item_level' || itemsShowDiscount.value == 'both_item_after_level' || itemsShowDiscount.value == 'both_item_before_level');
        }

        return {
            frontData,
            itemsList,
            taxesList,
            discounts: frontData.value.discounts,
            itemRecords,
            itemsShowDiscount,
            itemsShowTax,
            itemsShowDate,
            itemsShowPartners: props.withCommission,
            category: props.category,
            recordsType: props.type,
            currency,
            exchangeRate,
            showValidation,
            disabled,
            timeLogSummary,
            accounting,
            
            addItemRecord,
            removeItemRecord,
            onItemChange,
            onItemQtyChange,
            onItemPriceChange,
            onDiscountType,
            onTaxChange,
            validateDiscountNumber,
            validateDiscountPercentage,
            formatTotalAmount,
            calcPartnerCommissionAmount,
            showPartnerSharesModal,
            showNewItemModal,
            showNewTaxModal,
            showLineDiscount,
            updateItemDataCalc,
        }
    },
    template: `
  <tr v-if="itemRecords.length > 0">
  <th colspan="100%">
      <p-divider align="center">
          <div class="p-d-inline-flex p-ai-center">
          <span v-if="category == 'invoice_details'">
          <i class="pi pi-list m-2"></i>
              <b>${_lang.items}</b>
          </span>
              <span v-else-if="category == 'invoice_time_logs'">
               <i class="pi pi-clock m-2"></i>
              <b>${_lang.timeLogs}</b>

              </span>

              <span v-else-if="category == 'invoice_expenses'">
              <i class="pi pi-money-bill m-2"></i>
              <b>${_lang.expenses}</b>
              </span>
             
              
          </div>
      </p-divider>
      </th>
      </tr>

          <tbody class="row-item" :class="category" v-for="(record, index) in itemRecords" :key="index">
              <tr>
                  <td v-if="itemsShowDate">
                      <input :disabled="category != 'invoice_details' || disabled" :class="{ 'p-invalid' : (!record.item_date && showValidation)}" type="date" v-model="record.item_date" class="form-control" />
                      <small v-if="!record.item_date && showValidation" class="input-invalid"> ${_lang.requiredField}</small>    
                   </td>
                  <td>
                      <p-dropdown v-if="category == 'invoice_details'" :options="itemsList" option-label="name" option-value="id" v-model="record.item_id" :class="{'p-invalid': category == 'invoice_details' && !record.item_id && showValidation}"
                          placeholder="${_lang.money.selectService}" @change="onItemChange($event, record)" :disabled=disabled :filter="true" :show-clear="true" style="width:100%;">   
                          <template #option="slotProps">
                              <div class="item-li">
                                  <div v-if="slotProps.option.isParent"><strong>{{slotProps.option.name}} </strong></div>
                                  <div v-else class="pl-10">{{slotProps.option.name}} </div>
                              </div>
                          </template>
                      </p-dropdown>
                      <span v-else-if="category == 'invoice_time_logs'">
                        <p-inputtext :disabled="true" class="w-full" inputClass="w-full" v-model="record.item_title"></p-inputtext>
                        <input type="hidden" class="form-control" v-model=record.time_log_id />
                      </span>
                      <span v-else-if="category == 'invoice_expenses'">
                      <p-inputtext :disabled="true" class="w-full" inputClass="w-full" v-model="record.item_title"></p-inputtext>
                          <input type="hidden" class="form-control" v-model=record.expense_id />
                      </span>
                      <small v-if="category == 'invoice_details' && !record.item_id && showValidation" class="input-invalid">${_lang.requiredField}</small>      
                  </td>
                  <td>
                    <p-textarea :disabled="disabled" class="w-full" inputClass="w-full" v-model="record.item_description" rows="1" cols="30"></p-textarea>
                  </td>
                  <td>
                      <span>
                          <p-inputnumber :disabled=disabled v-model="record.quantity" :min-fraction-digits="0" maxFractionDigits="2"  :min="0" @input="onItemQtyChange($event, record)" :class="{ 'required-field' : ((record.quantity < 0 || record.quantity == '') && showValidation)}" ></p-inputnumber>
                          <small v-if="((record.quantity < 0 || record.quantity == '') && showValidation)" class="input-invalid"> ${_lang.requiredField}</small>
                      </span>
                  </td>
                  <td>
                      <div class="p-inputgroup">
                          <p-inputnumber :disabled=disabled v-model="record.unit_price" :min-fraction-digits="0" :max-fraction-digits="2" :min="0" @input="onItemPriceChange($event, record)" :class="{'p-invalid': ((accounting.toNumber(record.unit_price) < 0 || record.unit_price == '')  && showValidation)}"></p-inputnumber>
                          <span :class="{'gray-bg': disabled}" class="p-inputgroup-addon">{{currency}}</span>
                      </div>
                      <small v-if="((accounting.toNumber(record.unit_price) < 0 || record.unit_price == '')  && showValidation)" class="input-invalid"> ${_lang.requiredField}</small>
                  </td>
                  <td v-if="showLineDiscount()">
                    <div class="flex justify-content-end">
                      <p-inputnumber :min-fraction-digits="0" maxFractionDigits="2" :disabled="disabled" v-if="record.discount_type === 'amount'" inputClass="w-6rem" @input="validateDiscountNumber($event, record)" v-model="record.discount_amount"></p-inputnumber>
                      <p-inputnumber :min-fraction-digits="0" maxFractionDigits="2" suffix="%" inputClass="w-6rem" :disabled="disabled || record.discount_id" v-else v-model="record.discount_percentage" @input="validateDiscountPercentage($event, record)"></p-inputnumber>
                      <select :disabled="disabled" @change="onDiscountType($event, record)" class="w-8rem border-300">
                        <option value="percentage">${_lang.percentage}</option>
                        <option v-for="(discount, i) in discounts" :key="i" :selected="record.discount_id == discount.id" :value="discount.id">
                        {{ discount.name }} ({{ discount.percentage }} %)
                        </option>
                        <option value="amount" :selected="record.discount_type == 'amount'" > {{currency}} </option>
                      </select>
                    </div>
                  </td>
                  <td v-if="itemsShowTax">
                      <p-dropdown :options="taxesList" option-label="label" option-value="id" v-model="record.tax_id"
                          placeholder="${_lang.money.selectTax}" @change="onTaxChange($event, record)" :disabled=disabled :filter="true" :show-clear="true" style="width:100%;">
                  </td>
                  <td class="text-center font-size-14 single-record-total"> 
                      <strong>{{ formatTotalAmount(record.total) }}</strong>
                  </td>
                  <td>
                      <div v-if="!disabled" class="row">
                      <p-button icon="pi pi-trash" class="p-button-rounded p-button-text"  @click="removeItemRecord(index)"></p-button>
                      </div>
                  </td>
              </tr>
              <tr v-if="itemsShowPartners">
                  <td colspan=10>
                      <div class="">
                          <ul class="list-inline">
                              <li class="list-inline-item" v-if="!disabled">
                                  <a href="javascript:;" @click="showPartnerSharesModal(record, index,'add')"> <strong> <i class="fa fa-fw fa-plus"></i>${_lang.addPartnersShares}</strong></a>
                              </li>
                              <li class="list-inline-item" v-for="(partner_share, pIndex) in record.partner_shares" :key="pIndex">
                                  <strong>
                                  (
                                      {{ partner_share.partner_name }}
                                      - {{ partner_share.partner_commission }}% 
                                      -  {{ formatTotalAmount(calcPartnerCommissionAmount(record, partner_share.partner_commission)) }} {{currency}})
                                  </strong>
                              </li>
                              <li class="list-inline-item" v-if="record.partner_shares.length > 0 && !disabled">
                                  <a class="blue-color" href="javascript:;" @click="showPartnerSharesModal(record, index)"><strong>${_lang.edit}</strong></a>
                              </li>
                          </ul>
                      </div>
                  </td>
              </tr>
          </tbody>
          <tbody class="row-item" :class="category" v-if="category == 'invoice_time_logs' && itemRecords.length > 0 && disabled">
              <tr>
                  <td colspan=10 class="no-padding-top">
                      <p>
                          <strong>${_lang.timeLogsSummary} </strong> 
                          <a href="javascript:;" class="pull-right1" data-toggle="collapse" data-target="#collapse-invoice-time-log-summary"><i class="fa fa-fw fa-plus-circle"></i></a>
                      </p>
                      <div id="collapse-invoice-time-log-summary" class="collapse">
                          <table width=50%>
                              <tr v-for="(timeSummary, ind) in timeLogSummary" :key="ind">
                                  <td>{{timeSummary.userCode}}</td>
                                  <td>{{timeSummary.userName}}</td>
                                  <td>{{timeSummary.quantity}} {{frontData.labels.Time_Logs_Quantity}}</td>
                                  <td>{{timeSummary.unitPrice}} {{currency}}/{{frontData.labels.time_logs_quantity_unit}}</td>
                                  <td>{{formatTotalAmount(accounting.toNumber(timeSummary.unitPrice) * accounting.toNumber(timeSummary.quantity))}} {{currency}} </td>
                              </tr>
                          </table>
                      </div> 
                  </td>
              </tr>
          </tbody>
          <tbody class="row-item" :class="category" v-if="category == 'invoice_details' && !disabled">
              <tr>
                  <td colspan=10 class="text-center">
                      <p-button label="${_lang.addNewLine}" @click="addItemRecord()" icon="pi pi-plus-circle" class="p-button-text"></p-button>
                  </td>
              </tr>
          </tbody>
      `,
};