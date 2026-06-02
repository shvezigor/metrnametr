<template>
  <div class="container">

    <div class="row row-cards row-deck">

      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('menu.list-of-orders') }}</h3>
            <button @click="refresh()"
                    type="button"
                    :alt="$t('actions.refresh')"
                    class="btn btn-default ml-2"
            ><i class="fe fe-refresh-cw"></i></button>

            <div class="card-options">

              <form @submit.prevent="filter">
                <div class="input-group">
                  <input class="form-control form-control-sm"
                         v-model="search.text"
                         type="text"
                         v-bind:placeholder="$t('labels.search')"/>

                  <span class="input-group-btn ml-2">
                    <button @click="reset()"
                            type="button"
                            class="btn btn-sm btn-default">{{ $t('actions.reset') }}</button>
                  </span>
                </div>

              </form>

            </div>
          </div>

          <div class="table-responsive">
            <div class="dimmer"
                 :class="{ 'active': loading }">
              <div class="loader"
                   v-show="loading"></div>
              <div class="dimmer-content"
                   :class="{ 'min-height-300': loading }">

                <table v-if="list.data.length > 0"
                       class="table card-table table-vcenter text-nowrap">

                  <thead>
                  <tr>
                    <table-header-column
                      :title="$t('labels.id')"
                      :order="order"
                      :field="'id'"
                      :currentField="field"
                      @sort="handleSort"></table-header-column>

                    <table-header-column
                      :title="$t('labels.name')"
                      :order="order"
                      :field="'name'"
                      :currentField="field"
                      @sort="handleSort"></table-header-column>

                    <table-header-column
                      :title="$t('labels.phone')"
                      :order="order"
                      :field="'phone'"
                      :currentField="field"
                      @sort="handleSort"></table-header-column>

                    <th>{{ $t('labels.product') }}</th>

                    <table-header-column
                      :title="$t('labels.created-at')"
                      :order="order"
                      :field="'created_at'"
                      :currentField="field"
                      @sort="handleSort"></table-header-column>

                    <th>{{ $t('labels.actions') }}</th>
                  </tr>
                  </thead>

                  <tbody>
                  <tr v-for="(record, index) in list.data"
                      :key="index">
                    <td>{{ record.id }}</td>
                    <td>{{ record.name }}</td>
                    <td>{{ record.phone }}</td>
                    <td>{{ record.product ? record.product.title : '' }}</td>
                    <td>{{ record.created_at }}</td>

                    <td>
                      <button @click="remove(record, index)"
                              type="button"
                              :alt="$t('actions.remove')"
                              class="btn btn-danger"
                      ><i class="fe fe-trash-2"></i></button>

                    </td>
                  </tr>
                  </tbody>
                </table>

                <p v-if="list.data.length === 0 && loading === false"
                   class='empty'>{{ $t('labels.empty') }}</p>
              </div>
            </div>
          </div>
        </div>

        <pagination :pagination="list"
                    @paginate="getList()"
                    :offset="4">
        </pagination>

      </div>
    </div>

  </div>
</template>

<script>
  import axios from 'axios';
  import Pagination from './shared/Pagination.vue';
  import TableHeaderColumn from './table/Header-column'
  import _ from "lodash";
  import {mapGetters} from "vuex";

  export default {
    components: {
      Pagination,
      TableHeaderColumn
    },

    data() {
      return {
        errors: [],
        list: {
          total: 0,
          per_page: 10,
          from: 1,
          to: 0,
          last_page: 0,
          current_page: 1,
          data: []
        },

        loading: false,

        search: {
          text: ''
        },

        field: 'id',
        order: 'DESC',
      };
    },

    computed: {
      ...mapGetters({
        currentUser: "currentUser"
      }),
    },

    created() {
      this.getList();
    },

    methods: {

      refresh() {
        this.getList();
      },

      handleSort: function (field, order) {
        this.field = field;
        this.order = order;

        this.getList()
      },

      getList() {
        this.loading = true;
        // const params = _(this.search).omitBy(_.isUndefined).omitBy(_.isNull).omitBy(_.isEmpty).value()

        let params = Object.assign(
          _(this.search)
            .omitBy(_.isUndefined)
            .omitBy(_.isNull)
            .omitBy(_.isEmpty)
            .value(),
          {page: this.list.current_page}
        );

        axios
          .get('/api/orders', {
            params: Object.assign(
              {},
              params,
              {
                field: this.field,
                order: this.order
              }
            )
          })
          .then(response => {
            this.list = response.data;
          })
          .catch(e => {
            if (e.response.status === 422) {
              this.errors = e.response.data.errors;
            } else {
              this.$notify({group: 'app', type: 'error', text: e.message});
            }
          })
          .then(() => {
            this.loading = false;
          });
      },

      filter() {
        this.getList();
        return false;
      },

      remove: function (record, index) {

        if (!window.confirm('Are you sure?')) {
          return false;
        }

        axios
          .delete(`/api/orders/${record.id}`)
          .then(response => {
            this.list.data.splice(index, 1);
            this.$notify({
              group: 'app',
              type: 'success',
              text: this.$t('notifications.record-removed')
            });
          })
          .catch(e => {
            this.$notify({group: 'app', type: 'error', text: e.message});
          });
      },

      reset() {
        this.search = {
          text: ''
        };

        this.getList();
        return false;
      },
    }
  };
</script>
