<script setup>
    import { ref, reactive, onMounted, watchEffect } from 'vue'
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import '@bhplugin/vue3-datatable/dist/style.css';
    import AppModal from './AppModal.vue';
    import Axios from 'axios';
    import { useOrderStore } from '../stores/orders';

    const orderStore = useOrderStore();

    const responseText = ref();
    const isOpen = ref(false);
    const isUpdateModalOpen = ref(false);
    const updateOrderButtonText = ref('Update Order');

  
    let url = mhoAdminLocalizer.apiUrl + '/mho/v1/order-list'

    const form = ref([
        {
            name: 'order_id',
            value: '',
        },
        {
            name: 'note',
            value: '',
        },
        {
            name: 'order_status',
            value: '',
        },
    ])

    const updateOrder = async (e) => {
        updateOrderButtonText.value = 'Updating...';
        await Axios.post(url, {
            order_id: form.value[0].value,
            hub_notes: form.value[1].value,
            order_status: form.value[2].value,
        })
            .then((response) => {
                responseText.value = response.data.message
            })
            .catch((error) => {
                responseText.value = error
            }).finally(() => {
                updateOrderButtonText.value = 'Update Order';
                isUpdateModalOpen.value = false
            })
    }


    const showUpdateModal = (orderId) => {
        isUpdateModalOpen.value = true
        form.value[0].value = orderId
    }
    const params = reactive({
        current_page: 1,
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'asc',
        search: '',
    });

    const cols = ref([
        { title: 'SL', field: 'sl', isUnique: true, type: 'number', width: '100px', hide: false },
        { title: 'Order Id', field: 'order_id', hide: false },
        { title: 'Name', field: 'customer_name', hide: false },
        { title: 'Email', field: 'customer_email', hide: false },
        { title: 'Status', field: 'order_status', hide: false },
        { title: 'Order Date', field: 'order_date', hide: false },
        { title: 'Shipping Date', field: 'shipping_date', hide: false },
        { title: 'Action', field: 'action', hide: false },
    ])

    const rows = ref([{
        sl: '',
        order_id: '',
        customer_name: '',
        customer_email: '',
        order_status: '',
        order_date: '',
        shipping_date: '',
    }])

    const filterdOrderList = (data) => {
        params.current_page = data.current_page;
        params.pagesize = data.pagesize;
        params.column_filters = data.column_filters;
        params.search = data.search;

        if (data.change_type === 'search') {
            filterOrder();
        } else {
            orderStore.fetchOrders();
        }
    };

    const filterOrder = () => {
        clearTimeout(timer1);
        timer1 = setTimeout(() => {
            getUsers1();
        }, 300);
    };



    onMounted(() => {
        orderStore.fetchOrders();
    });

    watchEffect(() => {
        rows.value = orderStore.orders.map((item, index) => ({
            sl: index + 1,
            order_id: item.order_id,
            customer_name: item.customer_name,
            customer_email: item.customer_email,
            order_status: item.order_status,
            order_date: item.order_date,
            shipping_date: item.shipping_date,
        }));
    })


</script>

<template>
    <h2>Order List</h2>
    <div v-if="responseText" class="notice notice-success"><strong>{{ responseText }}</strong></div>
    <AppModal :isOpen="isUpdateModalOpen" @update:isOpen="isUpdateModalOpen = $event" :title="'Order Id:' + form[0].value">
        <form id="mho-order-update" @submit.prevent="updateOrder" method="post">

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="order-status">Order Status</label>
                        </th>
                        <td>
                            <select id="order_status" v-model="form[2].value" required>
                                <option value="" selected disabled>Select</option>
                                <option value="pending">Pending payment</option>
                                <option value="processing">Processing</option>
                                <option value="on-hold">On hold</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                                <option value="failed">Failed</option>
                                <option value="checkout-draft">Draft</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="notes">Notes</label>
                        </th>
                        <td>
                            <textarea id="notes" class="regular-text" v-model="form[1].value" required></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">{{ updateOrderButtonText }}</button>
            </p>
        </form>
    </AppModal>
    <div class="flex justify-between mb-5">
        <div class="relative">
            <button type="button" class="button-primary" @click="isOpen = !isOpen">
                Column Chooser
            </button>
            <ul v-if="isOpen" class="column-chooser">
                <li v-for="col in cols" :key="col.field">
                    <label>
                        <input type="checkbox" class="form-checkbox" :checked="!col.hide"
                            @change="col.hide = !$event.target.checked" />
                        <span>{{ col.title }}</span>
                    </label>
                </li>
            </ul>
        </div>
        <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Search..." />
    </div>
    <Vue3Datatable :rows="rows" :columns="cols" :sortable="true" :sortColumn="params.sort_column"
        :sortDirection="params.sort_direction" :search="params.search" :columnFilter="true" @change="filterdOrderList"
        :cloneHeaderInFooter="true" skin="bh-table-compact" class="column-filter p-4" rowClass="bg-white">
        <template #action="data">
            <button class="button button-primary" @click="showUpdateModal(data.value.order_id)">Update</button>
        </template>
    </Vue3Datatable>
</template>


<style scoped>

    .flex {
        display: flex
    }

    .justify-between {
        justify-content: space-between
    }
    .justify-around{
        justify-content: space-around
    }

    .mb-5 {
        margin-bottom: 5px
    }

    .ml-5 {
        margin-left: 5px
    }

    .relative {
        position: relative
    }

    .bg-white {
        background-color: white;
    }

    .column-chooser {
        position: absolute;
        left: 0;
        margin-top: 0.5px;
        padding: 2.5px;
        min-width: 150px;
        background-color: white;
        border-radius: 5px;
        z-index: 999;
    }
    .button.button-danger {
        background-color: red;
        color: #fff;
        border-color: red;
    }
    .warning{
        color: red;
        font-size: 20px;
        font-weight: bold;
    }
</style>