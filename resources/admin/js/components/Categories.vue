<template>
  <div class="container">
    <div class="row row-cards row-deck">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t("menu.list-of-categories") }}</h3>
            <button
              @click="refresh()"
              type="button"
              :alt="$t('actions.refresh')"
              class="btn btn-default ml-2"
            >
              <i class="fe fe-refresh-cw"></i>
            </button>

            <div class="card-options">
              <form @submit.prevent="filter">
                <div class="input-group">
                  <input
                    class="form-control form-control-sm"
                    v-model="search.text"
                    type="text"
                    v-bind:placeholder="$t('labels.search')"
                  />

                  <span class="input-group-btn ml-2">
                    <button
                      v-on:click="create()"
                      type="button"
                      class="btn btn-sm btn-default"
                    >
                      {{ $t("actions.create") }}
                    </button>
                  </span>

                  <span class="input-group-btn ml-2">
                    <button
                      @click="reset()"
                      type="button"
                      class="btn btn-sm btn-default"
                    >
                      {{ $t("actions.reset") }}
                    </button>
                  </span>
                </div>
              </form>
            </div>
          </div>

          <div class="table-responsive">
            <div class="dimmer" :class="{ active: loading }">
              <div class="loader" v-show="loading"></div>
              <div
                class="dimmer-content"
                :class="{ 'min-height-300': loading }"
              >
                <table
                  v-if="list.data.length > 0"
                  class="table card-table table-vcenter text-nowrap"
                >
                  <thead>
                    <tr>
                      <table-header-column
                        :title="$t('labels.id')"
                        :order="order"
                        :field="'id'"
                        :currentField="field"
                        @sort="handleSort"
                      ></table-header-column>

                      <table-header-column
                        :title="$t('labels.title')"
                        :order="order"
                        :field="'title'"
                        :currentField="field"
                        @sort="handleSort"
                      ></table-header-column>

                      <th>{{ $t("labels.type") }}</th>

                      <th>{{ $t("labels.catalog") }}</th>

                      <table-header-column
                        :title="$t('labels.created-at')"
                        :order="order"
                        :field="'created_at'"
                        :currentField="field"
                        @sort="handleSort"
                      ></table-header-column>

                      <th>{{ $t("labels.actions") }}</th>
                    </tr>
                  </thead>

                  <tbody>
                    <tr v-for="(record, index) in list.data" :key="index">
                      <td>{{ record.id }}</td>
                      <td>{{ record.title }}</td>
                      <td>{{ record.type ? record.type.title : "" }}</td>
                      <td>{{ record.catalog ? record.catalog.title : "" }}</td>
                      <td>{{ record.created_at }}</td>

                      <td>
                        <button
                          v-show="record.published"
                          class="btn btn-success"
                          @click="show(record)"
                        >
                          <i class="fe fe-eye"></i>
                        </button>

                        <button
                          v-show="!record.published"
                          class="btn btn-warning"
                          @click="show(record)"
                        >
                          <i class="fe fe-eye-off"></i>
                        </button>

                        <button
                          @click="edit(record)"
                          type="button"
                          :alt="$t('actions.edit')"
                          class="btn btn-primary"
                        >
                          <i class="fe fe-edit"></i>
                        </button>

                        <button
                          @click="remove(record, index)"
                          type="button"
                          :alt="$t('actions.remove')"
                          class="btn btn-danger"
                        >
                          <i class="fe fe-trash-2"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <p
                  v-if="list.data.length === 0 && loading === false"
                  class="empty"
                >
                  {{ $t("labels.empty") }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <pagination :pagination="list" @paginate="getList()" :offset="4">
        </pagination>
      </div>
    </div>

    <modal v-if="modal" @close="modal = false">
      <template slot="header">
        <template v-if="!record.id">
          {{ $t("title.create-record") }}
        </template>
        <template v-if="record.id">
          {{ $t("title.edit-record") }} {{ record.title }}
        </template>
      </template>

      <form>
        <!-- Set user -->
        <div class="form-group">
          <label class="form-label">{{ $t("labels.user") }}</label>

          <model-list-select
            v-model="record.userID"
            :list="users"
            option-value="id"
            option-text="name"
            :placeholder="$t('labels.select-user')"
          />

          <div v-if="errors.userID" class="invalid-feedback">
            {{ errors.userID.join(", ") }}
          </div>
        </div>
        <!-- END Set user -->

        <!-- Set catalog -->
        <div class="form-group">
          <label class="form-label">{{ $t("labels.catalog") }}</label>

          <model-list-select
            v-model="record.catalogID"
            :list="catalog"
            option-value="id"
            option-text="title"
            :placeholder="$t('labels.select-catalog')"
          />

          <div v-if="errors.catalogID" class="invalid-feedback">
            {{ errors.catalogID.join(", ") }}
          </div>
        </div>
        <!-- END Set catalog -->

        <!-- Set type -->
        <div class="form-group">
          <label class="form-label">{{ $t("labels.type") }}</label>

          <model-list-select
            v-model="record.typeID"
            :list="types"
            option-value="id"
            option-text="title"
            :placeholder="$t('labels.type')"
          />

          <div v-if="errors.typeID" class="invalid-feedback">
            {{ errors.typeID.join(", ") }}
          </div>
        </div>
        <!-- END Set type -->

        <div class="form-group">
          <label
            >{{ $t("labels.title") }} <span class="required">*</span></label
          >
          <input
            type="text"
            class="form-control"
            name="title"
            v-model="record.title"
            :class="{ 'is-invalid': errors.title }"
            :placeholder="$t('labels.title')"
          />
          <div v-if="errors.title" class="invalid-feedback">
            {{ errors.title.join(", ") }}
          </div>
        </div>

        <div class="form-group">
          <div class="form-label">{{ $t("labels.published") }}</div>
          <div class="custom-switches-stacked">
            <label class="custom-switch">
              <input
                type="checkbox"
                name="published"
                v-model="record.published"
                value="1"
                class="custom-switch-input"
              />
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description" v-if="record.published">{{
                $t("labels.enable")
              }}</span>
              <span
                class="custom-switch-description"
                v-if="!record.published"
                >{{ $t("labels.disable") }}</span
              >
            </label>
          </div>
        </div>

        <div class="form-group">
          <label>SEO title</label>
          <input type="text" class="form-control" v-model="record.seo_title" placeholder="SEO title" />
        </div>

        <div class="form-group">
          <label>SEO description</label>
          <textarea rows="4" class="form-control" v-model="record.seo_description" placeholder="SEO description"></textarea>
        </div>

        <div class="form-group">
          <label>Canonical URL</label>
          <input type="text" class="form-control" v-model="record.canonical_url" placeholder="https://metrnametr.com.ua/catalog?categories[]=..." />
        </div>

        <div class="form-group">
          <label>FAQ JSON</label>
          <textarea rows="8" class="form-control" v-model="record.faq" placeholder='[{"question":"Питання","answer":"Відповідь"}]'></textarea>
        </div>
      </form>

      <template slot="footer">
        <template v-if="!record.id">
          <button
            class="btn btn-primary"
            :disabled="loading"
            type="button"
            @click="store()"
          >
            {{ $t("actions.save") }}
          </button>
        </template>

        <template v-if="record.id">
          <button
            class="btn btn-primary"
            :disabled="loading"
            type="button"
            @click="update()"
          >
            {{ $t("actions.update") }}
          </button>
        </template>

        <button
          class="btn btn-secondary btn-space"
          type="button"
          :disabled="loading"
          @click="modal = false"
        >
          {{ $t("actions.cancel") }}
        </button>
      </template>
    </modal>
  </div>
