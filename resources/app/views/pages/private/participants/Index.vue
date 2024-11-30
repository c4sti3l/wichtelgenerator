<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

    <template #default>
      <Table
          :id="page.id"
          v-if="table"
          :headers="table.headers"
          :sorting="table.sorting"
          :actions="table.actions"
          :records="table.records"
          :pagination="table.pagination"
          :is-loading="table.loading"
          @page-changed="onTablePageChange"
          @action="onTableAction"
          @sort="onTableSort"
      >
        <template v-slot:content-id="props">
          <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10">
              <img v-if="props.item.avatar_thumb_url" :src="props.item.avatar_thumb_url" class="h-10 w-10 rounded-full" alt=""/>
              <Avatar v-else class="w-10 h-10 text-gray-400 rounded-full"/>
            </div>
            <div class="ml-4">
              <div class="text-sm font-medium text-gray-900">{{ props.item.full_name }}</div>
              <div class="text-sm text-gray-500">{{ trans('users.labels.id') + ': ' + props.item.id }}</div>
            </div>
          </div>
        </template>
        <template v-slot:content-status="props">
          <span
              v-if="props.item.email_verified_at"
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
              v-html="trans('users.status.verified')"
          ></span>
          <span
              v-else
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
              v-html="trans('users.status.not_verified')"
          ></span>
        </template>
        <template v-slot:content-role="props">
          {{ props.item.roles.map((entry) => entry.title).join(', ') }}
        </template>
      </Table>
      <iframe id="hiddenIframe" style="display:none;"></iframe>
    </template>

  </Page>
</template>

<script lang="ts" setup>
import {reactive, ref, onMounted, watch} from 'vue';
import {trans} from '@/helpers/i18n';
import {useAlertStore} from '@/stores';
import {getResponseError, prepareQuery} from '@/helpers/api';
import {toUrl} from '@/helpers/routing';
import ParticipantService from "@/services/ParticipantService";
import alertHelpers from '@/helpers/alert';
import Page from '@/views/layouts/Page.vue';
import Table from '@/views/components/Table.vue';
import Avatar from '@/views/components/icons/Avatar.vue';

const service = new ParticipantService();
const alertStore = useAlertStore();

const mainQuery = reactive({
  page: 1,
  search: '',
  sort: {column: 'name', direction: 'asc'},
  filters: {
    first_name: {value: '', comparison: '='},
    last_name: {value: '', comparison: '='},
    role: {value: '', comparison: '='},
    email: {value: '', comparison: '='},
  },
});

const page = reactive({
  id: 'list_participants',
  title: trans('global.pages.participants'),
  breadcrumbs: [
    {name: trans('global.pages.participants'), to: toUrl('/participants'), active: true},
  ],
  actions: [
    {id: 'reset', name: trans('global.buttons.reset'), icon: 'fa fa-sync'},
    {id: 'new', name: trans('global.buttons.add_new'), icon: 'fa fa-plus', to: toUrl('/participants/create')},
  ],
  toggleFilters: false,
});

const table = reactive({
  headers: {
    name: trans('users.labels.first_name'),
    phone: trans('participants.labels.phone'),
    count: trans('participants.labels.count')
  },
  sorting: {name: true, last_name: true},
  pagination: {meta: null, links: null},
  actions: {
    edit: {
      id: 'edit',
      name: trans('global.actions.edit'),
      icon: 'fa fa-edit',
      showName: false,
      to: toUrl('/participants/{id}/edit')
    },
    sendMessage: {
      id: 'sendMessage',
      name: trans('global.actions.delete'),
      icon: 'fa fa-comments',
      showName: false
    },
    delete: {
      id: 'delete',
      name: trans('global.actions.delete'),
      icon: 'fa fa-trash',
      showName: false,
      danger: true
    }
  },
  loading: false,
  records: null,
});

const fetchPage = (params: typeof mainQuery) => {
  table.records = [];
  table.loading = true;
  const query = prepareQuery(params);
  service
      .index(query)
      .then((response) => {
        table.records = response.data.data;
        table.pagination.meta = response.data.meta;
        table.pagination.links = response.data.links;
        table.loading = false;
      })
      .catch((error) => {
        alertStore.error(getResponseError(error));
        table.loading = false;
      });
};

const onTableSort = (params: string) => (mainQuery.sort = params);
const onTablePageChange = (page: number) => (mainQuery.page = page);
const onTableAction = (params: any) => {
  if (params.action.id === 'delete') {
    alertHelpers.confirmDanger(() => {
      service.delete(params.item.id).then(() => fetchPage(mainQuery));
    });
  }
  if (params.action.id === 'sendMessage') {
    service.sendMessage(params.item)
  }

};
const onPageAction = (params: any) => {
  if (params.action.id === 'filters') page.toggleFilters = !page.toggleFilters;
  if(params.action.id ==='reset'){
    alertHelpers.confirmDanger(() => {
      service.reset().then(() => fetchPage(mainQuery));
    });
  }
};
const onFiltersClear = () => {
  Object.keys(mainQuery.filters).forEach((key) => {
    mainQuery.filters[key as keyof typeof mainQuery.filters].value = '';
  });
};

watch(mainQuery, () => fetchPage(mainQuery));
onMounted(() => fetchPage(mainQuery));
</script>
