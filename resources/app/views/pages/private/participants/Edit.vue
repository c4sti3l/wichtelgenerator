<template>
  <Page
      :title="page.title"
      :breadcrumbs="page.breadcrumbs"
      :actions="page.actions"
      @action="onAction"
      :is-loading="page.loading"
  >
    <Panel>
      <Form id="edit-participant" @submit.prevent="onSubmit">
        <TextInput
            class="mb-4"
            type="text"
            :required="true"
            name="name"
            v-model="form.name"
            :label="trans('users.labels.name')"
        />
        <TextInput
            class="mb-4"
            type="text"
            :required="true"
            name="phone"
            v-model="form.phone"
            :label="trans('participants.labels.phone')"
        />
      </Form>
    </Panel>
  </Page>
</template>

<script lang="ts" setup>
import {reactive, onBeforeMount} from 'vue';
import {trans} from '@/helpers/i18n';
import {fillObject, reduceProperties} from '@/helpers/data';
import {useRoute} from 'vue-router';
import {useAuthStore} from '@/stores/auth';
import {toUrl} from '@/helpers/routing';
import ParticipantService from "@/services/ParticipantService";
import TextInput from '@/views/components/input/TextInput.vue';
import Panel from '@/views/components/Panel.vue';
import Page from '@/views/layouts/Page.vue';
import Form from '@/views/components/Form.vue';

const {user} = useAuthStore();
const route = useRoute();

const form = reactive({
  name: '',
  phone: '',
});

const page = reactive({
  id: 'edit_participant',
  title: trans('global.pages.participants_edit'),
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: trans('global.pages.participants'),
      to: toUrl('/participants'),
    },
    {
      name: trans('global.pages.participants_edit'),
      active: true,
    },
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: 'fa fa-angle-left',
      to: toUrl('/participants'),
      theme: 'outline',
    },
    {
      id: 'submit',
      name: trans('global.buttons.update'),
      icon: 'fa fa-save',
      type: 'submit',
    },
  ],
});

const service = new ParticipantService();

onBeforeMount(() => {
  service.edit(route.params.id).then((response) => {
    fillObject(form, response.data.model);
    page.loading = false;
  });
});

const onAction = (data: any) => {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
};

const onSubmit = () => {
  service.handleUpdate(
      'edit-participant',
      route.params.id,
      reduceProperties(form, ['roles'], 'id')
  );
  return false;
};
</script>

<style scoped>
</style>
