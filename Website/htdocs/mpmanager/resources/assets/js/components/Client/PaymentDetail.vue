<template>
    <div>
        <widget :title="' Payment Overview '+ period">
            <div slot="tabbar">
                <md-field>
                    <label>Period</label>
                    <md-select name="period" id="period" v-model="period" @md-selected="getFlow">
                        <md-option value="D">Daily</md-option>
                        <md-option value="W">Weekly</md-option>
                        <md-option value="M">Monthly</md-option>
                        <md-option value="Y">Annually</md-option>

                    </md-select>
                </md-field>
            </div>
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-medium-size-100 md-large-size-100 md-small-size-100">
                    <GChart
                        type="ColumnChart"
                        :data="chartData"
                        :options="chartOptions"
                        :resizeDebounce="500"
                    />
                </div>
            </div>

        </widget>
    </div>

</template>

<script>


    import { DonutChart } from 'vue-morris'
    import { BarChart } from 'vue-morris'
    import { GChart } from 'vue-google-charts'
    import Widget from '../../shared/widget'

    export default {
        name: 'PaymentDetail',

        data () {
            return {
                contentWidth: 0,
                personId: null,
                period: 'Monthly',
                chartData: [],
                chartOptions: {
                    chart: {
                        title: 'Customer Payment Flow',
                        subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                    },
                    colors: ['#FF6384', '#CC6384', '#36A2EB']
                },

                barData: [],
            }
        },
        created () {
            this.personId = this.$store.getters.person.id
        },
        mounted () {
            this.getFlow()
            /*  this.contentWidth = document.getElementById('client-payment-detail').clientWidth*/
        },
        components: {
            Widget,
            BarChart,
        },
        methods: {

            getFlow (period = 'M') {
                switch (period) {
                    case 'Y':
                        this.period = 'Annually'
                        break
                    case 'M':
                        this.period = 'Monthly'
                        break
                    case 'W':
                        this.period = 'Weekly'
                        break
                    case 'D':
                        this.period = 'Daily'
                        break

                }
                axios.get(resources.paymenthistories + this.personId + '/payments/' + period)
                    .then(response => {
                        this.chartData = [['Period', 'Energy', 'AccessRate', 'Deferred Payments']]

                        let data = response.data
                        for (let x in data) {
                            let items = []
                            items = [
                                x,
                                'energy' in data[x] ? parseInt(data[x]['energy']) : 0,
                                'access rate' in data[x] ? parseInt(data[x]['access rate']) : 0,
                                'deferred payment' in data[x] ? parseInt(data[x]['energy']) : 0,
                            ]
                            this.chartData.push(items)
                        }
                    })

            }
        }
    }
</script>
<style scoped>
    .payment-period-select {
        float: right;
        padding-right: 2.5rem !important;
        padding-left: 2.5rem !important;
    }
</style>
