<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create-participant" @submit.prevent="onSubmit">
        <TextInput
          class="mb-4"
          type="text"
          :required="true"
          name="name"
          v-model="form.name"
          :label="trans('users.labels.first_name')"
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
import { reactive } from 'vue';
import { trans } from '@/helpers/i18n';
import { useAuthStore } from '@/stores/auth';
import Button from '@/views/components/input/Button.vue';
import TextInput from '@/views/components/input/TextInput.vue';
import Dropdown from '@/views/components/input/Dropdown.vue';
import Alert from '@/views/components/Alert.vue';
import Panel from '@/views/components/Panel.vue';
import Page from '@/views/layouts/Page.vue';
import FileInput from '@/views/components/input/FileInput.vue';
import ParticipantService from "@/services/ParticipantService";
import { clearObject, reduceProperties } from '@/helpers/data';
import { toUrl } from '@/helpers/routing';
import Form from '@/views/components/Form.vue';

const { user } = useAuthStore();
const form = reactive({
  name: '',
});

const page = reactive({
  id: 'create_participants',
  title: trans('global.pages.participants_create'),
  filters: false,
  breadcrumbs: [
    {
      name: trans('global.pages.participants'),
      to: toUrl('/participants'),
    },
    {
      name: trans('global.pages.participants_create'),
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
      name: trans('global.buttons.save'),
      icon: 'fa fa-save',
      type: 'submit',
    },
  ],
});

const service = new ParticipantService();

const onAction = (data: any) => {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
};

const onSubmit = () => {
  service
    .handleCreate('create-participant', reduceProperties(form, 'roles', 'id'))
    .then(() => {
      clearObject(form);
    });
  return false;
};
</script>

<style scoped>
</style>
