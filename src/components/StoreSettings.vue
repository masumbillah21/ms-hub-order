<script setup>
import { ref, onMounted, watchEffect } from 'vue'
import Axios from 'axios';
import { useStoreStore  } from '../stores/store';

const storeStore = useStoreStore();

let url = mhoAdminLocalizer.apiUrl + '/mho/v1/store-settings'
const saveButtonText = ref('Save Settings');
const responseText = ref();

const form = ref([
  {
    name: 'webhook',
    value: '',
  },
  {
    name: 'token-key',
    value: '',
  },
])

const  saveSettings = async (e)  =>  {
  saveButtonText.value = 'Saving...';
  console.log(saveButtonText.value)
   await Axios.post( url, {
      webhook_url : form.value[0].value,
      store_token_key: form.value[1].value,
    } )
    .then( ( response ) => {
        responseText.value = response.data.message
    } )
    .catch( ( error ) => {
        responseText.value = error
    } ).finally(() => {
      saveButtonText.value = 'Save Settings';
    })
}

onMounted(() => {
  storeStore.fetchStoreSettings();
})

watchEffect(() => {
  if (storeStore.storeSettings) {
    form.value[0].value = storeStore.storeSettings.webhook_url
    form.value[1].value = storeStore.storeSettings.store_token_key
  }
})
</script>

<template>
  <h1>Store Settings</h1>
  <div id="mho-general-setting-tab" class="tab-container">
        <div v-if="responseText" class="notice notice-success"><strong>{{ responseText }}</strong></div>
        <form id="mho-general-setting-form" @submit.prevent="saveSettings">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="webhook">Webhook Url</label>
                        </th>
                        <td>
                            <input id="webhook" class="regular-text" v-model="form[0].value" type="url">
                        </td>
                    </tr>
                    <tr>
                      <th scope="row">
                            <label for="token-key">Token Key</label>
                        </th>
                        <td>
                            <input id="token-key" class="regular-text" type="text" v-model="form[1].value">
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">{{ saveButtonText }}</button>
            </p>
        </form>
        <div class="clear"></div>
    </div>
</template>

<style scoped>

.tab-container {
  width: 96%;
  min-height: 400px;
  padding: 30px;
  background-color: #fff;
  margin-top: -5px;
}
</style>