</template>

<script>
import axios from "axios";
import Modal from "./shared/Modal.vue";
import Pagination from "./shared/Pagination.vue";
import TableHeaderColumn from "./table/Header-column";
import { ModelListSelect } from "vue-search-select";
import _ from "lodash";

export default {
  components: {
    Modal,
    Pagination,
    TableHeaderColumn,
    ModelListSelect,
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
        data: [],
      },

      users: [],
      catalog: [],
      types: [],

      modal: false,

      record: {
        title: "",
        seo_title: "",
        seo_description: "",
        canonical_url: "",
        faq: "",
        userID: null,
        catalogID: null,
        published: false,
        typeID: null,
      },

      loading: false,

      search: {
        text: "",
      },

      field: "id",
      order: "DESC",
    };
  },

  created() {
    this.getList();
    this.getUsers();
    this.getCatalog();
    this.getTypes();
  },

  methods: {
    refresh() {
      this.getList();
    },

    getUsers() {
      axios
        .get("/api/users/list/array")
        .then((response) => {
          this.users = response.data;
        })
        .catch((e) => {
          this.$notify({ group: "app", type: "error", text: e.message });
        });
    },

    getCatalog() {
      axios
        .get("/api/catalog/list/array")
        .then((response) => {
          this.catalog = response.data;
        })
        .catch((e) => {
          this.$notify({ group: "app", type: "error", text: e.message });
        });
    },

    getTypes() {
      axios
        .get("/api/types/list/array")
        .then((response) => {
          this.types = response.data;
        })
        .catch((e) => {
          this.$notify({ group: "app", type: "error", text: e.message });
        });
    },

    handleSort: function (field, order) {
      this.field = field;
      this.order = order;

      this.getList();
    },

    getList() {
      this.loading = true;
      // const params = _(this.search).omitBy(_.isUndefined).omitBy(_.isNull).omitBy(_.isEmpty).value()

      const params = Object.assign(
        _(this.search)
          .omitBy(_.isUndefined)
          .omitBy(_.isNull)
          .omitBy(_.isEmpty)
          .value(),
        { page: this.list.current_page }
      );

      axios
        .get("/api/categories", {
          params: Object.assign({}, params, {
            field: this.field,
            order: this.order,
          }),
        })
        .then((response) => {
          if (response.data && Array.isArray(response.data.data)) {
            this.list = response.data;
          }
        })
        .catch((e) => {
          if (e.response?.status === 422) {
            this.errors = e.response.data.errors;
          } else {
            this.$notify({ group: "app", type: "error", text: e.message });
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

    show: function (record) {
      axios
        .put(`/api/categories/${record.id}`, { published: !record.published })
        .then(() => {
          record.published = !record.published;
        })
        .catch((e) =>
          this.$notify({
            group: "app",
            type: "error",
            text: e.message,
          })
        );
    },

    create: function () {
      this.record = {
        title: "",
        seo_title: "",
        seo_description: "",
        canonical_url: "",
        faq: "",
        userID: null,
        catalogID: null,
        published: false,
        typeID: null,
      };

      this.modal = true;
      this.errors = [];
    },

    store: function () {
      this.loading = true;

      const formData = new FormData();
      formData.append("title", this.record.title);
      formData.append("seo_title", this.record.seo_title || "");
      formData.append("seo_description", this.record.seo_description || "");
      formData.append("canonical_url", this.record.canonical_url || "");
      formData.append("faq", this.record.faq || "");

      if (this.record.userID !== null) {
        formData.append("user_id", this.record.userID);
      }

      if (this.record.typeID !== null) {
        formData.append("type_id", this.record.typeID);
      }

      formData.append("catalog_id", this.record.catalogID);
      formData.append("published", +this.record.published);

      axios
        .post("/api/categories", formData)
        .then((response) => {
          this.list.data.unshift(response.data);
          this.modal = false;

          this.$notify({
            group: "app",
            type: "success",
            text: this.$t("notifications.record-created"),
          });

          this.file = null;
          this.image = null;
        })
        .catch((e) => {
          if (e.response && e.response.status === 422) {
            this.errors = e.response.data.errors;
          } else {
            this.$notify({ group: "app", type: "error", text: e.message });
          }
        })
        .then(() => {
          this.loading = false;
        });
    },

    edit: function (record) {
      // Transform
      this.record = {
        ...record,
        userID: record.user_id,
        catalogID: record.catalog_id,
        typeID: record.type_id,
      };
      this.modal = true;
      this.errors = [];
    },

    update: function () {
      // Transform
      let form = {
        ...this.record,
        user_id: this.record.userID,
        catalog_id: this.record.catalogID,
        type_id: this.record.typeID,
      };

      this.loading = true;

      axios
        .put(`/api/categories/${form.id}`, form)
        .then((response) => {
          let i = this.list.data.map((item) => item.id).indexOf(form.id);
          this.list.data.splice(i, 1, response.data);

          this.modal = false;
          this.$notify({
            group: "app",
            type: "success",
            text: this.$t("notifications.record-updated"),
          });

          this.file = null;
          this.image = null;
        })
        .catch((e) => {
          if (e.response && e.response.status === 422) {
            this.errors = e.response.data.errors;
          } else {
            this.$notify({ group: "app", type: "error", text: e.message });
          }
        })
        .then(() => {
          this.loading = false;
        });
    },

    remove: function (record, index) {
      if (!window.confirm("Are you sure?")) {
        return false;
      }

      axios
        .delete(`/api/categories/${record.id}`)
        .then((response) => {
          this.list.data.splice(index, 1);
          this.$notify({
            group: "app",
            type: "success",
            text: this.$t("notifications.record-removed"),
          });
        })
        .catch((e) => {
          this.$notify({ group: "app", type: "error", text: e.message });
        });
    },

    reset() {
      this.search = {
        text: "",
      };

      this.getList();
      return false;
    },
  },
};
</script>
