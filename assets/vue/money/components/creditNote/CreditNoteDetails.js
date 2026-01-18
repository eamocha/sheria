import ItemRecord from "ItemRecord";
import { isTrue } from "Utils";

export default {
    name: "InvoiceDetails",
    components: { "item-record": ItemRecord, "p-datatable": primevue.datatable },
    emits: [
        "handle-change",
        "handle-partners",
        "handle-new-tax",
        "handle-new-item",
    ],
    props: [
        "timeLogs",
        "expenses",
        "items",
        "withTax",
        "withDiscount",
        "withDate",
        "withCommission",
        "currency",
        "exchangeRate",
        "validate",
        "disabled",
        "itemsList",
        "taxesList",
        "creditNoteDiscountPercent",
    ],

    setup(props, context) {
        const { ref, watch } = Vue;
        const data = ref(frontEndData);
        const timeLogs = ref(props.timeLogs);
        const expenses = ref(props.expenses);
        const items = ref(props.items);
        const itemsList = ref(props.itemsList);
        const taxesList = ref(props.taxesList);
        const currency = ref(props.currency);
        const exchangeRate = ref(props.exchangeRate);
        const validate = ref(props.validate);
        const disabled = ref(props.disabled);
        const withTax = ref(isTrue(props.withTax));
        const withDiscount = ref(props.withDiscount);
        const withDate = ref(isTrue(props.withDate));
        const creditNoteDiscountPercent = ref(props.creditNoteDiscountPercent);

        watch(props, (newValue, oldValue) => {
            exchangeRate.value = props.exchangeRate;
            currency.value = props.currency;
            timeLogs.value = props.timeLogs;
            expenses.value = props.expenses;
            items.value = props.items;
            itemsList.value = props.itemsList;
            taxesList.value = props.taxesList;
            validate.value = props.validate;
            disabled.value = props.disabled;
            withTax.value = isTrue(props.withTax);
            withDiscount.value = props.withDiscount;
            withDate.value = isTrue(props.withDate);
            creditNoteDiscountPercent.value = props.creditNoteDiscountPercent;
        });

        const handleObjectChange = (obj) => {
            context.emit("handle-change", { name: obj.name, value: obj.value });
        };

        const handlePartners = (obj) => {
            context.emit("handle-partners", obj);
        };

        const handleNewTax = (obj) => {
            context.emit("handle-new-tax", obj);
        };

        const handleNewItem = (obj) => {
            context.emit("handle-new-item", obj);
        };

        return {
            data,
            timeLogs,
            expenses,
            items,
            itemsList,
            taxesList,
            currency,
            exchangeRate,
            withTax,
            withDiscount,
            withDate,
            withCommission: props.withCommission,
            validate,
            disabled,
            creditNoteDiscountPercent,
            handleObjectChange,
            handlePartners,
            handleNewTax,
            handleNewItem,
        };
    },
    template: `
  <div>
  <table class="table m-0">
      <thead class="invoice-rows-type">
          <tr>
              <th v-if="withDate">${_lang.date}</th>
              <th>${_lang.item}</th>
              <th style="width: 20%;">${_lang.description}</th>
              <th style="width: 8%;">${_lang.quantity}</th>
              <th v-if="!currency">${_lang.unitPrice}</th>
              <th v-if="currency">${_lang.unitPrice} ({{currency}})</th>
              <th style="width: 10%;" v-if="withDiscount == 'item_level' || withDiscount == 'both_item_after_level' || withDiscount == 'both_item_before_level'">${_lang.discount}</th>
              <th v-if="withTax">${_lang.tax} (%)</th>
              <th v-if="!currency" class="text-center">${_lang.amount}</th>
              <th v-if="currency" class="text-center">${_lang.amount} ({{currency}})</th>
              <th style="width: 2%;"></th>
          </tr>
      </thead>
      <item-record
          @handle-change="handleObjectChange"
          @handle-partners="handlePartners"
          @handle-new-tax="handleNewTax"
          @handle-new-item="handleNewItem"
          :itemsList="itemsList"
          :taxesList="taxesList"
          :withTax="withTax"
          :withDiscount="withDiscount"
          :withDate="withDate"
          category="invoice_expenses"
          :withCommission="withCommission"
          :type="data.labels.expenses"
          :expenses="expenses"
          :currency="currency"
          :exchangeRate="exchangeRate"
          :validate="validate"
          :creditNoteDiscountPercent="creditNoteDiscountPercent"
          :disabled="disabled"
      ></item-record>
      <item-record
          @handle-change="handleObjectChange"
          @handle-partners="handlePartners"
          @handle-new-tax="handleNewTax"
          @handle-new-item="handleNewItem"
          :itemsList="itemsList"
          :taxesList="taxesList"
          :withTax="withTax"
          :withDiscount="withDiscount"
          :withDate="withDate"
          category="invoice_time_logs"
          :withCommission="withCommission"
          :type="data.labels.time_logs"
          :timeLogs="timeLogs"
          :currency="currency"
          :exchangeRate="exchangeRate"
          :validate="validate"
          :creditNoteDiscountPercent="creditNoteDiscountPercent"
          :disabled="disabled"
      ></item-record>
      <item-record
          @handle-change="handleObjectChange"
          @handle-partners="handlePartners"
          @handle-new-tax="handleNewTax"
          @handle-new-item="handleNewItem"
          :itemsList="itemsList"
          :taxesList="taxesList"
          :withTax="withTax"
          :withDiscount="withDiscount"
          :withDate="withDate"
          category="invoice_details"
          :withCommission="withCommission"
          :type="data.labels.items"
          nbRecords="1"
          :items="items"
          :timeLogs="timeLogs"
          :expenses="expenses"
          :currency="currency"
          :exchangeRate="exchangeRate"
          :validate="validate"
          :creditNoteDiscountPercent="creditNoteDiscountPercent"
          :disabled="disabled"
      ></item-record>
  </table>
</div>
    `,
};