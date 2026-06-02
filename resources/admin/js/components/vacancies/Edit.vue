<template>
  <div class="container">
    <div class="row row-cards row-deck">
      <div class="col-12">
        <div class="card">
          <div class="card-header justify-content-between">
            <h3 class="card-title">{{ $t('title.edit-record') }}</h3>
            <div>
              <button
                type="button"
                @click="update()"
                :disabled="loading"
                class="btn btn-primary ml-auto"
              >{{ $t('actions.save') }}</button>
              <button
                type="button"
                @click="cancel()"
                :disabled="loading"
                class="btn btn-secondary ml-auto"
              >{{ $t('actions.cancel') }}</button>
            </div>

          </div>

          <div class="card-body">
            <form>

              <!-- Set user -->
              <div class="form-group">
                <label class="form-label">{{ $t('labels.user') }}</label>

                <model-list-select
                  v-model="record.user_id"
                  :list="users"
                  option-value="id"
                  option-text="name"
                  :placeholder="$t('labels.select-user')"
                />

                <div
                  v-if="errors.user_id"
                  class="invalid-feedback"
                >{{ errors.user_id.join(', ') }}</div>
              </div>
              <!-- END Set user -->

              <div class="form-group">
                <label>{{ $t('labels.title') }} <span class="required">*</span></label>
                <input type="text"
                       class="form-control"
                       name="title"
                       v-model="record.title"
                       :class="{ 'is-invalid': errors.title }"
                       :placeholder="$t('labels.title')"/>
                <div v-if="errors.title"
                     class="invalid-feedback">{{ errors.title.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.alias') }} <span class="required">*</span></label>
                <input type="text"
                       class="form-control"
                       name="alias"
                       v-model="record.alias"
                       :class="{ 'is-invalid': errors.alias }"
                       :placeholder="$t('labels.alias')"/>
                <div v-if="errors.alias"
                     class="invalid-feedback">{{ errors.alias.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.salary') }}</label>
                <input type="text"
                       class="form-control"
                       name="salary"
                       v-model="record.salary"
                       :class="{ 'is-invalid': errors.salary }"
                       :placeholder="$t('labels.salary')"/>
                <div v-if="errors.salary"
                     class="invalid-feedback">{{ errors.salary.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.text') }}</label>
                <vue-editor v-model="record.text" id="product-text-editor"></vue-editor>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.contacts') }}</label>
                <input type="text"
                       class="form-control"
                       name="contacts"
                       v-model="record.contacts"
                       :class="{ 'is-invalid': errors.contacts }"
                       :placeholder="$t('labels.contacts')"/>
                <div v-if="errors.contacts"
                     class="invalid-feedback">{{ errors.contacts.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <div class="form-label">{{ $t('labels.published') }}</div>
                <div class="custom-switches-stacked">
                  <label class="custom-switch">
                    <input type="checkbox"
                           name="published"
                           v-model="record.published"
                           value="1"
                           class="custom-switch-input" />
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description" v-if="record.published">{{ $t('labels.enable') }}</span>
                    <span class="custom-switch-description" v-if="!record.published">{{ $t('labels.disable') }}</span>
                  </label>
                </div>
              </div>

            </form>
          </div>

          <div class="card-footer text-right">
            <div class="d-flex justify-content-end">
              <div>

                <button
                  type="button"
                  @click="update()"
                  :disabled="loading"
                  class="btn btn-primary ml-auto"
                >
                  {{ $t('actions.save') }}
                </button>

                <button
                  type="button"
                  @click="cancel()"
                  :disabled="loading"
                  class="btn btn-secondary ml-auto"
                >
                  {{ $t('actions.cancel') }}
                </button>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import { VueEditor } from "vue2-editor";
  import { ModelListSelect } from 'vue-search-select';

  export default {
    components: {
      VueEditor,
      ModelListSelect
    },

    data() {
      return {
        recordID: null,
        errors: [],

        record: {
          title: '',
          alias: '',
          salary: '',
          text: '',
          contacts: '',
          user_id: null,
          published: false,
        },

        loading: false,
        users: []
      };
    },

    created() {
      // Get ID
      this.recordID = this.$route.params.id;
      this.load();
      this.getUsers();
    },

    beforeRouteUpdate(to, from, next) {
      this.recordID = to.params.id;
      this.load();
      next();
    },

    methods: {
      getUsers() {
        axios
          .get('/api/users/list/array')
          .then((response) => {
            this.users = response.data;
          })
          .catch((e) => {
            this.$notify({ group: 'app', type: 'error', text: e.message });
          });
      },

      load() {
        this.loading = true;

        axios
          .get(`/api/vacancies/${this.recordID}`)
          .then(response => {
            // Transform
            this.record = {...response.data};
          })
          .catch(e => {
            if (e.response.status === 422) {
              this.errors = e.response.data.errors;
            } else {
              this.$notify({ group: 'app', type: 'error', text: e.message });
            }
          })
          .then(() => {
            this.loading = false;
          });
      },

      update() {
        // Transform
        let form = {...this.record};
        this.loading = true;

        axios
          .put(`/api/vacancies/${form.id}`, form)
          .then(response => {
            this.$notify({
              group: 'app',
              type: 'success',
              text: this.$t('notifications.record-updated')
            });

            this.$router.push({ name: 'vacancies-list' });
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

      cancel: function() {
        this.$router.push({ name: 'vacancies-list' });
      }
    }

  }
</script>
