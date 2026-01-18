export default {
    name: 'DateFilter',
    props: ['filterdate'],

    setup(props) {
        const { ref, watch } = Vue;

        const showToDate = ref(false);
        const date = ref({
            operator: "eq",
            from: moment().locale('en').toDate().format('Y-m-d'),
            to: moment().locale('en').toDate().format('Y-m-d'),
        });
        watch(date.value, () => {
            showToDate.value = (date.value.operator == "btw") ? true : false
            props.filterdate(date.value)
        })
        return {
            date,
            showToDate,
        };
    },
    template: `
        <div class="form-group px-1">
            <label for="date">` + _lang.date + ` &nbsp;</label>
            <select class="form-control" v-model="date.operator">
                <option value="eq"> = </option>
                <option value="neq"> != </option>
                <option value="lt"> &lt; </option>
                <option value="lte"> &lt;= </option>
                <option value="gt"> &gt; </option>
                <option value="gte"> &gt;= </option>
                <option value="btw">Between</option>
            </select>
        </div>
        <div class="form-group px-1">
            <label></label>
            <input type="date" class="form-control" v-model="date.from">
        </div>
        <div class="form-group px-1" v-if="showToDate">
            <label></label>
            <input type="date" class="form-control" v-model="date.to">
        </div>
    `,
};