<template>
    <widget

        :title="'Last Transactions'"
        :paginator="transactions.paginator"

    >

        <md-card>
            <md-card-content>
                <md-table style="width:100%" v-model="transactions" md-card md-fixed-header>
                    <md-table-row
                        @click="loadTransaction(item.transaction_id)"
                        slot="md-table-row"
                        slot-scope="{ item }"
                    >
                        <md-table-cell
                            md-label="Payment Type"
                            md-sort-by="payment_type"
                            md-numeric
                        >{{ item.payment_type }}
                        </md-table-cell>
                        <md-table-cell md-label="Sender" md-sort-by="sender">{{ item.sender }}</md-table-cell>
                        <md-table-cell md-label="Amount" md-sort-by="amount">{{ item.amount + ' ' + appConfig.currency}}
                        </md-table-cell>
                        <md-table-cell md-label="Paid For" md-sort-by="paid_for_type">{{ item.paid_for_type }}
                        </md-table-cell>
                        <md-table-cell
                            md-label="Payment Service"
                            md-sort-by="payment_service"
                        >{{ item.payment_service }}
                        </md-table-cell>
                        <md-table-cell
                            md-label="Created At"
                            md-sort-by="paid_for_type"
                        >{{timeForHuman(item.created_at)}}
                        </md-table-cell>
                    </md-table-row>
                </md-table>
            </md-card-content>
            <md-card-actions>
                <div>
                    <div>
                        <div style="position: absolute; bottom: 10px ; right: 10px; font-size: 12px" role="status"
                             aria-live="polite">Showing {{from}} to {{to}} of {{total}} entries
                        </div>
                    </div>
                    <div>
                        <div class="paging_simple_numbers"
                             style="margin-bottom: 1.5rem;">
                            <ul class="pagination pagination-sm">
                                <li :class="currentPage>1 ? 'paginate_button previous' :' paginate_button previous disabled'"
                                    id="datatable_col_reorder_previous">
                                    <a href="javascript:void(0);" aria-controls="datatable_col_reorder"
                                       data-dt-idx="0"
                                       tabindex="0" @click="getTransactions(--currentPage)">Previous</a>
                                </li>

                                <li v-for="page in totalPages" :class="page==currentPage?' active':''"><a
                                    href="javascript:void(0);"
                                    @click="getTransactions(page)">{{page}}</a>
                                </li>
                                <li :class="currentPage< totalPages? 'paginate_button next':'paginate_button next disabled'"
                                    id="datatable_col_reorder_next">
                                    <a href="javascript:void(0);" aria-controls="datatable_col_reorder"
                                       data-dt-idx="8"
                                       tabindex="0" @click="getTransactions(++currentPage)">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </md-card-actions>


        </md-card>
    </widget>
</template>

<script>
    import { currency } from '../../mixins/currency'
    import { timing } from '../../mixins/timing'
    import Widget from '../../shared/widget'
    import Modal from '../../modal/modal'

    export default {
        name: 'Transactions',
        components: { Widget, Modal },
        mixins: [currency, timing],
        data () {
            return {
                articleClass: 'col-sm-12',
                personId: null,
                transactions: [],
                currentPage: 1,
                from: 0,
                to: 0,
                total: 0,
                totalPages: 0
            }
        },
        mounted () {
            this.personId = this.$store.getters.person.id
            this.getTransactions(0)
            pageSetUp()
            window.addEventListener('resize', this.handleResize)
        },

        methods: {
            handleResize () {
                console.log('resize')
            },
            toggleArticleClass () {
                if (this.articleClass === 'col-sm-6') {
                    this.articleClass = 'col-sm-12'
                } else {
                    this.articleClass = 'col-sm-6'
                }
            },
            getTransactions (page = 1) {
                if (page > this.totalPages) return
                axios.get(resources.person.list + '/' + this.personId + '/transactions?page=' + page)
                    .then(response => {
                        let responseData = response.data
                        this.transactions = responseData.data
                        this.currentPage = responseData.current_page
                        this.from = responseData.from
                        this.to = responseData.to
                        this.total = responseData.total
                        this.totalPages = responseData.last_page
                    })
            },
            loadTransaction (transactionId) {
                this.$router.push({ path: '/transactions/' + transactionId })
            }
        }
    }
</script>
<style scoped lang="scss">
    .pagination {
        color: #ac2925;
        list-style: none;
        display: flex;

        li {
            list-style: none;
            display: inline-block;
            padding: 5px;
            margin: 1px;
            background-color: #f7f7f7;
        }

        .active {
            background-color: #dddddd;
        }
    }
</style>
