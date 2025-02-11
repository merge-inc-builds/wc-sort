<template>
  <div></div>
  <Teleport v-if="firstNoticeTarget && message" :to="firstNoticeTarget">
    <div id="wc-sort-frontend-message" v-html="message"></div>
  </Teleport>
  <Teleport v-if="processingLoadingTarget" :to="processingLoadingTarget">
    <div id="wc-sort-processing-frontend"
         style="display: flex; align-items: center; gap: 6px; font-style: italic; margin-top: 10px;">
      <UiSpinner v-if="processingLoading"/>
      Products processing.. <span v-if="processingPage">Next
        page to process is {{ processingPage }}</span>
    </div>
  </Teleport>
</template>

<script lang="ts" setup>
import {onMounted, ref} from 'vue';
import {getMessage, getMetaKeysProgress} from './assets/js/api';
import UiSpinner from './components/UiSpinner.vue';
import {debugConsole} from './assets/js/general';

debugConsole.log('vue3 with vite loaded!')

const message = ref<string>('');
const firstNoticeTarget = ref<string>('');
const processingLoadingTarget = ref<string>('');
const processingLoading = ref<boolean>(false);
const processingPage = ref<number>(0);

const init = async () => {
  const firstNotice = document.querySelector('#wc-sort-generic-message-container') as HTMLElement
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const url = (window as any)?.wc_sort_data?.externalUrlMessage
  const data = await getMessage({url});
  // debugConsole.log('data:', data)
  if (data?.message) {
    message.value = data.message

    if (firstNotice) {
      firstNotice.style.display = 'block'
      firstNoticeTarget.value = '#wc-sort-generic-message-container #wc-sort-generic-message'
    }
  }
}

const setupFreemiumWarning = () => {
  const checkbox = document.querySelector('#ms-settings-field-freemium-activated') as HTMLInputElement;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const defaults = (window as any).wc_sort_data?.settings?.defaults;

  if (checkbox && defaults && Object.keys(defaults).length > 0) {
    checkbox.addEventListener('click', (eve: Event) => {
      debugConsole.log('click')
      let changedValues = false;

      for (const def of Object.keys(defaults)) {
        const val = defaults[def];
        const input = document.querySelector(`#${def}`) as HTMLInputElement;
        // debugConsole.log('input:', input, val, input?.value)
        if (input?.value !== val) {
          changedValues = true;
        }
      }

      if (changedValues) {
        debugConsole.log('changed values');

        if (!confirm('Are you sure you want to deactivate freemium features? Some freemium fields have been changed, and will be reverted back to the defaults.')) {
          eve.stopPropagation();
          eve?.preventDefault()
        }
      }
    })
  }
}

const wait = (ms: number) => {
  return new Promise(resolve => setTimeout(resolve, ms));
}

const metaKeysClickListener = async (eve: Event) => {
  debugConsole.log('processing click', eve)

  eve.target?.removeEventListener('click', metaKeysClickListener);
  do {
    processingLoading.value = true;
    if (document.querySelector('#wc-sort-meta-keys-progress')) {
      processingLoadingTarget.value = '#wc-sort-meta-keys-progress';
    }
    const response = await getMetaKeysProgress({url: metaKeysEndpoint.value});
    if (response?.nextPageToProcess && response?.nextPageToProcess !== 0) {
      processingPage.value = response.nextPageToProcess;
    } else if (response?.nextPageToProcess === 0 || !!response?.nextPageToProcess) {
      window.location.reload();
      processingLoading.value = false;
      processingLoadingTarget.value = '';
      processingPage.value = 0;
    }

    await wait(1000);
  } while (processingLoading.value)
}

const setupProcessing = () => {
  const processing = document.querySelector('#wc-sort-start-products-creation-ajax') as HTMLAnchorElement;

  if (processing && metaKeysEndpoint.value) {
    processing.addEventListener('click', metaKeysClickListener)
  }
}

const metaKeysEndpoint = ref<string>('');

onMounted(async () => {
  await init();
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  metaKeysEndpoint.value = (window as any)?.wc_sort_data?.externalUrlMetaKeysCreation;

  setupFreemiumWarning();
  setupProcessing();
})
</script>

<style scoped></style>
